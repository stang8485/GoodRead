<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            
            // อ้างอิงว่าใครเป็นคนกดติดตาม
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // อ้างอิงว่ากดติดตามนิยายเรื่องอะไร
            $table->foreignId('novel_id')->constrained('novels')->onDelete('cascade');
            
            // บันทึกตำแหน่งการอ่านล่าสุด (เผื่ออนาคตทำระบบอ่านต่อจากที่ค้างไว้)
            $table->foreignId('last_chapter_id')->nullable()->constrained('chapters')->onDelete('set null');
            
            $table->timestamps();

            // ป้องกันไม่ให้ user คนเดิม กดติดตามนิยายเรื่องเดิมซ้ำให้เกิดข้อมูลเบิ้ล
            $table->unique(['user_id', 'novel_id']); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookmarks');
    }
};
