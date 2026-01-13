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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('slug')->unique();
            $table->string('logo')->nullable(); 
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();   // Agency Contact Email
            $table->timestamps();
        });

        // Add agency_id to Users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\Schema::dropIfExists('agencies');
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
};
