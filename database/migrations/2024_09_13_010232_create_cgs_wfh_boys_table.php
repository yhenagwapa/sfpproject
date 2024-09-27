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
        Schema::create('cgs_wfh_boys', function (Blueprint $table) {
            $table->id();
            $table->double(column: 'length_in_cm')->nullable();
            $table->double('severly_wasted')->nullable();
            $table->double('wasted_from')->nullable();
            $table->double('wasted_to')->nullable();
            $table->double('normal_from')->nullable();
            $table->double('normal_to')->nullable();
            $table->double('overweight_from')->nullable();
            $table->double('overweight_to')->nullable();
            $table->double('obese')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cgs_wfh_boys');
    }
};
