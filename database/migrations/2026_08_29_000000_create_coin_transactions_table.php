<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['topup', 'chapter_purchase']);
            $table->integer('coins_change')->comment('จำนวนบวกคือได้รับ จำนวนลบคือใช้');
            $table->unsignedInteger('balance_after');
            $table->foreignId('coin_topup_id')->nullable()->constrained('coin_topups')->nullOnDelete();
            $table->foreignId('chapter_purchase_id')->nullable()->constrained('chapter_purchases')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');
    }
};
