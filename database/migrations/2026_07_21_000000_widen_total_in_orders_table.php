<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // decimal(15, 2) allows up to 13 digits before the decimal point
            // (max value ~9,999,999,999,999.99), plenty of headroom for large orders.
            $table->decimal('total', 15, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total', 10, 2)->default(0)->change();
        });
    }
};
