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
        Schema::create('lingkungan_documents', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('folder')->nullable();
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->longText('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type', 50)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->longText('link')->nullable();
            $table->timestamp('tanggal_upload')->nullable();
            $table->timestamps();
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('set null');
            $table->index('judul');
            $table->index('folder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lingkungan_documents');
    }
};
