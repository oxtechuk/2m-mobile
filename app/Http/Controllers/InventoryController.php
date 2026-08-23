<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Branch;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $branchId = selected_branch_id();

        $query = Inventory::with(['product.category', 'branch']);
        if ($branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $stock = $query->latest()->get();
        $branches = Branch::where('id', '!=', $branchId)->get();
        $products = Product::all();

        return view('inventory.index', compact('stock', 'branches', 'products'));
    }

    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $branchId = $user->branch_id ?? 1;
        $productId = $request->input('product_id');
        $qty = intval($request->input('quantity'));

        DB::transaction(function () use ($branchId, $productId, $qty, $request, $user) {
            $inv = Inventory::firstOrCreate(
                ['branch_id' => $branchId, 'product_id' => $productId],
                ['quantity' => 0]
            );

            $oldQty = $inv->quantity;
            $inv->increment('quantity', $qty);

            InventoryMovement::create([
                'inventory_id' => $inv->id,
                'type' => $qty >= 0 ? 'adjustment_in' : 'adjustment_out',
                'quantity' => abs($qty),
                'old_quantity' => $oldQty,
                'new_quantity' => $oldQty + $qty,
                'description' => $request->input('notes') ?? 'تسوية يدوية لرصيد المخزن.',
                'created_by' => $user->id,
            ]);
        });

        flash('تمت تسوية وتحديث رصيد المخزن بنجاح.')->success();

        return redirect()->route('inventory.index');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'target_branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $sourceBranchId = $user->branch_id ?? 1;
        $targetBranchId = $request->input('target_branch_id');
        $productId = $request->input('product_id');
        $qty = intval($request->input('quantity'));

        $sourceInv = Inventory::where('branch_id', $sourceBranchId)->where('product_id', $productId)->first();

        if (!$sourceInv || $sourceInv->quantity < $qty) {
            flash('عفواً، الرصيد الحالي لا يكفي لإتمام عملية التحويل.')->error();
            return redirect()->route('inventory.index');
        }

        DB::transaction(function () use ($sourceInv, $sourceBranchId, $targetBranchId, $productId, $qty, $request, $user) {
            // Deduct from source branch
            $oldSourceQty = $sourceInv->quantity;
            $sourceInv->decrement('quantity', $qty);

            InventoryMovement::create([
                'inventory_id' => $sourceInv->id,
                'type' => 'transfer_out',
                'quantity' => $qty,
                'old_quantity' => $oldSourceQty,
                'new_quantity' => $oldSourceQty - $qty,
                'description' => 'تحويل بضائع صادر إلى الفرع: ' . $targetBranchId . '. ' . $request->input('notes'),
                'created_by' => $user->id,
            ]);

            // Add to target branch
            $targetInv = Inventory::firstOrCreate(
                ['branch_id' => $targetBranchId, 'product_id' => $productId],
                ['quantity' => 0]
            );

            $oldTargetQty = $targetInv->quantity;
            $targetInv->increment('quantity', $qty);

            InventoryMovement::create([
                'inventory_id' => $targetInv->id,
                'type' => 'transfer_in',
                'quantity' => $qty,
                'old_quantity' => $oldTargetQty,
                'new_quantity' => $oldTargetQty + $qty,
                'description' => 'تحويل بضائع وارد من الفرع: ' . $sourceBranchId . '. ' . $request->input('notes'),
                'created_by' => $user->id,
            ]);
        });

        flash('تم تحويل البضائع بنجاح.')->success();

        return redirect()->route('inventory.index');
    }
}
