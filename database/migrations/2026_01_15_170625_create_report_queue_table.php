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
        Schema::create('report_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report'); // masterlist, inventory, sales, etc.
            $table->enum('status', ['pending', 'generating', 'ready', 'downloaded', 'failed'])->default('pending');
            $table->integer('cdc_id')->default(0);
            $table->string('file_path')->nullable(); // where the generated file is stored
            $table->timestamp('generated_at')->nullable(); // when report generation completed
            $table->timestamp('downloaded_at')->nullable(); // when user downloaded the report
            $table->text('error_message')->nullable(); // if generation fails
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_queue');
    }
};
