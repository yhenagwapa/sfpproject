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
        Schema::create('cycle_implementations', function (Blueprint $table) {
            $table->id();
            $table->string('cycle_name');
            $table->integer('cycle_target');
            $table->decimal('cycle_allocation', 12, 2)->nullable();
            $table->string('cycle_school_year');
            $table->string('cycle_status');
            $table->foreignId('created_by_user_id')->constrained('users')->nullable();
            $table->foreignId('updated_by_user_id')->constrained('users')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_implementations');
    }
};
