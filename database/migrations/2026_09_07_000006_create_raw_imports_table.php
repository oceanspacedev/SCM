<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_imports', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_key')->nullable();
            $table->string('file_size')->nullable();
            $table->text('file_url')->nullable();
            $table->string('storage_type')->default('seaweedfs');
            $table->integer('imported_rows_count')->default(0);
            $table->string('uploaded_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_imports');
    }
};
