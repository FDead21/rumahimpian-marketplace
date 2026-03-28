<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The problem: settings is a flat key-value table with an ad-hoc
     * naming convention (eo_ prefix) to separate concerns. This breaks
     * down when you add a third vertical (e.g. car rental) and now
     * you can't query "give me all EO settings" without a LIKE 'eo_%'.
     *
     * Solution: add a 'group' column so settings are queryable by context.
     *
     * Groups:
     *   GLOBAL      — site name, favicon, footer, social links shared across all modules
     *   PROPERTY    — property module specific (hero text, banners, feature toggles)
     *   EO          — event organizer module specific
     *   SYSTEM      — internal config (pagination limits, feature flags, etc.)
     *
     * Usage in code:
     *   Setting::where('group', 'EO')->pluck('value', 'key')
     *   Setting::where('group', 'GLOBAL')->pluck('value', 'key')
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->enum('group', ['GLOBAL', 'PROPERTY', 'EO', 'SYSTEM'])
                  ->default('GLOBAL')
                  ->after('key');
        });

        // Migrate existing keys into their correct groups

        // EO-specific settings (previously using eo_ prefix)
        DB::table('settings')
            ->where('key', 'like', 'eo_%')
            ->update(['group' => 'EO']);

        // Property-specific settings — adjust these keys to match what you actually have
        DB::table('settings')
            ->whereIn('key', [
                'property_hero_title',
                'property_hero_subtitle',
                'property_hero_banner',
                'property_contact_phone',
                'property_contact_email',
                'property_contact_address',
            ])
            ->update(['group' => 'PROPERTY']);

        // System-level internal config
        DB::table('settings')
            ->whereIn('key', [
                'pagination_per_page',
                'maintenance_mode',
            ])
            ->update(['group' => 'SYSTEM']);

        // Everything else stays as GLOBAL (site_name, favicon, footer_text,
        // social_facebook, social_instagram, etc.)
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
