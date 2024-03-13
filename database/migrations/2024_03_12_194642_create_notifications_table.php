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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('land_id');
            $table->enum('status',['read','unread'])->default('unread');
            $table->enum('type',['new_farmer','new_iot_actions','new_detection','message']);
            $table->string('message');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('land_id')->references('unique_land_id')->on('lands')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
