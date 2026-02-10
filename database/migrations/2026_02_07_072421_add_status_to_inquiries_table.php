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
        Schema::table('inquiries', function (Blueprint $table) {
            $table->enum('status', ['NEW', 'CONTACTED', 'CLOSED'])->default('NEW')->after('message');
            $table->text('admin_notes')->nullable()->after('status'); // To write internal notes
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['status', 'admin_notes']);
        });
    }
};
