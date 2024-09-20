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
            $table->unsignedBigInteger('cycle_implementation_id')->nullable()->after('id');

            // Add a foreign key constraint
            $table->foreign('cycle_implementation_id')
                  ->references('id')
                  ->on('cycle_implementations')
                  ->onDelete('set null'); // or 'cascade' depending on your needs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['cycle_implementation_id']);
            $table->dropColumn('cycle_implementation_id');
        });
    }
};
