<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to Agent

            // Basic Info
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            
            // Categorization
            $table->enum('listing_type', ['SALE', 'RENT']);
            $table->enum('category', ['RESIDENTIAL', 'COMMERCIAL', 'LAND']);
            $table->string('property_type'); // e.g. "House", "Ruko"

            // Location
            $table->string('city');
            $table->string('district');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Standard Filters (Indexed automatically by DB usually, or add ->index())
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('land_area')->nullable();
            $table->integer('building_area')->nullable();

            // The "Hybrid" JSON Column
            $table->json('specifications')->nullable(); 

            $table->enum('status', ['DRAFT', 'PUBLISHED', 'SOLD', 'ARCHIVED'])->default('DRAFT');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['price', 'city', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
