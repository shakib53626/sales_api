<?php

namespace App\Repositories;

use App\Models\Subject;
use App\Classes\BaseHelper as BH;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepository
{
    public function index($request)
    {
        $paginateSize = $request->input('paginate_size', null);
        $paginateSize = BH::checkPaginateSize($paginateSize);
        $searchKey    = $request->input('name', null);

        try {
            $orders = Order::with(['orderItems'])->when($searchKey, fn ($query) => $query->where("name", "like", "%$searchKey%"))
                ->orderBy('created_at', 'desc')
                ->paginate($paginateSize);

            return $orders;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function store($request)
    {
        $orderNumber = $request->input('order_number', null);
        $orderDate   = $request->input('order_date', null);
        $warehouse   = $request->input('warehouse', null);
        $remarks     = $request->input('remarks', null);
        $customer    = $request->input('customer', null);
        $cartonBonus = $request->input('carton_bonus', null);
        $type1       = $request->input('type_1', null);
        $type2       = $request->input('type_2', null);

        try {
            DB::beginTransaction();

            $order = new Order();

            $order->order_number = $orderNumber;
            $order->order_date   = $orderDate;
            $order->warehouse    = $warehouse;
            $order->remarks      = $remarks;
            $order->customer     = $customer;
            $order->carton_bonus = $cartonBonus;
            $order->type_1       = $type1;
            $order->type_2       = $type2;
            $order->save();

            DB::commit();

            return $order;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            return Order::find($id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw $exception;
        }
    }

    public function update($request, Order $order)
    {

        $orderNumber = $request->input('order_number', null);
        $orderDate   = $request->input('order_date', null);
        $warehouse   = $request->input('warehouse', null);
        $remarks     = $request->input('remarks', null);
        $customer    = $request->input('customer', null);
        $cartonBonus = $request->input('carton_bonus', null);
        $type1       = $request->input('type_1', null);
        $type2       = $request->input('type_2', null);

        try {
            DB::beginTransaction();

            $order->order_number = $orderNumber;
            $order->order_date   = $orderDate;
            $order->warehouse    = $warehouse;
            $order->remarks      = $remarks;
            $order->customer     = $customer;
            $order->carton_bonus = $cartonBonus;
            $order->type_1       = $type1;
            $order->type_2       = $type2;
            $order->save();

            foreach ($request->items as $item) {
                $orderItems[] = [
                    'order_id' => $order->id,
                    'item_id'  => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'amount'   => $item['amount'],
                ];
            }

            // Insert order details
            OrderItems::insert($orderItems);

            DB::commit();

            return $order;
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());

            throw $exception;
        }
    }
}
