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
        Schema::create('nutritional_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_implementation_id')->nullable()->constrained('cycle_implementations');
            $table->foreignId('milk_feeding_id')->nullable()->constrained('milk_feedings');
            $table->foreignId('child_id')->nullable()->constrained('children'); 
            $table->decimal('weight', 8, 2);
            $table->decimal('height', 8, 2);
            $table->date('weighing_date');
            $table->integer('age_in_months');
            $table->integer('age_in_years');
            $table->string('weight_for_age')->nullable();
            $table->string('weight_for_height')->nullable();
            $table->string('height_for_age')->nullable();
            $table->boolean('is_malnourish');
            $table->boolean('is_undernourish');
            $table->unsignedBigInteger('created_by_user_id');
            $table->unsignedBigInteger('updated_by_user_id'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutritional_statuses');
    }
};
