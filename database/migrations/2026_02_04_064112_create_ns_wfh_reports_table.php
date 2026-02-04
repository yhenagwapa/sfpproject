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
        Schema::create('ns_wfh_reports', function (Blueprint $table) {
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

        Schema::create('ns_wfh_report_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('ns_wfh_reports')->onDelete('cascade');
            $table->unsignedBigInteger('center_id');
            $table->string('center_name');
            $table->string('worker_name')->nullable();

            // Weight-for-Height category counts by age and sex
            // Normal
            $table->integer('normal_age_2_male')->default(0);
            $table->integer('normal_age_2_female')->default(0);
            $table->integer('normal_age_3_male')->default(0);
            $table->integer('normal_age_3_female')->default(0);
            $table->integer('normal_age_4_male')->default(0);
            $table->integer('normal_age_4_female')->default(0);
            $table->integer('normal_age_5_male')->default(0);
            $table->integer('normal_age_5_female')->default(0);

            // Wasted
            $table->integer('wasted_age_2_male')->default(0);
            $table->integer('wasted_age_2_female')->default(0);
            $table->integer('wasted_age_3_male')->default(0);
            $table->integer('wasted_age_3_female')->default(0);
            $table->integer('wasted_age_4_male')->default(0);
            $table->integer('wasted_age_4_female')->default(0);
            $table->integer('wasted_age_5_male')->default(0);
            $table->integer('wasted_age_5_female')->default(0);

            // Severely Wasted
            $table->integer('severely_wasted_age_2_male')->default(0);
            $table->integer('severely_wasted_age_2_female')->default(0);
            $table->integer('severely_wasted_age_3_male')->default(0);
            $table->integer('severely_wasted_age_3_female')->default(0);
            $table->integer('severely_wasted_age_4_male')->default(0);
            $table->integer('severely_wasted_age_4_female')->default(0);
            $table->integer('severely_wasted_age_5_male')->default(0);
            $table->integer('severely_wasted_age_5_female')->default(0);

            // Overweight
            $table->integer('overweight_age_2_male')->default(0);
            $table->integer('overweight_age_2_female')->default(0);
            $table->integer('overweight_age_3_male')->default(0);
            $table->integer('overweight_age_3_female')->default(0);
            $table->integer('overweight_age_4_male')->default(0);
            $table->integer('overweight_age_4_female')->default(0);
            $table->integer('overweight_age_5_male')->default(0);
            $table->integer('overweight_age_5_female')->default(0);

            // Obese
            $table->integer('obese_age_2_male')->default(0);
            $table->integer('obese_age_2_female')->default(0);
            $table->integer('obese_age_3_male')->default(0);
            $table->integer('obese_age_3_female')->default(0);
            $table->integer('obese_age_4_male')->default(0);
            $table->integer('obese_age_4_female')->default(0);
            $table->integer('obese_age_5_male')->default(0);
            $table->integer('obese_age_5_female')->default(0);

            // Center totals
            $table->integer('total_children')->default(0);
            $table->integer('total_male')->default(0);
            $table->integer('total_female')->default(0);

            $table->timestamps();

            $table->index('report_id');
            $table->index('center_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ns_wfh_report_data');
        Schema::dropIfExists('ns_wfh_reports');
    }
};
