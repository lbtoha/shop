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
        Schema::create('notification_template_bodies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\NotificationTemplate::class)->constrained('notification_templates')->cascadeOnDelete();
            $table->enum('channel', ['email', 'sms', 'push'])->default('email');
            $table->boolean('is_active')->default(false);
            $table->text('subject')->nullable();
            $table->longText('body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_template_types');
    }
};
