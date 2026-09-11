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
        Schema::table('programs', function (Blueprint $table) {
            $table->string('pph_type', 50)->default('NON_PPH')->after('total_amount');
            $table->decimal('pph_amount', 18, 2)->default(0)->after('pph_type');
            $table->string('faktur_number', 100)->nullable()->after('pph_amount');
            $table->date('faktur_date')->nullable()->after('faktur_number');
            $table->text('tax_notes')->nullable()->after('faktur_date');
            $table->boolean('is_verified')->default(false)->after('tax_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'pph_type',
                'pph_amount',
                'faktur_number',
                'faktur_date',
                'tax_notes',
                'is_verified'
            ]);
        });
    }
};
