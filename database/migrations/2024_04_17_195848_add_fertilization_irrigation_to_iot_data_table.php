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
        Schema::table('iot_data', function (Blueprint $table) {
            $table->enum('action_type', ['fertilization', 'irrigation'])->nullable();
            $table->timestamp('action_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iot_data', function (Blueprint $table) {
            $table->dropColumn('action_type');
            $table->dropColumn('action_time');
        });
    }
};
