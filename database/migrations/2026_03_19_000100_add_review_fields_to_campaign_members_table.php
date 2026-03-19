<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_members', function (Blueprint $table): void {
            $table->text('review_message')->nullable()->after('invited_by');
            $table->timestamp('reviewed_at')->nullable()->after('review_message');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_members', function (Blueprint $table): void {
            $table->dropColumn(['review_message', 'reviewed_at']);
        });
    }
};
