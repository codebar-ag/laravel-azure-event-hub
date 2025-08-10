<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('type');

            // Subject of the event (model class and id) for model events
            $table->string('subject_type')->nullable();
            $table->unsignedInteger('subject_id')->nullable();

            // Auth context
            $table->string('user_type')->nullable();
            $table->unsignedInteger('user_id')->nullable();

            // Request metadata (for HTTP events)
            $table->ipAddress('request_ip')->nullable();
            $table->string('request_method')->nullable();
            $table->text('request_url')->nullable();
            $table->string('request_route')->nullable();

            // Payloads
            $table->json('request_headers')->nullable();
            $table->json('request_data')->nullable();

            // Event data
            $table->string('event')->nullable();
            $table->json('event_data')->nullable();

            // Context
            $table->json('context')->nullable();

            $table->dateTime('synced_at')->nullable();
            $table->dateTime('sync_failed_at')->nullable();

            $table->timestamps();

            $table->index(['uuid']);
            $table->index(['type']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'user_type']);
            $table->index(['event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
