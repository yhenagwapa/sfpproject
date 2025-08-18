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
        Schema::create('child_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->nullable()->constrained('children');
            $table->foreignId('implementation_id')->nullable()->constrained('implementations');
            $table->string('action_type');
            $table->date('action_date');
            $table->foreignId('center_from')->nullable()->constrained('child_development_centers');
            $table->foreignId('center_to')->nullable()->constrained('child_development_centers');
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
        Schema::dropIfExists('child_histories');
    }
};
