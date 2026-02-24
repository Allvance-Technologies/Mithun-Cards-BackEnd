<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\V1\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = Order::with(['customer', 'items'])->latest()->paginate(10);
        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
            'discount' => 'sometimes|numeric',
            'advance_paid' => 'required|numeric|min:0',
            'payment_method' => 'sometimes|string|nullable',
            'status' => 'sometimes|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = $this->orderService->createOrder(
            $validated,
            $validated['items'],
            $request->user()->id
        );

        return response()->json([
            'status' => true,
            'message' => 'Order created successfully',
            'data' => new OrderResource($order->load(['customer', 'items']))
        ], 201);
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load(['customer', 'items']));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'subtotal' => 'sometimes|numeric',
            'tax' => 'sometimes|numeric',
            'total' => 'sometimes|numeric',
            'discount' => 'sometimes|numeric',
            'advance_paid' => 'required|numeric|min:0',
            'payment_method' => 'sometimes|string|nullable',
            'status' => 'sometimes|string|in:pending,design,printing,ready,delivered,paid',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $updatedOrder = $this->orderService->updateOrder(
            $order,
            $validated,
            $validated['items']
        );

        return response()->json([
            'status' => true,
            'message' => 'Order updated successfully',
            'data' => new OrderResource($updatedOrder->load(['customer', 'items']))
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,design,printing,ready,delivered',
        ]);

        $updatedOrder = $this->orderService->updateStatus($order, $request->status);

        return response()->json([
            'status' => true,
            'message' => 'Order status updated',
            'data' => new OrderResource($updatedOrder->load(['customer', 'items']))
        ]);
    }
}
