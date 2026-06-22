<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add SSLCommerz online-payment tracking fields. COD orders leave these null.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // SSLCommerz validation id returned on a successful transaction.
            $table->string('transaction_id')->nullable()->after('payment_status');
            // Bank/gateway transaction id (bank_tran_id) for reconciliation.
            $table->string('gateway_transaction_id')->nullable()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'gateway_transaction_id']);
        });
    }
};
