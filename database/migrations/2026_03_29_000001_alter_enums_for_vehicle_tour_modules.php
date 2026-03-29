<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN vertical ENUM('PROPERTY','EO','VEHICLE','TOUR') NULL COMMENT 'Which module this user operates in. Null for ADMIN and BUYER.'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN','AGENT','EO_AGENT','VEHICLE_RENTER','TOUR_GUIDE','BUYER') NULL DEFAULT 'BUYER'");
        DB::statement("ALTER TABLE settings MODIFY COLUMN `group` ENUM('GLOBAL','PROPERTY','EO','VEHICLE','TOUR','SYSTEM') NOT NULL DEFAULT 'GLOBAL'");
        DB::statement("ALTER TABLE articles MODIFY COLUMN category ENUM('PROPERTY','EO','VEHICLE','TOUR','GENERAL') NOT NULL DEFAULT 'PROPERTY'");
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN source ENUM('PROPERTY','EO','VEHICLE','TOUR') NOT NULL DEFAULT 'PROPERTY'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN vertical ENUM('PROPERTY','EO','CAR','HOTEL') NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN','AGENT','EO_AGENT','BUYER') NULL DEFAULT 'BUYER'");
        DB::statement("ALTER TABLE settings MODIFY COLUMN `group` ENUM('GLOBAL','PROPERTY','EO','SYSTEM') NOT NULL DEFAULT 'GLOBAL'");
        DB::statement("ALTER TABLE articles MODIFY COLUMN category ENUM('PROPERTY','EO','GENERAL') NOT NULL DEFAULT 'PROPERTY'");
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN source ENUM('PROPERTY','EO') NOT NULL DEFAULT 'PROPERTY'");
    }
};