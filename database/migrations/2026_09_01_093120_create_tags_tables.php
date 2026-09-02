<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // สร้างตาราง tags
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // ชื่อแท็ก
            $table->timestamps();
        });

        // สร้างตารางเชื่อมสำหรับนิยายและแท็ก
        Schema::create('novel_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('novel_id')->constrained('novels')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('novel_tag');
        Schema::dropIfExists('tags');
    }
};
