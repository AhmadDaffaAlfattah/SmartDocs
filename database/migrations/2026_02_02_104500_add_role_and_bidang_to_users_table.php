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
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user'); // super_admin, admin, user
            }
            if (!Schema::hasColumn('users', 'bidang')) {
                $table->string('bidang')->nullable(); // engineering, operasi, pemeliharaan, business_support, keamanan, lingkungan, mesin
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'bidang']);
        });
    }
};
