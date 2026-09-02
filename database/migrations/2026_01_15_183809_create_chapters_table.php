<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('chapters', function (Blueprint $table) {
        $table->id();
        // เชื่อมกับตารางนิยายหลัก
        $table->foreignId('novel_id')->constrained('novels')->onDelete('cascade');
        
        $table->string('title');
        $table->longText('content'); // ใช้ LongText เพื่อรองรับเนื้อหาจาก Text Editor จำนวนมาก
        $table->integer('chapter_number');
        
        // ระบบเหรียญ
        $table->integer('price')->default(0); 
        $table->boolean('is_free')->default(false); // 3 ตอนแรกให้ตั้งเป็น true
        
        // ระบบสถานะและการตั้งเวลา (เงื่อนไขข้อ 10, 11)
        $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft');
        $table->timestamp('scheduled_at')->nullable(); // วันที่ลงนิยายล่วงหน้า
        
        // ยอดวิวรายตอน (สำหรับ Hybrid Method)
        $table->unsignedBigInteger('view_count')->default(0);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
