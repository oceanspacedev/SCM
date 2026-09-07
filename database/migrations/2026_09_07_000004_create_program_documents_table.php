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
        Schema::create('program_documents', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. doc-101
            $table->string('program_id');
            $table->foreign('program_id')->references('id')->on('programs')->cascadeOnDelete();
            $table->string('type'); // 'invoice', 'faktur', 'memo'
            $table->string('file_name');
            $table->string('file_size')->default('1.2 MB');
            $table->string('file_path')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_documents');
    }
};
