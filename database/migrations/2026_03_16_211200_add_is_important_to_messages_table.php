<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->boolean('is_important')->default(false)->after('content');
            $table->index(['campaign_id', 'is_important']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropIndex(['campaign_id', 'is_important']);
            $table->dropColumn('is_important');
        });
    }
};
