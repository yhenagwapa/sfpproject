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
        Schema::create('undernourished_upon_entry_reports', function (Blueprint $table) {
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

        Schema::create('undernourished_upon_entry_report_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('undernourished_upon_entry_reports')->onDelete('cascade');
            $table->unsignedBigInteger('center_id');
            $table->string('center_name');
            $table->string('worker_name')->nullable();

            // Age bracket counts
            $table->integer('age_2_male')->default(0);
            $table->integer('age_2_female')->default(0);
            $table->integer('age_3_male')->default(0);
            $table->integer('age_3_female')->default(0);
            $table->integer('age_4_male')->default(0);
            $table->integer('age_4_female')->default(0);
            $table->integer('age_5_male')->default(0);
            $table->integer('age_5_female')->default(0);

            // Beneficiary profile counts
            $table->integer('indigenous_male')->default(0);
            $table->integer('indigenous_female')->default(0);
            $table->integer('pantawid_male')->default(0);
            $table->integer('pantawid_female')->default(0);
            $table->integer('pwd_male')->default(0);
            $table->integer('pwd_female')->default(0);
            $table->integer('lactose_intolerant_male')->default(0);
            $table->integer('lactose_intolerant_female')->default(0);
            $table->integer('solo_parent_male')->default(0);
            $table->integer('solo_parent_female')->default(0);

            // Deworming & Vitamin A counts
            $table->integer('dewormed_male')->default(0);
            $table->integer('dewormed_female')->default(0);
            $table->integer('vitamin_a_male')->default(0);
            $table->integer('vitamin_a_female')->default(0);

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
        Schema::dropIfExists('undernourished_upon_entry_report_data');
        Schema::dropIfExists('undernourished_upon_entry_reports');
    }
};
