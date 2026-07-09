<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Models\StockTransaction;
use App\Observers\StockTransactionObserver;
use App\Models\OrderItem;
use App\Observers\OrderItemsObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        StockTransaction::observe(StockTransactionObserver::class);
        OrderItem::observe(OrderItemsObserver::class);
    }
}
