<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order with items.
     */
    public function createOrder(array $data, array $items, int $userId)
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax = 0; // Set tax to 0 for now as per frontend expectations
            $total = $subtotal + $tax;
            $balanceDue = $total - $data['advance_paid'];

            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'user_id' => $userId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'advance_paid' => $data['advance_paid'],
                'balance_due' => $balanceDue,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Update an existing order.
     */
    public function updateOrder(Order $order, array $data, array $items)
    {
        return DB::transaction(function () use ($order, $data, $items) {
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax = 0; // Set tax to 0 for now
            $total = $subtotal + $tax;
            $balanceDue = $total - $data['advance_paid'];

            $order->update([
                'customer_id' => $data['customer_id'],
                'status' => $data['status'] ?? $order->status,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'advance_paid' => $data['advance_paid'],
                'balance_due' => $balanceDue,
            ]);

            // Sync items: simpler to delete and recreate for this use case
            $order->items()->delete();
            foreach ($items as $item) {
                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $order;
        });
    }

    /**
     * Update order status and handle side effects.
     */
    public function updateStatus(Order $order, string $status)
    {
        return DB::transaction(function () use ($order, $status) {
            $oldStatus = $order->status;
            $order->status = $status;
            $order->save();

            // When order is delivered, update customer total_spent
            if ($status === 'delivered' && $oldStatus !== 'delivered') {
                $customer = $order->customer;
                $customer->total_spent += $order->total;
                $customer->save();
            }

            return $order;
        });
    }
}
