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
            // Add folder_id column as nullable (to support old data)
            $table->unsignedBigInteger('folder_id')->nullable()->after('id');

            // Add foreign key constraint
            $table->foreign('folder_id')
                ->references('id')
                ->on('folders')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engineering_documents', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};
