<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Expense;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $branchId = selected_branch_id();
        $query = Expense::with('wallet');
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->input('search') . '%');
        }

        $expenses = $query->latest()->get();
        
        $walletsQuery = Wallet::where('is_active', true);
        if ($branchId !== 'all') {
            $walletsQuery->where('branch_id', $branchId);
        }
        $wallets = $walletsQuery->get();

        return view('expenses.index', compact('expenses', 'wallets'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_id' => 'required|exists:wallets,id',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
        ]);

        $walletId = $request->input('wallet_id');
        $amount = floatval($request->input('amount'));
        $wallet = Wallet::findOrFail($walletId);

        if ($wallet->balance < $amount) {
            flash('عفواً، رصيد الخزينة المحددة لا يكفي لتغطية هذا المصروف.')->error();
            return redirect()->route('expenses.index');
        }

        DB::beginTransaction();
        try {
            // 1. Create Expense
            $expense = Expense::create([
                'branch_id' => Auth::user()->branch_id ?? 1,
                'wallet_id' => $walletId,
                'recorded_by' => Auth::id(),
                'amount' => $amount,
                'category' => $request->input('category'),
                'description' => $request->input('description'),
                'date' => now()->toDateString(),
            ]);

            // 2. Decrement wallet balance
            $wallet->decrement('balance', $amount);

            // 3. Log Wallet Transaction withdrawal
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'category' => 'expense',
                'description' => 'صرف مصروف: ' . $expense->description,
                'reference_id' => $expense->id,
                'reference_type' => 'Expense',
                'performed_by' => Auth::id(),
            ]);

            DB::commit();
            flash('تم تسجيل المصروف خصماً من الخزينة المحددة بنجاح.')->success();

        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء حفظ المصروف: ' . $e->getMessage())->error();
        }

        return redirect()->route('expenses.index');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        // For daily costs, updates are generally kept minimal; redirect to index
        return redirect()->route('expenses.index');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        DB::beginTransaction();
        try {
            $wallet = Wallet::find($expense->wallet_id);
            if ($wallet) {
                // Restore money
                $wallet->increment('balance', $expense->amount);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'credit',
                    'amount' => $expense->amount,
                    'balance_after' => $wallet->balance,
                    'category' => 'expense',
                    'description' => 'إيداع استرداد إلغاء مصروف: ' . $expense->description,
                    'reference_id' => $expense->id,
                    'reference_type' => 'ExpenseCancel',
                    'performed_by' => Auth::id(),
                ]);
            }

            $expense->delete();

            DB::commit();
            flash('تم إلغاء وحذف المصروف وإرجاع القيمة للخزينة بنجاح.')->warning();

        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء حذف المصروف: ' . $e->getMessage())->error();
        }

        return redirect()->route('expenses.index');
    }
}
