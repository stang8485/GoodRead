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
    // ไฟล์นี้มี timestamp ก่อน create_novels_table จึงข้ามเมื่อยังไม่มีตาราง
    if (Schema::hasTable('novels') && !Schema::hasColumn('novels', 'slug')) {
        Schema::table('novels', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    if (Schema::hasTable('novels') && Schema::hasColumn('novels', 'slug')) {
        Schema::table('novels', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
};
