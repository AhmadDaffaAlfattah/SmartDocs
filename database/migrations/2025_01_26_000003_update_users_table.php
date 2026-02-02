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
            // Add bidang if not exists
            if (!Schema::hasColumn('users', 'bidang')) {
                $table->string('bidang')->nullable()->after('name');
            }

            // Add role if not exists
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('bidang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'bidang')) {
                $table->dropColumn('bidang');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
