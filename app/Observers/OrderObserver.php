<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;

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
    }

    public function deleted(Order $order): void {}
    public function restored(Order $order): void {}
    public function forceDeleted(Order $order): void {}
}
