<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('vehicle_type', ['CAR', 'MOTORBIKE', 'BOAT']);
            $table->string('brand')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->decimal('price_per_day', 15, 2);
            $table->string('thumbnail')->nullable();
            $table->string('city');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('max_passengers')->nullable();
            $table->json('specifications')->nullable(); // fuel, transmission, CC, etc
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('rental_vehicle_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_vehicle_media');
        Schema::dropIfExists('rental_vehicles');
    }
};