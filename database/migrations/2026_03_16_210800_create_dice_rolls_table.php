<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dice_rolls', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('campaign_sessions')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('message_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->string('expression', 100);
            $table->string('normalized_expression', 100);
            $table->json('dice_results');
            $table->json('modifiers')->nullable();
            $table->integer('total');
            $table->string('roll_mode', 20)->default('normal');
            $table->timestamp('rolled_at')->index();
            $table->timestamps();

            $table->index(['campaign_id', 'rolled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dice_rolls');
    }
};
