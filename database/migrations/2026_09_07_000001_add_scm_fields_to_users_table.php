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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('role')->default('Tim Pajak')->after('phone'); // 'Admin SCM', 'Tim Pajak', 'Staf SCM'
            $table->string('status')->default('pending')->after('role'); // 'pending', 'approved', 'rejected'
            $table->string('division')->nullable()->after('status');
            $table->string('initials', 10)->nullable()->after('division');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('initials');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'phone',
                'role',
                'status',
                'division',
                'initials',
                'approved_by',
                'approved_at'
            ]);
        });
    }
};
