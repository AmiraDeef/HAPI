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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role',['landowner','farmer'])->default('landowner');
            $table->string('image')->nullable();
            $table->string('name')->nullable();
           // $table->unsignedBigInteger('farm_id')->nullable();       //cause i build farmers table
            $table->rememberToken();
            $table->timestamps();
            //$table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
