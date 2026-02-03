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
        Schema::table('asset_wellness', function (Blueprint $table) {
            $table->string('inisial_mesin')->nullable()->after('kode_mesin_silm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_wellness', function (Blueprint $table) {
            $table->dropColumn('inisial_mesin');
        });
    }
};
