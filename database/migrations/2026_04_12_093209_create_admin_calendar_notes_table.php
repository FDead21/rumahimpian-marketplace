<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_calendar_notes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['BLOCK', 'MEMO']);
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_calendar_notes');
    }
};
