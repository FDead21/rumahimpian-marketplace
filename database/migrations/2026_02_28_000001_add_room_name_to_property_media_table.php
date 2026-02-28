<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('property_media', function (Blueprint $table) {
            $table->string('room_name')->nullable()->after('file_type');
        });
    }
    public function down(): void {
        Schema::table('property_media', function (Blueprint $table) {
            $table->dropColumn('room_name');
        });
    }
};