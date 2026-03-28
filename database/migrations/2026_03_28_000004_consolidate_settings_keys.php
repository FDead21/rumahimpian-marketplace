<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The problem: EO settings duplicated contact and social keys.
     * Since branding is shared, contact_* and social_* should live
     * in GLOBAL only. EO group keeps only its banner/hero settings.
     *
     * Before:
     *   EO:     eo_contact_phone, eo_contact_email, eo_contact_address
     *   EO:     eo_social_facebook, eo_social_instagram, eo_social_twitter, eo_social_youtube
     *   EO:     eo_site_name, eo_site_description
     *   GLOBAL: site_name, hero_slides
     *
     * After:
     *   GLOBAL: site_name, site_logo, site_favicon, site_description
     *   GLOBAL: contact_phone, contact_email, contact_address
     *   GLOBAL: social_facebook, social_instagram, social_twitter, social_youtube
     *   GLOBAL: hero_slides, hero_title, hero_subtitle        ← property banner
     *   EO:     eo_hero_slides, eo_hero_title, eo_hero_subtitle ← EO banner only
     */
    public function up(): void
    {
        // 1. Migrate EO contact data into GLOBAL keys (only if GLOBAL doesn't exist yet)
        $eoContactMap = [
            'eo_contact_phone'   => 'contact_phone',
            'eo_contact_email'   => 'contact_email',
            'eo_contact_address' => 'contact_address',
        ];

        foreach ($eoContactMap as $eoKey => $globalKey) {
            $eoSetting = DB::table('settings')->where('key', $eoKey)->first();
            $globalExists = DB::table('settings')->where('key', $globalKey)->exists();

            if ($eoSetting && !$globalExists) {
                DB::table('settings')->insert([
                    'key'        => $globalKey,
                    'group'      => 'GLOBAL',
                    'value'      => $eoSetting->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Delete the EO duplicate regardless
            DB::table('settings')->where('key', $eoKey)->delete();
        }

        // 2. Migrate EO social data into GLOBAL keys
        $eoSocialMap = [
            'eo_social_facebook'  => 'social_facebook',
            'eo_social_instagram' => 'social_instagram',
            'eo_social_twitter'   => 'social_twitter',
            'eo_social_youtube'   => 'social_youtube',
        ];

        foreach ($eoSocialMap as $eoKey => $globalKey) {
            $eoSetting = DB::table('settings')->where('key', $eoKey)->first();
            $globalExists = DB::table('settings')->where('key', $globalKey)->exists();

            if ($eoSetting && !$globalExists) {
                DB::table('settings')->insert([
                    'key'        => $globalKey,
                    'group'      => 'GLOBAL',
                    'value'      => $eoSetting->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('settings')->where('key', $eoKey)->delete();
        }

        // 3. Migrate eo_site_name → site_name (GLOBAL) if not already there
        $eoSiteName = DB::table('settings')->where('key', 'eo_site_name')->first();
        $globalSiteName = DB::table('settings')->where('key', 'site_name')->exists();

        if ($eoSiteName && !$globalSiteName) {
            DB::table('settings')->insert([
                'key'        => 'site_name',
                'group'      => 'GLOBAL',
                'value'      => $eoSiteName->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::table('settings')->where('key', 'eo_site_name')->delete();

        // 4. Migrate eo_site_description → site_description (GLOBAL)
        $eoDesc = DB::table('settings')->where('key', 'eo_site_description')->first();
        $globalDesc = DB::table('settings')->where('key', 'site_description')->exists();

        if ($eoDesc && !$globalDesc) {
            DB::table('settings')->insert([
                'key'        => 'site_description',
                'group'      => 'GLOBAL',
                'value'      => $eoDesc->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::table('settings')->where('key', 'eo_site_description')->delete();

        // 5. Ensure remaining EO keys have correct group
        DB::table('settings')
            ->whereIn('key', ['eo_hero_slides', 'eo_hero_title', 'eo_hero_subtitle'])
            ->update(['group' => 'EO']);

        // 6. Ensure GLOBAL keys have correct group
        DB::table('settings')
            ->whereIn('key', [
                'site_name', 'site_logo', 'site_favicon', 'site_description',
                'contact_phone', 'contact_email', 'contact_address',
                'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube',
            ])
            ->update(['group' => 'GLOBAL']);

        // 7. Ensure PROPERTY keys have correct group
        DB::table('settings')
            ->whereIn('key', ['hero_slides', 'hero_title', 'hero_subtitle'])
            ->update(['group' => 'PROPERTY']);
    }

    public function down(): void
    {
        // Reversing this would risk data loss — not implemented intentionally.
        // Restore from backup if needed.
    }
};
