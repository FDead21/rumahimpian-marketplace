<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['ADVENTURE', 'CULTURAL', 'NATURE', 'WATER_SPORTS', 'CUSTOM']);
            $table->string('custom_category')->nullable(); // used when category = CUSTOM
            $table->unsignedInteger('duration_days')->default(1);
            $table->string('duration_label')->nullable(); // e.g. "1 Day", "3D2N"
            $table->json('itinerary')->nullable(); // [{day, title, items: [{time, activity, description}]}]
            $table->string('meeting_point')->nullable();
            $table->decimal('meeting_point_lat', 10, 7)->nullable();
            $table->decimal('meeting_point_lng', 10, 7)->nullable();
            $table->unsignedInteger('min_participants')->default(1);
            $table->unsignedInteger('max_participants')->nullable();
            $table->decimal('price_per_person', 15, 2);
            $table->decimal('original_price', 15, 2)->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('inclusions')->nullable(); // same structure as packages
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('tour_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tour_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->date('tour_date');
            $table->unsignedInteger('participants');
            $table->decimal('total_price', 15, 2);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->enum('payment_status', ['UNPAID', 'DEPOSIT_PAID', 'FULLY_PAID'])->default('UNPAID');
            $table->enum('status', ['INQUIRY', 'CONFIRMED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('INQUIRY');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_bookings');
        Schema::dropIfExists('tour_media');
        Schema::dropIfExists('tours');
    }
};