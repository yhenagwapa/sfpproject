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
        Schema::table('nutritional_statuses', function (Blueprint $table) {
            $table->boolean('entry_is_malnourish')->default(false)->after('entry_height_for_age');
            $table->boolean('entry_is_undernourish')->default(false)->after('entry_is_malnourish');
            $table->boolean('exit_is_malnourish')->default(false)->after('exit_height_for_age');
            $table->boolean('exit_is_undernourish')->default(false)->after('exit_is_malnourish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutritional_statuses', function (Blueprint $table) {
            $table->dropColumn('entry_is_malnourish');
            $table->dropColumn('entry_is_undernourish');
            $table->dropColumn('exit_is_malnourish');
            $table->dropColumn('exit_is_undernourish');
        });
    }
};
