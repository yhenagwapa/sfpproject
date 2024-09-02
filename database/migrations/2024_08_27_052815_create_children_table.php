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
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('extension_name')->nullable();
            $table->date('date_of_birth');
            $table->string('sex');
            $table->string('address');
            $table->unsignedBigInteger('psgc_id')->nullable(); 
            $table->integer('zip_code');
            $table->integer('child_development_center_id')->constrained('child_development_center')->nullable();
            $table->boolean('is_pantawid');   
            $table->string('pantawid_details')->nullable(); ;   
            $table->boolean('is_person_with_disability');    
            $table->string('person_with_disability_details')->nullable(); ;
            $table->boolean('is_indigenous_people'); 
            $table->boolean('is_child_of_soloparent'); 
            $table->boolean('is_lactose_intolerant');
            $table->date('deworming_date')->nullable();
            $table->date('vitamin_a_date')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->nullable();
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
