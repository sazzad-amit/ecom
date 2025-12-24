<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductOrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'customer_name'      => 'required|string|min:2|max:100',
            'customer_mobile'    => 'required|regex:/^01[3-9]\d{8}$/',
            'customer_address'   => 'required|string|min:10|max:500',
            'delivery_location'  => 'required|in:dhaka,outside-dhaka',
            'order_notes'        => 'nullable|string|max:500',
            'subtotal'           => 'required|numeric|min:0',
            'delivery_charge'    => 'required|numeric|min:0',
            'total_amount'       => 'required|numeric|min:0',
            'order_items'        => 'required|array|min:1',
            'order_items.*.product_id'   => 'nullable|integer|exists:products,id',
            'order_items.*.product_name' => 'required|string|max:200',
            'order_items.*.quantity'     => 'required|integer|min:1|max:100',
            'order_items.*.unit_price'   => 'required|numeric|min:0',
            'order_items.*.total_price'  => 'required|numeric|min:0',
        ]);

        Log::info('Order received:', $validated);

        DB::beginTransaction();

        try {
            // Create the order
            $order = Order::create([
                'customer_name'     => $validated['customer_name'],
                'customer_mobile'   => $validated['customer_mobile'],
                'customer_address'  => $validated['customer_address'],
                'delivery_location' => $validated['delivery_location'],
                'order_notes'       => $validated['order_notes'] ?? null,
                'subtotal'          => $validated['subtotal'],
                'delivery_charge'   => $validated['delivery_charge'],
                'total_amount'      => $validated['total_amount'],
                'order_status'      => 'pending',
            ]);

            // Create order items
            foreach ($validated['order_items'] as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'total_price'  => $item['total_price'],
                ]);
            }

            DB::commit();

            Log::info('Order created successfully', ['order_id' => $order->id]);

            return response()->json([
                'success'  => true,
                'message'  => 'Order placed successfully',
                'order_id' => $order->id,
                'order'    => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}