<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('game_system_id')->constrained('game_systems')->restrictOnDelete();
            $table->string('title', 120);
            $table->string('slug')->unique();
            $table->string('synopsis', 500);
            $table->text('description')->nullable();
            $table->text('rules_summary')->nullable();
            $table->unsignedTinyInteger('max_players');
            $table->string('visibility', 20)->index();
            $table->string('status', 20)->index();
            $table->string('language', 12)->default('en');
            $table->string('timezone');
            $table->string('frequency_label', 120)->nullable();
            $table->timestamp('next_session_at')->nullable()->index();
            $table->timestamps();

            $table->index('game_system_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
