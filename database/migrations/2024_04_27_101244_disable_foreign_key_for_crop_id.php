<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    protected $foreignKeyName = 'crop_land_histories_crop_id_foreign';

    public function up(): void
    {

        Schema::table('crop_land_histories', function (Blueprint $table) {
            $table->dropForeign($this->foreignKeyName);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('crop_land_histories', function (Blueprint $table) {
            $table->foreign($this->foreignKeyName);
        });
    }
};
