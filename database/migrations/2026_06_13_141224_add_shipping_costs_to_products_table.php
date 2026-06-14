<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('shipping_cost_dhaka', 12, 2)->default(0)->after('is_featured');
            $table->decimal('shipping_cost_outside', 12, 2)->default(0)->after('shipping_cost_dhaka');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shipping_cost_dhaka', 'shipping_cost_outside']);
        });
    }
};
