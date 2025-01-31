<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psgcs', function (Blueprint $table) {
            $table->id('psgc_id');
            $table->string('region_name')->nullable();
            $table->string('region_name_short')->nullable();
            $table->integer('region_psgc')->nullable();
            $table->string('province_name')->nullable();
            $table->integer('province_psgc')->nullable();
            $table->string('city_name')->nullable();
            $table->integer('city_name_psgc')->nullable();
            $table->string('brgy_name')->nullable();
            $table->integer('brgy_psgc')->nullable();
            $table->string('district')->nullable();
            $table->string('subdistrict')->nullable();
            $table->timestamps();

            $table->index('region_psgc');
            $table->unique('brgy_psgc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psgcs');
    }
};
