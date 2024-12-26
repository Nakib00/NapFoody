<?php

namespace App\Http\Controllers\posorder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\posorder;
use App\Models\posorderList;
use App\Models\posorderListExtra;
use App\Models\ShopInfo;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;


class PosOrdeerController extends Controller
{
    //crate pos order
    public function placeOrder(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'products' => 'required|array', // Array of products in the order
                'products.*.product_id' => 'required|integer',
                'products.*.quantity' => 'required|integer',
                'payment_method' => 'required|string',
                'discount' => 'nullable|numeric',
                'customer_id' => 'nullable|integer',
            ]);

            // Generate the order ID
            $shopInfo = ShopInfo::first(); // Assuming this fetches shop information for order prefix
            $lastOrder = posorder::latest()->first();
            $orderId = $shopInfo ? substr($shopInfo->name, 0, 3) . '-' . ($lastOrder ? $lastOrder->id + 1 : 1) : 'SHOP-1';

            // Determine admin_id and stuff_id based on the user role
            $adminId = Auth::id();
            $stuffId = null;
            if (Auth::user()->is_stuff) {
                $stuffId = Staff::where('admin_id', $adminId)->first()->id;
            }

            // Default customer_id to 0, if not provided
            $customerId = $request->customer_id ?? 0;

            // Create a new order
            $order = posorder::create([
                'orders_id' => $orderId,
                'admin_id' => $adminId,
                'stuff_id' => $stuffId,
                'customer_id' => $customerId ?? 0,
                'branch_id' => $request->branch_id,
                'total_price' => $request->total_price,
                'payment_method' => $request->payment_method,
                'discount' => $request->discount ?? 0, // Default discount to 0 if not provided
            ]);

            // Create order items in posorder_lists
            foreach ($request->products as $product) {
                $orderList = posorderList::create([
                    'posorder_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'size_id' => $product['size_id'] ?? null,
                    'quantity' => $product['quantity'],
                    'price' => $product['price'], // Assuming you send price for each product
                ]);

                // Handle extras for each product
                if (isset($product['extras']) && is_array($product['extras'])) {
                    foreach ($product['extras'] as $extraId) {
                        posorderListExtra::create([
                            'posorder_id' => $order->id,
                            'product_id' => $product['product_id'],
                            'extra_id' => $extraId,
                        ]);
                    }
                }
            }

            // Return the created order
            return response()->json([
                'message' => 'Order placed successfully.',
                'order' => $order,
            ], 201);
        } catch (\Exception $e) {
            // If any exception occurs, return an error response
            return response()->json([
                'message' => 'An error occurred while placing the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
