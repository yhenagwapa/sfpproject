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
        Schema::create('milk_feedings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('target');
            $table->decimal('allocation', 12, 2)->nullable();
            $table->string('school_year');
            $table->string('status');
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
        Schema::dropIfExists('milk_feedings');
    }
};
