<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MoneyTransfer;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $branchId = selected_branch_id();
        
        $walletsQuery = Wallet::where('is_active', true);
        if ($branchId !== 'all') {
            $walletsQuery->where('branch_id', $branchId);
        }
        $wallets = $walletsQuery->get();

        $transfersQuery = MoneyTransfer::with(['fromWallet', 'toWallet', 'transferredBy']);
        if ($branchId !== 'all') {
            $transfersQuery->whereHas('fromWallet', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        $transfers = $transfersQuery->latest()->get();

        return view('transfers.index', compact('wallets', 'transfers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id' => 'required|exists:wallets,id|different:from_wallet_id',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $fromId = $request->input('from_wallet_id');
        $toId = $request->input('to_wallet_id');
        $amount = floatval($request->input('amount'));

        $fromWallet = Wallet::findOrFail($fromId);
        $toWallet = Wallet::findOrFail($toId);

        if ($fromWallet->balance < $amount) {
            flash('عفواً، رصيد الخزينة المصدر لا يكفي لإتمام عملية التحويل.')->error();
            return redirect()->route('transfers.index');
        }

        DB::beginTransaction();
        try {
            // Subtract from source
            $fromWallet->decrement('balance', $amount);
            // Add to target
            $toWallet->increment('balance', $amount);

            // Record Transfer entry
            $transfer = MoneyTransfer::create([
                'from_wallet_id' => $fromId,
                'to_wallet_id' => $toId,
                'amount' => $amount,
                'notes' => $request->input('notes') ?? 'تحويل مالي بين الخزائن يدوياً.',
                'status' => 'approved',
                'transferred_by' => Auth::id(),
            ]);

            // Ledger logs for source
            WalletTransaction::create([
                'wallet_id' => $fromWallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $fromWallet->balance,
                'category' => 'transfer',
                'description' => 'تحويل مالي صادر إلى: ' . $toWallet->name,
                'reference_id' => $transfer->id,
                'reference_type' => 'WalletTransfer',
                'performed_by' => Auth::id(),
            ]);

            // Ledger logs for target
            WalletTransaction::create([
                'wallet_id' => $toWallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $toWallet->balance,
                'category' => 'transfer',
                'description' => 'تحويل مالي وارد من: ' . $fromWallet->name,
                'reference_id' => $transfer->id,
                'reference_type' => 'WalletTransfer',
                'performed_by' => Auth::id(),
            ]);

            DB::commit();
            flash('تم تحويل الأموال بنجاح وتحديث أرصدة الخزائن.')->success();

        } catch (\Exception $e) {
            DB::rollBack();
            flash('حدث خطأ أثناء إجراء عملية التحويل المالي: ' . $e->getMessage())->error();
        }

        return redirect()->route('transfers.index');
    }

    public function approve(Request $request, $id)
    {
        // Immediate transfers are approved, keep skeleton for future extensions
        return redirect()->route('transfers.index');
    }
}
