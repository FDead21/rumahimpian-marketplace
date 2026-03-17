<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add source to inquiries (PROPERTY vs EO)
        Schema::table('inquiries', function (Blueprint $table) {
            $table->enum('source', ['PROPERTY', 'EO'])->default('PROPERTY')->after('status');
        });

        // 2. Add category to articles (PROPERTY vs EO vs GENERAL)
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('category', ['PROPERTY', 'EO', 'GENERAL'])->default('PROPERTY')->after('is_published');
        });

        // 3. Add EO_AGENT role to users
        DB::statement("ALTER TABLE users MODIFY role ENUM('ADMIN', 'AGENT', 'EO_AGENT', 'BUYER') DEFAULT 'BUYER'");

        // 4. Seed default EO settings (eo_* prefix = EO website settings)
        $eoSettings = [
            'eo_site_name'        => 'Wedding & Event Organizer',
            'eo_site_description' => 'Your trusted event organizer.',
            'eo_contact_phone'    => '',
            'eo_contact_email'    => '',
            'eo_contact_address'  => '',
            'eo_hero_title'       => 'Your Dream Event, Made Real',
            'eo_hero_subtitle'    => 'Professional event organizer for weddings, corporate, and more.',
            'eo_social_facebook'  => '',
            'eo_social_instagram' => '',
        ];

        foreach ($eoSettings as $key => $value) {
            DB::table('settings')->insertOrIgnore([
                'key'        => $key,
                'value'      => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn('source');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('ADMIN', 'AGENT', 'BUYER') DEFAULT 'BUYER'");

        DB::table('settings')->whereIn('key', [
            'eo_site_name', 'eo_site_description', 'eo_contact_phone',
            'eo_contact_email', 'eo_contact_address', 'eo_hero_title',
            'eo_hero_subtitle', 'eo_social_facebook', 'eo_social_instagram',
        ])->delete();
    }
};
