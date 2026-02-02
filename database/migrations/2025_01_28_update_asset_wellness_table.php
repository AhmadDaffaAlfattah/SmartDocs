<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_wellness', function (Blueprint $table) {
            // Tambah kolom baru sesuai excel
            $table->string('tipe_aset')->nullable()->after('unit_pembangkit_common');
            $table->string('kode_mesin_silm')->nullable()->after('tipe_aset');
            $table->decimal('daya_terpasang', 10, 2)->nullable()->after('kode_mesin_silm');
            $table->decimal('daya_mampu_netto', 10, 2)->nullable()->after('daya_terpasang');
            $table->decimal('daya_mampu_pasok', 10, 2)->nullable()->after('daya_mampu_netto');
            $table->string('percentage_safe')->nullable()->after('safe');
            $table->string('percentage_warning')->nullable()->after('warning');
            $table->string('percentage_fault')->nullable()->after('fault');
            $table->string('warning_equipment')->nullable()->after('percentage_fault');
            $table->string('fault_equipment')->nullable()->after('warning_equipment');
            $table->string('status_operasi')->nullable()->after('fault_equipment');

            // Ubah kolom keterangan menjadi required
            $table->text('keterangan')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('asset_wellness', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_aset',
                'kode_mesin_silm',
                'daya_terpasang',
                'daya_mampu_netto',
                'daya_mampu_pasok',
                'percentage_safe',
                'percentage_warning',
                'percentage_fault',
                'warning_equipment',
                'fault_equipment',
                'status_operasi',
            ]);
        });
    }
};
