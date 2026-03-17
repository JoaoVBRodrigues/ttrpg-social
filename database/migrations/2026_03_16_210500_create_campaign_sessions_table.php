<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->timestamp('starts_at')->index();
            $table->timestamp('ends_at');
            $table->string('timezone');
            $table->string('status', 20)->index();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_sessions');
    }
};
