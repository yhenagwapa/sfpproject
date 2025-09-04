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
        Schema::table('child_centers', function (Blueprint $table) {
            $table->dropForeign(['milk_feeding_id']);
            $table->dropColumn('milk_feeding_id');
            $table->dropColumn('status');

            $table->string('action_type')->after('implementation_id');
            $table->date('action_date')->nullable()->after('action_type');
            $table->foreignId('created_by_user_id')->after('funded')->nullable()->constrained('users');
            $table->foreignId('updated_by_user_id')->after('created_by_user_id')->nullable()->constrained('users');
        });

        Schema::rename('child_centers', 'child_records');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('child_records', 'child_centers');

        Schema::table('child_centers', function (Blueprint $table) {
            $table->dropForeign(['updated_by_user_id']);
            $table->dropColumn('updated_by_user_id');
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn('created_by_user_id');
            $table->dropColumn('action_date');
            $table->dropColumn('action_type');

            $table->string('status')->default("active");
            $table->foreignId('milk_feeding_id')->nullable()->constrained('implementations');
            $table->foreignId('child_development_center_id')->after('child_id')->nullable()->constrained('child_development_centers');
        });


    }
};
