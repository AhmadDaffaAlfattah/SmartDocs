<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('asset_wellness', function (Blueprint $table) {
                // Try dropping using the standard array syntax which infers the name
                $table->dropUnique(['kode_mesin']);
            });
        } catch (QueryException $e) {
            // If it fails because index doesn't exist (Error 1091), we can ignore it.
            // Otherwise re-throw.
            if ($e->errorInfo[1] != 1091) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('asset_wellness', function (Blueprint $table) {
                $table->unique('kode_mesin');
            });
        } catch (QueryException $e) {
            // Ignore if already exists
        }
    }
};
