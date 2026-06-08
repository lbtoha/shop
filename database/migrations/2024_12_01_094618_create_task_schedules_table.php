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
        Schema::create('task_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->dateTime('next_run')->nullable();
            $table->dateTime(column: 'last_run')->nullable();
            $table->string('type')->default('custom');
            $table->string('command')->nullable();
            $table->foreignIdFor(\App\Models\ScheduleTime::class)->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_schedules');
    }
};
