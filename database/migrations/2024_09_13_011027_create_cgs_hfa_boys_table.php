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
        Schema::create('cgs_hfa_boys', function (Blueprint $table) {
            $table->id();
            $table->double(column: 'age_month')->nullable();
            $table->double('severely_stunted')->nullable();
            $table->double('stunted_from')->nullable();
            $table->double('stunted_to')->nullable();
            $table->double('normal_from')->nullable();
            $table->double('normal_to')->nullable();
            $table->double('tall')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cgs_hfa_boys');
    }
};
