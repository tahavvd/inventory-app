<?php

namespace App\Observers;

use App\Models\StockTransaction;
use App\Enums\StockTransactionType;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class StockTransactionObserver
{
    public function created(StockTransaction $stockTransaction): void
    {
        DB::transaction(function () use ($stockTransaction) {

            if ($stockTransaction->type === StockTransactionType::In) {
                Inventory::firstOrCreate(
                    [
                        'product_id'   => $stockTransaction->product_id,
                        'warehouse_id' => $stockTransaction->warehouse_id,
                    ],
                    ['quantity' => 0]
                )->increment('quantity', $stockTransaction->quantity);
            }

            if ($stockTransaction->type === StockTransactionType::Out) {
                $inventory = Inventory::where([
                    'product_id'   => $stockTransaction->product_id,
                    'warehouse_id' => $stockTransaction->warehouse_id,
                ])
                    ->lockForUpdate()
                    ->first();

                if (! $inventory) {
                    throw new \Exception('Inventory record not found.');
                }

                if ($inventory->quantity < $stockTransaction->quantity) {
                    throw new \Exception('Insufficient stock.');
                }

                $inventory->decrement('quantity', $stockTransaction->quantity);
            }
        });
    }

    public function updated(StockTransaction $stockTransaction): void {}

    public function deleted(StockTransaction $stockTransaction): void {}

    public function restored(StockTransaction $stockTransaction): void {}

    public function forceDeleted(StockTransaction $stockTransaction): void {}
}
