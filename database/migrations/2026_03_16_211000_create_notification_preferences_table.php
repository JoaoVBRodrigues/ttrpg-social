<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();
            $table->boolean('email_sessions_enabled')->default(true);
            $table->boolean('email_invites_enabled')->default(true);
            $table->boolean('email_messages_enabled')->default(false);
            $table->boolean('in_app_sessions_enabled')->default(true);
            $table->boolean('in_app_invites_enabled')->default(true);
            $table->boolean('in_app_messages_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
