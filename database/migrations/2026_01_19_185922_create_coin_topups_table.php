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
    Schema::create('coin_topups', function (Blueprint $table) {
        $table->id();
        // ผู้ใช้งานที่แจ้งเติมเงิน
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        // รายละเอียดเงินและเหรียญ
        $table->decimal('amount', 10, 2); // เก็บยอดเงินบาท 
        $table->integer('coins_to_receive'); // จำนวนเหรียญที่จะได้รับ
        
        // หลักฐานการโอน
        $table->string('slip_image'); // พาธไฟล์รูปสลิป
        
        // สถานะการทำรายการ
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        
        // แอดมินผู้อนุมัติ (เป็น Null ได้ในตอนแรก)
        $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_topups');
    }
};
