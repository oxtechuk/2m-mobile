<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    // Display checkout page
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'سلة التسوق فارغة.');
        }

        // Calculate checkout financial details
        $subtotal = 0;
        $physicalCount = 0;
        
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            if ($item['type'] === 'physical') {
                $physicalCount += $item['quantity'];
            }
        }

        // Check free shipping threshold (550 EGP)
        $freeShippingThreshold = 550.00;
        $shippingFee = 0;
        
        if ($physicalCount > 0) {
            if ($subtotal >= $freeShippingThreshold) {
                $shippingFee = 0.00; // Free shipping
            } else {
                $shippingFee = 50.00; // Flat shipping fee in Egypt
            }
        }

        $total = $subtotal + $shippingFee;

        // InstaPay and Wallet details
        $instapayAddress = '2morro@instapay';
        $walletNumber = '01012345678';

        return view('storefront.checkout', compact(
            'cart',
            'subtotal',
            'shippingFee',
            'total',
            'physicalCount',
            'instapayAddress',
            'walletNumber'
        ));
    }

    // Process checkout form submission
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'السلة فارغة.');
        }

        // Form Validation
        $rules = [
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'shipping_governorate' => 'required|string|max:50',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cod,instapay,wallet',
            'notes' => 'nullable|string',
        ];

        // Proof of payment is required for InstaPay and Wallet
        if ($request->input('payment_method') === 'instapay' || $request->input('payment_method') === 'wallet') {
            $rules['payment_screenshot'] = 'required|image|max:4096'; // Max 4MB image
        }

        $request->validate($rules);

        // Calculate financials
        $subtotal = 0;
        $physicalCount = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            if ($item['type'] === 'physical') {
                $physicalCount += $item['quantity'];
            }
        }

        // Free shipping over 550 EGP
        $shippingFee = 0.00;
        if ($physicalCount > 0) {
            $shippingFee = ($subtotal >= 550.00) ? 0.00 : 50.00;
        }

        $total = $subtotal + $shippingFee;

        // Transaction block for database integrity
        DB::beginTransaction();
        try {
            // Handle guest account creation (if not logged in)
            $userId = Auth::id();
            if (!$userId) {
                // Check if user exists by email, otherwise create new
                $existingUser = User::where('email', $request->input('customer_email'))->first();
                if ($existingUser) {
                    $userId = $existingUser->id;
                } else {
                    $newUser = User::create([
                        'name' => $request->input('customer_name'),
                        'email' => $request->input('customer_email'),
                        'password' => Hash::make(Str::random(12)),
                    ]);
                    $userId = $newUser->id;
                }
            }

            // Generate unique order number
            $orderNumber = '2M-' . strtoupper(Str::random(4)) . '-' . time();

            // Handle payment proof screenshot upload
            $screenshotPath = null;
            if ($request->hasFile('payment_screenshot')) {
                $file = $request->file('payment_screenshot');
                $filename = 'proof_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $screenshotPath = $file->storeAs('payment_proofs', $filename, 'public');
            }

            // Create Order
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'customer_name' => $request->input('customer_name'),
                'customer_email' => $request->input('customer_email'),
                'customer_phone' => $request->input('customer_phone'),
                'shipping_governorate' => $request->input('shipping_governorate'),
                'shipping_city' => $request->input('shipping_city', ''),
                'shipping_address' => $request->input('shipping_address'),
                'shipping_fee' => $shippingFee,
                'subtotal' => $subtotal,
                'discount_total' => 0.00,
                'total' => $total,
                'payment_method' => $request->input('payment_method'),
                'payment_status' => $request->input('payment_method') === 'cod' ? 'pending' : 'paid', // screenshot uploaded
                'payment_screenshot' => $screenshotPath,
                'status' => 'pending',
                'notes' => $request->input('notes'),
            ]);

            // Create Order Items and decrease stock
            foreach ($cart as $id => $item) {
                $product = Product::find($id);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'type' => $item['type'],
                ]);

                // Decrease stock if physical product
                if ($product && $product->type === 'physical') {
                    $product->decrement('stock', $item['quantity']);
                }

                // If digital product, create a Download permission record
                if ($product && $product->type === 'digital') {
                    Download::create([
                        'order_id' => $order->id,
                        'user_id' => $userId,
                        'product_id' => $id,
                        'token' => Str::random(32),
                        'download_count' => 0,
                        'max_downloads' => $product->digital_download_limit ?: 5,
                        'expires_at' => $product->digital_expiry_days ? now()->addDays($product->digital_expiry_days) : now()->addDays(30),
                    ]);
                }
            }

            DB::commit();

            // Clear Cart Session
            session()->forget('cart');

            return redirect()->route('checkout.success', $order->order_number)
                ->with('success', 'تم تسجيل طلبك بنجاح وجاري مراجعته!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إتمام الطلب: ' . $e->getMessage())->withInput();
        }
    }

    // Order success page
    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->firstOrFail();
        
        // Fetch downloads generated for this order
        $downloads = Download::where('order_id', $order->id)->with('product')->get();

        return view('storefront.success', compact('order', 'downloads'));
    }
}
