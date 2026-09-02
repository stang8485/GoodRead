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
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        
        // ใครเป็นคนทำ (เชื่อมกับ Users) ถ้าเป็น Guest หรือระบบลบ User ไปแล้วให้เป็น Null
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        
        // ทำอะไร: login, logout, create, update, delete
        $table->string('action'); 
        
        // จัดการตารางไหน: เช่น novels, chapters, users
        $table->string('table_name')->nullable(); 
        
        // ID ของข้อมูลในตารางนั้นๆ (ถ้ามี)
        $table->unsignedBigInteger('record_id')->nullable(); 
        
        // รายละเอียดเพิ่มเติม เช่น "แก้ไขชื่อนิยายจาก A เป็น B"
        $table->text('description')->nullable(); 
        
        // ข้อมูลทางเทคนิคเพื่อความปลอดภัยและการตรวจสอบ
        $table->string('ip_address', 45)->nullable(); // รองรับทั้ง IPv4 และ IPv6
        $table->text('user_agent')->nullable(); // ข้อมูล Browser/Device ที่ใช้งาน
        
        // timestamps จะเก็บ created_at ซึ่งใช้เป็น "เวลาที่เข้าใช้งาน/ทำรายการ" (ข้อ 5)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
