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
    Schema::create('ratings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('novel_id')->constrained()->onDelete('cascade');
        
        // ใช้ tinyInteger เพื่อประหยัดพื้นที่ (เก็บค่า 1-5)
        $table->unsignedTinyInteger('stars'); 
        
        // หนึ่งคนให้คะแนนนิยายเรื่องหนึ่งได้แค่ครั้งเดียว (ถ้าให้ใหม่คือการ Update)
        $table->unique(['user_id', 'novel_id']); 
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
