<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $suppliers = $query->withCount('purchases')->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'total_due' => Supplier::where('current_balance', '>', 0)->sum('current_balance'),
            'total_credit' => abs(Supplier::where('current_balance', '<', 0)->sum('current_balance')),
        ];

        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'opening_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $openingBalance = floatval($validated['opening_balance']);
            $supplier = Supplier::create([
                'name' => $validated['name'],
                'company_name' => $validated['company_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
                'is_active' => true,
                'notes' => $validated['notes'],
            ]);

            if ($openingBalance != 0) {
                SupplierTransaction::create([
                    'supplier_id' => $supplier->id,
                    'type' => 'opening_balance',
                    'amount' => abs($openingBalance),
                    'balance_after' => $openingBalance,
                    'notes' => 'رصيد افتتاحي عند إضافة المورد',
                    'created_by' => Auth::id() ?? 1,
                ]);
            }

            DB::commit();
            flash('تم إضافة المورد الجديد بنجاح.')->success();
            return redirect()->route('suppliers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء إضافة المورد: ' . $e->getMessage())->error();
            return back()->withInput();
        }
    }

    public function show(Supplier $supplier, Request $request)
    {
        $period = $request->input('period', 'all');
        $txQuery = $supplier->transactions()->with('creator');

        switch ($period) {
            case 'today':
                $txQuery->whereDate('created_at', \Carbon\Carbon::today());
                break;
            case 'this_week':
                $txQuery->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $txQuery->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()]);
                break;
            case 'custom':
                if ($request->filled('from_date')) {
                    $txQuery->whereDate('created_at', '>=', $request->input('from_date'));
                }
                if ($request->filled('to_date')) {
                    $txQuery->whereDate('created_at', '<=', $request->input('to_date'));
                }
                break;
            case 'all':
            default:
                break;
        }

        $transactions = $txQuery->get();
        $supplier->load(['purchases.branch', 'products']);
        $wallets = Wallet::where('is_active', true)->get();

        return view('suppliers.show', compact('supplier', 'wallets', 'transactions', 'period'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $supplier->update([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'is_active' => $request->has('is_active'),
            'notes' => $validated['notes'],
        ]);

        flash('تم تحديث بيانات المورد بنجاح.')->success();
        return redirect()->route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->count() > 0) {
            flash('لا يمكن حذف المورد لوجود فواتير توريد مرتبطة به. يمكن تعطيل الحساب بدلاً من الحذف.')->error();
            return back();
        }

        $supplier->delete();
        flash('تم حذف المورد بنجاح.')->warning();
        return redirect()->route('suppliers.index');
    }

    public function paySupplier(Request $request, Supplier $supplier)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'wallet_id' => 'required|exists:wallets,id',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $amount = floatval($request->input('amount'));
        $wallet = Wallet::findOrFail($request->input('wallet_id'));

        if ($wallet->balance < $amount) {
            flash('عفواً، رصيد الخزينة المحددة لا يكفي لعمل هذا السداد.')->error();
            return back();
        }

        DB::beginTransaction();
        try {
            // 1. Deduct supplier balance
            $newBalance = $supplier->current_balance - $amount;
            $supplier->update(['current_balance' => $newBalance]);

            // 2. Record Supplier Transaction
            $transaction = SupplierTransaction::create([
                'supplier_id' => $supplier->id,
                'type' => 'payment_out',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'payment_method' => $request->input('payment_method'),
                'notes' => $request->input('notes') ?? ('سداد مستحقات للمورد ' . $supplier->name),
                'created_by' => Auth::id() ?? 1,
            ]);

            // 3. Deduct money from Wallet
            $wallet->decrement('balance', $amount);

            // 4. Record Wallet Transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'category' => 'supplier_payment',
                'description' => 'سداد للمورد: ' . $supplier->name . ' (' . ($request->input('notes') ?? 'سداد مستحقات') . ')',
                'reference_id' => $transaction->id,
                'reference_type' => 'SupplierTransaction',
                'performed_by' => Auth::id() ?? 1,
            ]);

            DB::commit();
            flash('تم تسجيل دفعة السداد للمورد وخصم القيمة من الخزينة بنجاح.')->success();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء تنفيذ عملية السداد: ' . $e->getMessage())->error();
        }

        return redirect()->route('suppliers.show', $supplier->id);
    }
}
