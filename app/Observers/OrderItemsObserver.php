<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use App\Enums\StockTransactionType;

class OrderItemsObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        StockTransaction::create([
            'type'       => StockTransactionType::Out,
            'product_id' => $orderItem->product_id,
            'quantity'   => $orderItem->quantity,
            'order_id'   => $orderItem->order_id,
            'user_id'    => Auth::id(),
            'warehouse_id' => $orderItem->warehouse_id,
        ]);
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        // if quantity or product or warehouse changed
        if ($orderItem->wasChanged(['quantity', 'product_id', 'warehouse_id'])) {

            // reverse the old transaction
            StockTransaction::create([
                'type'         => StockTransactionType::In,
                'product_id'   => $orderItem->getOriginal('product_id'),
                'quantity'     => $orderItem->getOriginal('quantity'),
                'order_id'     => $orderItem->order_id,
                'user_id'      => Auth::id(),
                'warehouse_id' => $orderItem->getOriginal('warehouse_id'),
            ]);

            // create the new transaction
            StockTransaction::create([
                'type'         => StockTransactionType::Out,
                'product_id'   => $orderItem->product_id,
                'quantity'     => $orderItem->quantity,
                'order_id'     => $orderItem->order_id,
                'user_id'      => Auth::id(),
                'warehouse_id' => $orderItem->warehouse_id,
            ]);
        }
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        //
    }
}
