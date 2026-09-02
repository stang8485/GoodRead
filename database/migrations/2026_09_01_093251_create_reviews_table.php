<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // อ้างอิงผู้ใช้ที่รีวิว
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // อ้างอิงนิยายที่ถูกรีวิว
            $table->foreignId('novel_id')->constrained('novels')->onDelete('cascade');
            
            $table->decimal('rating', 2, 1); // คะแนนดาว เช่น 4.5, 5.0
            $table->text('content')->nullable(); // ข้อความรีวิว
            $table->integer('likes_count')->default(0); // จำนวนคนกดยอดเยี่ยมให้รีวิวนี้
            
            $table->timestamps();
            
            // ป้องกันไม่ให้ User คนเดิมรีวิวนิยายเรื่องเดิมซ้ำ 2 ครั้ง
            $table->unique(['user_id', 'novel_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
