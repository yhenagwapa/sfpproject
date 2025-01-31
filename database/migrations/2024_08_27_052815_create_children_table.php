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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('extension_name')->nullable();
            $table->date('date_of_birth');
            $table->foreignId('sex_id')->nullable()->constrained('sexes');
            $table->string('address');
            $table->unsignedBigInteger('psgc_id')->nullable();
            $table->foreign('psgc_id')
                  ->references('psgc_id')
                  ->on('psgcs');
            $table->string('pantawid_details')->nullable();
            $table->string('person_with_disability_details')->nullable(); ;
            $table->boolean('is_indigenous_people');
            $table->boolean('is_child_of_soloparent');
            $table->boolean('is_lactose_intolerant');
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
        Schema::dropIfExists('children');
    }
};
