<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tour_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_media_id')->constrained('property_media')->onDelete('cascade');
            $table->foreignId('to_media_id')->constrained('property_media')->onDelete('cascade');
            $table->float('pitch')->default(0);
            $table->float('yaw')->default(0);
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tour_hotspots');
    }
};