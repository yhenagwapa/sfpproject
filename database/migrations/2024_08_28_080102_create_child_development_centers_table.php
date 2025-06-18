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
        Schema::create('child_development_centers', function (Blueprint $table) {
            $table->id();
            $table->string('center_name');
            $table->string('address');
            $table->unsignedBigInteger('psgc_id')->nullable();
            $table->foreign('psgc_id')
                  ->references('psgc_id')
                  ->on('psgcs');
            $table->string('center_type');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_development_centers');
    }
};
