<?php

namespace App\Observers;

use App\Models\StockTransaction;
use App\Enums\StockTransactionType;
use App\Enums\UserRole;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
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

                $this->alertIfLowStock($inventory, $stockTransaction->product);
            }
        });
    }

    public function updated(StockTransaction $stockTransaction): void {}

    public function deleted(StockTransaction $stockTransaction): void {}

    public function restored(StockTransaction $stockTransaction): void {}

    public function forceDeleted(StockTransaction $stockTransaction): void {}

    /**
     * Notify admins (and the current user, if logged in) when a product's
     * stock at a given warehouse has dropped to or below its reorder level.
     */
    protected function alertIfLowStock(Inventory $inventory, Product $product): void
    {
        if ($inventory->quantity > $product->reorder_level) {
            return;
        }

        $isOutOfStock = $inventory->quantity <= 0;

        $title = $isOutOfStock
            ? "{$product->name} is out of stock"
            : "{$product->name} is running low";

        $body = "Only {$inventory->quantity} {$product->unit->value}(s) left at {$inventory->warehouse->name}.";

        $color = $isOutOfStock ? 'danger' : 'warning';

        // Persistent alert in the notification bell for every admin.
        Notification::make()
            ->title($title)
            ->body($body)
            ->icon('heroicon-o-exclamation-triangle')
            ->color($color)
            ->sendToDatabase(
                User::query()->where('role', UserRole::Admin)->get()
            );

        // Immediate toast for whoever triggered the transaction.
        if (Auth::check()) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($color)
                ->send();
        }
    }
}
