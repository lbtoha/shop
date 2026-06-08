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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Menu::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(model: \App\Models\MenuItem::class, column: 'parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('url')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->string('target')->default('_self');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
