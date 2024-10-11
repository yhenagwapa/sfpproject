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
        Schema::table('children', function (Blueprint $table) {
            $table->unsignedBigInteger('milk_feeding_id')->nullable()->after('cycle_implementation_id');
            $table->foreign('milk_feeding_id')
                  ->references('id')
                  ->on('milk_feedings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['milk_feeding_id']);
            $table->dropColumn('milk_feeding_id');
        });
    }
};
