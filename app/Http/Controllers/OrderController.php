<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->orders->listForUser($request->user()->id));
    }

    public function sellerOrders(Request $request): JsonResponse
    {
        return response()->json($this->orders->listForSeller($request->user()->id));
    }

    public function find(int $orderId): JsonResponse
    {
        $order = $this->orders->find($orderId);
        Gate::authorize('view', $order);

        return response()->json($order);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orders->create($request->user()->id, $request->validated('items'));

        return response()->json($order, 201);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);
        $status = OrderStatus::from($request->validated('status'));

        return response()->json($this->orders->updateStatus($order, $request->user(), $status));
    }

    public function cancel(int $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);
        Gate::authorize('cancel', $order);

        return response()->json($this->orders->cancel($order));
    }
}