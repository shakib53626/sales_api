<?php

namespace App\Http\Controllers\Admin;

use App\Classes\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequest;
use App\Http\Resources\Admin\OrderCollection;
use App\Http\Resources\Admin\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends BaseController
{
    protected $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('orders-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $orders  = $this->repository->index($request);

            $orders  = new OrderCollection($orders);

            return $this->sendResponse($orders, "Orders list");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(OrderRequest $request)
    {
        if (!$request->user()->hasPermission('orders-create')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $orders = $this->repository->store($request);

            $orders = new OrderResource($orders);

            return $this->sendResponse($orders, "Orders created successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('orders-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $orders = $this->repository->show($id);

            $orders = new OrderResource($orders);

            return $this->sendResponse($orders, 'Order single view');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(OrderRequest $request, $id)
    {
        if (!$request->user()->hasPermission('orders-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $orders = Order::find($id);
            if (!$orders) {
                return $this->sendError("Order not found", 404);
            }

            $order = $this->repository->update($request, $orders);
            if (!$order) {
                return $this->sendError("Order not found", 404);
            }

            $order = new OrderResource($order);

            return $this->sendResponse($order, "Order updated successfully");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request)
    {
        $order = Order::where('id', $request->id)->delete();

        return $this->sendResponse($order, 'Order Deleted successfully');
    }
}
