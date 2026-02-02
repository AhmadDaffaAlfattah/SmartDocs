<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_wellness', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mesin')->unique();
            $table->string('unit_pembangkit_common');
            $table->integer('total_equipment');
            $table->integer('safe')->default(0);
            $table->integer('warning')->default(0);
            $table->integer('fault')->default(0);
            $table->string('tahun');
            $table->string('bulan');
            $table->string('sentral')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_wellness');
    }
};
