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
        Schema::table('banks', function (Blueprint $table) {
            // Drop the old simple rate
            $table->dropColumn('interest_rate'); 

            // Add Realistic KPR Fields
            $table->decimal('fixed_rate_1y', 5, 2)->nullable()->comment('Fixed rate for 1 year');
            $table->decimal('fixed_rate_3y', 5, 2)->nullable()->comment('Fixed rate for 3 years');
            $table->decimal('fixed_rate_5y', 5, 2)->nullable()->comment('Fixed rate for 5 years');
            $table->decimal('floating_rate', 5, 2)->default(11.00)->comment('Rate after fixed period');
            
            $table->integer('max_tenor')->default(20)->comment('Max years (e.g. 20)');
            $table->decimal('min_dp_percent', 5, 2)->default(10.00)->comment('Minimum DP %');
        });
    }

    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->decimal('interest_rate', 5, 2)->default(5.00);
            $table->dropColumn(['fixed_rate_1y', 'fixed_rate_3y', 'fixed_rate_5y', 'floating_rate', 'max_tenor', 'min_dp_percent']);
        });
    }
};
