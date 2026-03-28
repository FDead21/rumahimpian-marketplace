<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('city')->nullable()->after('email');
            $table->text('address')->nullable()->after('city');
            $table->string('instagram_url')->nullable()->after('logo');
            $table->string('website_url')->nullable()->after('instagram_url');
            $table->string('youtube_url')->nullable()->after('website_url');
            $table->json('features')->nullable()->after('youtube_url'); 
            $table->json('service_menu')->nullable()->after('features'); 
            $table->longText('detailed_description')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'city', 
                'address', 
                'instagram_url', 
                'website_url', 
                'youtube_url', 
                'features', 
                'detailed_description'
            ]);
        });
    }
};