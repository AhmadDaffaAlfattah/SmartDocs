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
        Schema::table('detail_fault', function (Blueprint $table) {
            // Drop existing columns if exists
            if (Schema::hasColumn('detail_fault', 'status_saat_ini')) {
                $table->dropColumn('status_saat_ini');
            }
            if (Schema::hasColumn('detail_fault', 'apa_yang_diinginkan')) {
                $table->dropColumn('apa_yang_diinginkan');
            }
            if (Schema::hasColumn('detail_fault', 'kendala_dari')) {
                $table->dropColumn('kendala_dari');
            }
            if (Schema::hasColumn('detail_fault', 'action_dari')) {
                $table->dropColumn('action_dari');
            }
            if (Schema::hasColumn('detail_fault', 'target_dari')) {
                $table->dropColumn('target_dari');
            }
            if (Schema::hasColumn('detail_fault', 'progress_hari')) {
                $table->dropColumn('progress_hari');
            }
            if (Schema::hasColumn('detail_fault', 'beberapa_metode')) {
                $table->dropColumn('beberapa_metode');
            }
            if (Schema::hasColumn('detail_fault', 'status_kendala')) {
                $table->dropColumn('status_kendala');
            }
        });

        Schema::table('detail_fault', function (Blueprint $table) {
            // Add new columns based on Excel structure
            $table->date('tanggal_identifikasi')->nullable()->after('unit_pembangkit');
            $table->string('status_saat_ini')->nullable()->after('tanggal_identifikasi');
            $table->string('asset_description')->nullable()->after('status_saat_ini');
            $table->string('kondisi_aset')->nullable()->after('asset_description');
            $table->string('action_plan')->nullable()->after('kondisi_aset');
            $table->date('target_selesai')->nullable()->after('action_plan');
            $table->string('progres_saat_ini')->nullable()->after('target_selesai');
            $table->date('realisasi_selesai')->nullable()->after('progres_saat_ini');
            $table->string('main_issue_kendala')->nullable()->after('realisasi_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_fault', function (Blueprint $table) {
            $table->dropColumnIfExists(['tanggal_identifikasi', 'status_saat_ini', 'asset_description', 'kondisi_aset', 'action_plan', 'target_selesai', 'progres_saat_ini', 'realisasi_selesai', 'main_issue_kendala']);
        });
    }
};
