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
        Schema::create('disability_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('implementation_id')->constrained('implementations')->onDelete('cascade');
            $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('total_records')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('implementation_id');
        });

        Schema::create('disability_report_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disability_report_id')->constrained('disability_reports')->onDelete('cascade');
            $table->unsignedBigInteger('child_id');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('extension_name')->nullable();
            $table->date('date_of_birth');
            $table->string('sex_name');
            $table->string('center_name');
            $table->string('person_with_disability_details')->nullable();
            $table->timestamps();

            $table->index('disability_report_id');
            $table->index('child_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disability_report_data');
        Schema::dropIfExists('disability_reports');
    }
};
