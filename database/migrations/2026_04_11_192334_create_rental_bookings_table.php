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
    Schema::create('rental_bookings', function (Blueprint $table) {
        $table->id();
        $table->string('booking_code')->unique();
        
        // Links to your existing rental_vehicles table
        $table->foreignId('rental_vehicle_id')->constrained()->cascadeOnDelete();
        
        // Client Info
        $table->string('client_name');
        $table->string('client_phone');
        $table->string('client_email')->nullable();
        
        // The crucial dates for blocking availability!
        $table->date('start_date');
        $table->date('end_date');
        
        // Financials & Status
        $table->decimal('total_price', 15, 2);
        $table->decimal('deposit_amount', 15, 2)->default(0);
        $table->string('payment_status')->default('UNPAID');
        $table->string('status')->default('INQUIRY'); 
        
        $table->text('notes')->nullable();
        $table->text('admin_notes')->nullable();
        $table->text('cancellation_reason')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_bookings');
    }
};
