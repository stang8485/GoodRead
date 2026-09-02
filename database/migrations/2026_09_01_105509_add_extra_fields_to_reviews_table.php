<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // เก็บกลุ่มคำนิยามในรูปแบบ JSON 
            $table->json('review_tags')->nullable()->after('content');
            
            // เก็บสถานะการไม่เปิดเผยตัวตน (0 = โชว์ชื่อปกติ, 1 = ซ่อนชื่อเป็น @Dxxxxx)
            $table->boolean('is_anonymous')->default(false)->after('review_tags');
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['review_tags', 'is_anonymous']);
        });
    }
};
