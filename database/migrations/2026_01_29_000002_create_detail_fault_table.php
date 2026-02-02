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
        Schema::create('detail_fault', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_wellness_id');
            $table->string('unit_pembangkit');
            $table->string('status_saat_ini')->nullable();
            $table->string('apa_yang_diinginkan')->nullable();
            $table->string('kendala_dari')->nullable();
            $table->string('action_dari')->nullable();
            $table->string('target_dari')->nullable();
            $table->integer('progress_hari')->nullable();
            $table->string('beberapa_metode')->nullable();
            $table->string('status_kendala')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('asset_wellness_id')
                ->references('id')
                ->on('asset_wellness')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_fault');
    }
};
