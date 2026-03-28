<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The problem: role is a hardcoded enum on the users table.
     * Every new vertical (Car Rental, Hotel, etc.) requires ALTER TABLE.
     * 
     * Since this is an admin-managed site (not a multi-vendor marketplace),
     * we don't need full spatie/laravel-permission complexity yet.
     *
     * Simple fix: add a nullable 'vertical' column that scopes what module
     * an AGENT or EO_AGENT belongs to. This avoids enum changes when you
     * add a new vertical — you just add a new vertical value.
     *
     * roles stay:    ADMIN | AGENT | EO_AGENT | BUYER
     * vertical adds: PROPERTY | EO | CAR | HOTEL | null (for ADMIN/BUYER)
     *
     * Later, if you need spatie, you can migrate into it cleanly from this.
     *
     * Also: the is_verified and email_verified_at were added in 2 separate
     * migrations. They're consolidated here in documentation — no schema
     * change needed, just noting the intent below.
     *
     * is_verified     = admin manually verified this agent (trust badge)
     * email_verified_at = Laravel standard email verification flow
     * These are different things. Keep both. Good design.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('vertical', ['PROPERTY', 'EO', 'CAR', 'HOTEL'])
                  ->nullable()
                  ->after('role')
                  ->comment('Which module this user operates in. Null for ADMIN and BUYER.');
        });

        // Migrate existing EO_AGENT users to have vertical = EO
        DB::table('users')
            ->where('role', 'EO_AGENT')
            ->update(['vertical' => 'EO']);

        // Migrate existing AGENT users to have vertical = PROPERTY
        DB::table('users')
            ->where('role', 'AGENT')
            ->update(['vertical' => 'PROPERTY']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('vertical');
        });
    }
};
