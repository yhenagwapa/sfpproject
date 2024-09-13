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
            $table->unsignedBigInteger('child_id'); 
            $table->decimal('entry_weight', 8, 2); // use decimal for more precise control over weight
            $table->decimal('entry_height', 8, 2); // use decimal for height as well
            $table->date('entry_actual_date_of_weighing');
            $table->string('entry_weight_for_age')->nullable(); // nullable if not required
            $table->string('entry_weight_for_height')->nullable();
            $table->string('entry_height_for_age')->nullable();
            $table->decimal('exit_weight', 8, 2)->nullable(); // use decimal for more precise control over weight
            $table->decimal('exit_height', 8, 2)->nullable(); // use decimal for height as well
            $table->date('exit_actual_date_of_weighing')->nullable();
            $table->string('exit_weight_for_age')->nullable(); // nullable if not required
            $table->string('exit_weight_for_height')->nullable();
            $table->string('exit_height_for_age')->nullable();
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
