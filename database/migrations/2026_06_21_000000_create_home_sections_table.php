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
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();           // heading shown on the storefront
            $table->string('subtitle')->nullable();         // eyebrow / small label above the heading
            $table->string('source')->default('category');  // HomeSectionSourceEnum
            $table->string('layout')->default('grid');      // HomeSectionLayoutEnum
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->json('product_ids')->nullable();        // hand-picked product list (source = products)
            $table->unsignedInteger('product_limit')->default(8);
            $table->string('view_all_url')->nullable();     // optional "View all" link override
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
