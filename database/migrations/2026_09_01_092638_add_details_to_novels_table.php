<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('novels', function (Blueprint $table) {
            // คำโปรย
            $table->text('blurb')->nullable()->after('description'); 
            
            // ระดับเนื้อหา เช่น PG ทั่วไป, NC-18
            $table->string('content_rating', 50)->nullable()->after('blurb'); 
            
        });
    }
    public function down()
    {
        Schema::table('novels', function (Blueprint $table) {
            $table->dropColumn(['blurb', 'content_rating']);
        });
    }

   
};
