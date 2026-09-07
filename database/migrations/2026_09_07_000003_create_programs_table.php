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
        Schema::create('programs', function (Blueprint $table) {
            $table->string('id')->primary(); // e.g. PRG-001
            $table->string('title');
            $table->string('supplier');
            $table->string('category')->default('Lainnya');
            $table->string('invoice_no')->nullable();
            $table->decimal('dpp_amount', 18, 2)->default(0);
            $table->decimal('ppn_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('status')->default('Perlu Tindakan'); // 'Lengkap', 'Perlu Tindakan'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
