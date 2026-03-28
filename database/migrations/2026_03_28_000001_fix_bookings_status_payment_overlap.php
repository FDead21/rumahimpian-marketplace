<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The problem: bookings.status had 'DEPOSIT_PAID' as a value,
     * which duplicates the meaning of payment_status entirely.
     * 
     * Rule: status = WHERE IS THIS BOOKING IN THE WORKFLOW
     *       payment_status = MONEY STATE (separate concern)
     * 
     * Old status enum: INQUIRY, CONFIRMED, DEPOSIT_PAID, IN_PROGRESS, COMPLETED, CANCELLED
     * New status enum: INQUIRY, CONFIRMED, IN_PROGRESS, COMPLETED, CANCELLED
     * 
     * Any rows with status='DEPOSIT_PAID' get migrated to status='CONFIRMED'
     * because a deposit being paid means the booking was confirmed — the payment
     * state is already captured in payment_status='DEPOSIT_PAID'.
     */
    public function up(): void
    {
        // Step 1: migrate any existing rows that use the overlapping status
        DB::table('bookings')
            ->where('status', 'DEPOSIT_PAID')
            ->update([
                'status'         => 'CONFIRMED',
                'payment_status' => 'DEPOSIT_PAID', // ensure payment_status reflects this
            ]);

        // Step 2: redefine the status enum without DEPOSIT_PAID
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status ENUM(
                'INQUIRY',
                'CONFIRMED',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED'
            ) NOT NULL DEFAULT 'INQUIRY'
        ");

        // Step 3: add a note column for when/why it was cancelled (useful for EO ops)
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('cancellation_reason')->nullable()->after('admin_notes');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
        });

        // Restore old enum with DEPOSIT_PAID
        DB::statement("
            ALTER TABLE bookings 
            MODIFY COLUMN status ENUM(
                'INQUIRY',
                'CONFIRMED',
                'DEPOSIT_PAID',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED'
            ) NOT NULL DEFAULT 'INQUIRY'
        ");
    }
};
