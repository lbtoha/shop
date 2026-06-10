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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            // Display label for the combination, e.g. "Red / L".
            $table->string('name');
            $table->string('sku')->nullable();

            // Structured options, e.g. {"Color": "Red", "Size": "L"} — drives the
            // grouped option pickers on the storefront.
            $table->json('attributes')->nullable();

            // Added to (or subtracted from) the product's base price.
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
