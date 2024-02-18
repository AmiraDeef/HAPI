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
        Schema::create('crop_land_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('land_id');
            $table->unsignedBigInteger('crop_id');
            $table->timestamp('planted_at');
            $table->timestamp('harvested_at')->nullable();
            $table->decimal('nitrogen_applied', 8, 2)->nullable();
            $table->decimal('phosphorus_applied', 8, 2)->nullable();
            $table->decimal('potassium_applied', 8, 2)->nullable();
            $table->foreign('land_id')->references('id')->on('lands')->onDelete('cascade');
            $table->foreign('crop_id')->references('id')->on('crops')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_land_history');
    }
};
