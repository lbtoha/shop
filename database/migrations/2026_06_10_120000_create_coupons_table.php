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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            // 'percent' => value is a percentage; 'fixed' => value is a flat amount.
            $table->string('type')->default('fixed');
            $table->decimal('value', 12, 2)->default(0);

            // Optional cap on the discount when type=percent.
            $table->decimal('max_discount', 12, 2)->nullable();
            // Minimum cart subtotal required to use the coupon.
            $table->decimal('min_subtotal', 12, 2)->default(0);

            // Total redemptions allowed (null = unlimited).
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->date('starts_at')->nullable();
            $table->date('expires_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
