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
        Schema::table('child_development_centers', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_focal_user_id')->nullable()->after('zip_code');
            $table->foreign('assigned_focal_user_id')
                  ->references('id')
                  ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('child_development_centers', function (Blueprint $table) {
            $table->dropForeign(['assigned_focal_user_id']);
            $table->dropColumn('assigned_focal_user_id');
        });
    }
};
