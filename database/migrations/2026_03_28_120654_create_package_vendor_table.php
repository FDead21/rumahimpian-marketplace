<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the new pivot table
        Schema::create('package_vendor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            
            // This is your requested feature!
            $table->boolean('is_mandatory')->default(false); 
            
            $table->timestamps();
        });

        // 2. Remove the old vendor_id from packages
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']); 
            $table->dropColumn('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_vendor');
        Schema::table('packages', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable();
        });
    }
};