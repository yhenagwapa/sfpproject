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
        Schema::table('implementations', function (Blueprint $table) {

            $table->dropColumn('school_year');

            $table->year('school_year_from')->after('allocation')->nullable();
            $table->year('school_year_to')->after('school_year_from')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('implementations', function (Blueprint $table) {
            // Revert the changes if needed
            $table->dropColumn(['school_year_from', 'school_year_to']);
            $table->string('school_year')->nullable();
        });
    }
};
