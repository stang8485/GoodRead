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
    Schema::create('novels', function (Blueprint $table) {
        $table->id();
        // เชื่อมกับตาราง users (คนเขียน)
        $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
        // เชื่อมกับตาราง categories
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('cover_image')->nullable();
        
        // สถานะนิยาย: กำลังแต่ง หรือ จบแล้ว
        $table->enum('status', ['ongoing', 'completed'])->default('ongoing');
        
        // การเปิดเผยต่อสาธารณะ
        $table->boolean('is_published')->default(false);
        
        // ยอดวิวรวม 
        $table->unsignedBigInteger('view_count')->default(0);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novels');
    }
};
