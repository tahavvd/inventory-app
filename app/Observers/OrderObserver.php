<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\StockTransaction;
use App\Enums\StockTransactionType;
use App\Enums\OrderStatus;

class OrderObserver
{
    public function created(Order $order): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'user_id'  => Auth::id(),
            'status'   => $order->status,
        ]);
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id'  => Auth::id(),
                'status'   => $order->status,
            ]);
        }

        if ($order->status === OrderStatus::Cancelled && $order->wasChanged('status')) {
            foreach ($order->items as $item) {
                StockTransaction::create([
                    'type'       => StockTransactionType::In,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'order_id'   => $order->id,
                    'user_id'    => Auth::id(),
                    'warehouse_id' => $item->warehouse_id,
                ]);
            }
        }
    }

    public function deleted(Order $order): void {}
    public function restored(Order $order): void {}
    public function forceDeleted(Order $order): void {}
}
