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
        Schema::table('cgs_wfh_girls', function (Blueprint $table) {
            $table->double('length_from')->after('id')->nullable();
            $table->double('length_to')->after('length_from')->nullable();

            $table->dropColumn('length_in_cm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cgs_wfh_girls', function (Blueprint $table) {
            $table->double(column: 'length_in_cm')->nullable();

            $table->dropColumn('length_from');
            $table->dropColumn('length_to');
        });
    }
};
