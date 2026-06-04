<?php

namespace App\Models;

use App\Enums\StockTransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'warehouse_id', 'user_id', 'supplier_id', 'order_id', 'type', 'quantity', 'notes'])]
class StockTransaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => StockTransactionType::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nullable — only set on purchase/stock-in transactions.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Nullable — only set when a transaction is triggered by a sale order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
