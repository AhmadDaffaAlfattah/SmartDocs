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
        Schema::create('excel_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('original_name');
            $table->text('file_path');
            $table->longText('sheets_data')->nullable();
            $table->integer('total_sheets')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('excel_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('excel_upload_id');
            $table->string('sheet_name');
            $table->integer('sheet_index');
            $table->longText('sheet_data');
            $table->timestamps();
            $table->foreign('excel_upload_id')->references('id')->on('excel_uploads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excel_sheets');
        Schema::dropIfExists('excel_uploads');
    }
};
