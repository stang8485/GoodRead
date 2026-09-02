<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // slug กำหนดตายตัวเพื่อให้ seed ซ้ำได้ และ URL ไม่เปลี่ยนตามการแสดงชื่อภาษาไทย
        $categories = [
            ['name' => 'Y-Yaoi', 'slug' => 'y-yaoi'],
            ['name' => 'จีน-กำลังภายใน-เทพเซียน', 'slug' => 'chinese-wuxia-xianxia'],
            ['name' => 'ธรรมะ', 'slug' => 'dharma'],
            ['name' => 'ผจญภัย-แอคชั่น-ไซไฟ', 'slug' => 'adventure-action-sci-fi'],
            ['name' => 'รัก', 'slug' => 'romance'],
            ['name' => 'อีโรติก', 'slug' => 'erotic'],
            ['name' => 'เรื่องสั้น', 'slug' => 'short-story'],
            ['name' => 'แฟนฟิค', 'slug' => 'fan-fiction'],
            ['name' => 'Y-Yuri', 'slug' => 'y-yuri'],
            ['name' => 'ทั่วไป', 'slug' => 'general'],
            ['name' => 'นิยายสาร', 'slug' => 'literature'],
            ['name' => 'ย้อนยุค-อนาคต', 'slug' => 'historical-future'],
            ['name' => 'สืบสวนสอบสวน-ระทึกขวัญ', 'slug' => 'mystery-thriller'],
            ['name' => 'เกมออนไลน์', 'slug' => 'online-game'],
            ['name' => 'แฟนตาซี', 'slug' => 'fantasy'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}

// class CategorySeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         //
//     }
// }
