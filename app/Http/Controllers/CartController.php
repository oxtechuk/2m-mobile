<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Add product to cart
    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $product = Product::findOrFail($productId);

        // Stock check for physical products
        if ($product->type === 'physical' && $product->stock < $quantity) {
            return back()->with('error', 'الكمية المطلوبة غير متوفرة حالياً في المخزون.');
        }

        $cart = session()->get('cart', []);

        // Calculate active price (with sale discount if applicable)
        $price = $product->sale_price !== null ? $product->sale_price : $product->price;

        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            if ($product->type === 'physical' && $product->stock < $newQuantity) {
                return back()->with('error', 'عذراً، لا يمكن إضافة المزيد من هذا المنتج لعدم كفاية المخزون.');
            }
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $price,
                'quantity' => $quantity,
                'image' => ($product->images && count($product->images) > 0) ? $product->images[0] : null,
                'type' => $product->type,
            ];
        }

        session()->put('cart', $cart);

        if ($request->has('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'تم إضافة "' . $product->name . '" إلى السلة بنجاح.');
    }

    // Update product quantity in cart
    public function update(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                if ($product->type === 'physical' && $product->stock < $quantity) {
                    return back()->with('error', 'الكمية المطلوبة غير متوفرة حالياً في المخزون.');
                }
                $cart[$productId]['quantity'] = $quantity;
            }
            session()->put('cart', $cart);
        }

        return back()->with('success', 'تم تحديث السلة بنجاح.');
    }

    // Remove product from cart
    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'تم حذف المنتج من السلة.');
    }

    // Clear entire cart
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('home')->with('success', 'تم تفريغ السلة.');
    }
}
