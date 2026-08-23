<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Models\SaleItem;
use App\Models\MoneyTransfer;
use App\Models\Wallet;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->input('view_mode', 'financial'); // 'financial', 'products', 'transfers'
        $branches = Branch::all();

        // Branch filter override or default
        $branchId = $request->input('branch_id', selected_branch_id());

        if ($viewMode === 'products') {
            // Mode 2: Sold Products Log
            $query = SaleItem::with(['sale.customer', 'sale.branch', 'sale.cashier', 'sale.user', 'product', 'serials']);

            if ($branchId && $branchId !== 'all') {
                $query->whereHas('sale', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                           ->orWhere('barcode', 'like', "%{$search}%")
                           ->orWhere('sku', 'like', "%{$search}%");
                    })
                    ->orWhereHas('sale', function ($sq) use ($search) {
                        $sq->where('invoice_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('serials', function ($sq) use ($search) {
                        $sq->where('serial_number', 'like', "%{$search}%");
                    });
                });
            }

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->input('from_date'));
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->input('to_date'));
            }

            $statsQuery = clone $query;
            $totalProductsQty = (clone $statsQuery)->sum('quantity');
            $totalProductsRevenue = (clone $statsQuery)->sum('total');
            $uniqueProductsCount = (clone $statsQuery)->distinct('product_id')->count('product_id');

            $items = $query->latest()->paginate(20);

            return view('transactions.index', compact(
                'viewMode',
                'items',
                'branches',
                'branchId',
                'totalProductsQty',
                'totalProductsRevenue',
                'uniqueProductsCount'
            ));

        } elseif ($viewMode === 'transfers') {
            // Mode 3: Money Transfers Log
            $query = MoneyTransfer::with(['fromWallet.branch', 'toWallet.branch', 'transferredBy', 'approvedBy']);

            if ($branchId && $branchId !== 'all') {
                $query->where(function ($q) use ($branchId) {
                    $q->whereHas('fromWallet', function ($fq) use ($branchId) {
                        $fq->where('branch_id', $branchId);
                    })->orWhereHas('toWallet', function ($tq) use ($branchId) {
                        $tq->where('branch_id', $branchId);
                    });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'like', "%{$search}%")
                      ->orWhereHas('transferredBy', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->input('from_date'));
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->input('to_date'));
            }

            $statsQuery = clone $query;
            $totalTransferAmount = (clone $statsQuery)->sum('amount');
            $approvedCount = (clone $statsQuery)->where('status', 'approved')->count();
            $pendingCount = (clone $statsQuery)->where('status', 'pending')->count();

            $transfers = $query->latest()->paginate(20);

            return view('transactions.index', compact(
                'viewMode',
                'transfers',
                'branches',
                'branchId',
                'totalTransferAmount',
                'approvedCount',
                'pendingCount'
            ));

        } else {
            // Mode 1: Financial Transactions (Default)
            $query = WalletTransaction::with(['wallet.branch', 'performedBy']);

            if ($branchId && $branchId !== 'all') {
                $query->whereHas('wallet', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            }

            if ($request->filled('category')) {
                $query->where('category', $request->input('category'));
            }

            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('reference_id', 'like', "%{$search}%")
                      ->orWhereHas('performedBy', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->input('from_date'));
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->input('to_date'));
            }

            $statsQuery = clone $query;
            $totalCredits = (clone $statsQuery)->where('type', 'credit')->sum('amount');
            $totalDebits = (clone $statsQuery)->where('type', 'debit')->sum('amount');
            $netBalance = $totalCredits - $totalDebits;
            $totalCount = (clone $statsQuery)->count();

            $transactions = $query->latest()->paginate(20);

            return view('transactions.index', compact(
                'viewMode',
                'transactions',
                'branches',
                'branchId',
                'totalCredits',
                'totalDebits',
                'netBalance',
                'totalCount'
            ));
        }
    }
}
