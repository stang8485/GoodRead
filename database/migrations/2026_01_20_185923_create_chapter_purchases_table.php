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
    Schema::create('chapter_purchases', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
        
        // ราคา ณ วันที่ซื้อ (เผื่อผู้เขียนเปลี่ยนราคาทีหลัง)
        $table->integer('price_paid'); 
        
        $table->timestamps();
        
        // ป้องกันการบันทึกการซื้อซ้ำในตอนเดิม
        $table->unique(['user_id', 'chapter_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_purchases');
    }
};
