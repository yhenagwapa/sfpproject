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
        Schema::create('exit_nutritional_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id'); 
            $table->decimal('weight', 8, 2); // use decimal for more precise control over weight
            $table->decimal('height', 8, 2); // use decimal for height as well
            $table->date('actual_date_of_weighing');
            $table->string('weight_for_age')->nullable(); // nullable if not required
            $table->string('weight_for_height')->nullable();
            $table->string('height_for_age')->nullable();
            $table->unsignedBigInteger('created_by_user_id'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exit_nutritional_statuses');
    }
};
