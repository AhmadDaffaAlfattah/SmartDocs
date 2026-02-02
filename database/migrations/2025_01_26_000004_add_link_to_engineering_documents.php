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
        Schema::table('engineering_documents', function (Blueprint $table) {
            // Add link column for storing file links or document links
            $table->longText('link')->nullable()->after('file_size');

            // Make file columns nullable to support link-only entries
            $table->string('file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->string('file_type')->nullable()->change();
            $table->integer('file_size')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engineering_documents', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
