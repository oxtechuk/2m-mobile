<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $branchId = selected_branch_id();
        $query = Wallet::with('branch');
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }
        $wallets = $query->get();

        return view('wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,vodafone_cash,instapay,bank',
            'balance' => 'required|numeric|min:0',
        ]);

        $wallet = Wallet::create([
            'name' => $request->input('name'),
            'type' => 'branch',
            'balance' => $request->input('balance'),
            'branch_id' => Auth::user()->branch_id ?? 1,
            'is_active' => true,
        ]);

        // Create initial deposit log
        if ($wallet->balance > 0) {
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $wallet->balance,
                'balance_after' => $wallet->balance,
                'category' => 'other',
                'description' => 'الرصيد الافتتاحي عند إنشاء المحفظة.',
                'performed_by' => Auth::id(),
            ]);
        }

        flash('تم إنشاء الخزينة/المحفظة بنجاح.')->success();

        return redirect()->route('wallets.index');
    }

    public function show($id)
    {
        $wallet = Wallet::findOrFail($id);
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)->latest()->take(50)->get();

        return view('wallets.show', compact('wallet', 'transactions'));
    }

    public function edit($id)
    {
        $wallet = Wallet::findOrFail($id);
        return view('wallets.edit', compact('wallet'));
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $wallet->update([
            'name' => $request->input('name'),
            'is_active' => $request->input('is_active'),
        ]);

        flash('تم تعديل بيانات الخزينة بنجاح.')->success();

        return redirect()->route('wallets.index');
    }

    public function transactions($id)
    {
        $wallet = Wallet::findOrFail($id);
        $transactions = WalletTransaction::where('wallet_id', $wallet->id)->latest()->get();

        return view('wallets.transactions', compact('wallet', 'transactions'));
    }
}
