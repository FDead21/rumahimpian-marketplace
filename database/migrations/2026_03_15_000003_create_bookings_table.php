<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();  // e.g. WO-2026-0001

            // Relations
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')           // LINK to property marketplace (optional)
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->foreignId('assigned_to')           // EO agent handling this booking
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Client Info
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();

            // Event Details
            $table->date('event_date');
            $table->integer('guest_count')->nullable();
            $table->string('event_type')->nullable();  // Wedding, Corporate, Birthday, etc.
            $table->text('notes')->nullable();         // client's special requests

            // Financials
            $table->decimal('total_price', 15, 2);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->enum('payment_status', [
                'UNPAID',
                'DEPOSIT_PAID',
                'FULLY_PAID'
            ])->default('UNPAID');

            // Booking Status
            $table->enum('status', [
                'INQUIRY',        // just submitted
                'CONFIRMED',      // EO confirmed the date
                'DEPOSIT_PAID',   // deposit received
                'IN_PROGRESS',    // event day approaching / being handled
                'COMPLETED',      // event done
                'CANCELLED'       // cancelled
            ])->default('INQUIRY');

            $table->text('admin_notes')->nullable();   // internal EO notes
            $table->timestamps();

            $table->index(['event_date', 'status']);
        });

        Schema::create('booking_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'vendor_id']); // prevent duplicate assignment
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_vendors');
        Schema::dropIfExists('bookings');
    }
};
