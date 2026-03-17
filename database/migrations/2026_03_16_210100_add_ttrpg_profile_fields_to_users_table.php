<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username')->nullable()->after('name');
            $table->text('bio')->nullable()->after('password');
            $table->string('avatar_path')->nullable()->after('bio');
            $table->string('timezone')->default('UTC')->after('avatar_path');
            $table->string('preferred_role')->default('both')->after('timezone');
            $table->json('favorite_systems')->nullable()->after('preferred_role');
            $table->json('availability')->nullable()->after('favorite_systems');
            $table->boolean('is_profile_public')->default(true)->after('availability');
            $table->boolean('is_email_public')->default(false)->after('is_profile_public');
        });

        $existing = DB::table('users')->select('id', 'name', 'email')->get();

        foreach ($existing as $user) {
            $base = Str::of($user->name ?: Str::before($user->email, '@'))
                ->lower()
                ->replaceMatches('/[^a-z0-9]+/', '')
                ->value();

            $username = $base !== '' ? $base : 'user'.$user->id;

            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username.$user->id]);
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['username']);
            $table->dropColumn([
                'username',
                'bio',
                'avatar_path',
                'timezone',
                'preferred_role',
                'favorite_systems',
                'availability',
                'is_profile_public',
                'is_email_public',
            ]);
        });
    }
};
