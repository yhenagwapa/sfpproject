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
        Schema::create('malnourished_reports', function (Blueprint $table) {
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

        Schema::create('malnourished_report_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('malnourished_report_id')->constrained('malnourished_reports')->onDelete('cascade');
            $table->unsignedBigInteger('child_id');
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('extension_name')->nullable();
            $table->date('date_of_birth');
            $table->string('sex_name');
            $table->string('center_name');

            // Entry nutritional status
            $table->date('entry_weighing_date')->nullable();
            $table->decimal('entry_weight', 6, 2)->nullable();
            $table->decimal('entry_height', 6, 2)->nullable();
            $table->integer('entry_age_months')->nullable();
            $table->integer('entry_age_years')->nullable();
            $table->string('entry_weight_for_age')->nullable();
            $table->string('entry_weight_for_height')->nullable();
            $table->string('entry_height_for_age')->nullable();

            // Exit nutritional status
            $table->date('exit_weighing_date')->nullable();
            $table->decimal('exit_weight', 6, 2)->nullable();
            $table->decimal('exit_height', 6, 2)->nullable();
            $table->integer('exit_age_months')->nullable();
            $table->integer('exit_age_years')->nullable();
            $table->string('exit_weight_for_age')->nullable();
            $table->string('exit_weight_for_height')->nullable();
            $table->string('exit_height_for_age')->nullable();

            $table->timestamps();

            $table->index('malnourished_report_id');
            $table->index('child_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('malnourished_report_data');
        Schema::dropIfExists('malnourished_reports');
    }
};
