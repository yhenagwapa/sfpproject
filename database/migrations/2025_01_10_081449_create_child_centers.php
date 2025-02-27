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
        Schema::create('child_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->nullable()->constrained('children');
            $table->foreignId('child_development_center_id')->nullable()->constrained('child_development_centers');
            $table->foreignId('implementation_id')->nullable()->constrained('implementations');
            $table->foreignId('milk_feeding_id')->nullable()->constrained('implementations');
            $table->string('status')->default("active");
            $table->boolean('funded')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_records');
    }
};
