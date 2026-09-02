<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Novel;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug)
    {
        // ค้นหาหมวดหมู่จาก slug
        $category = Category::where('slug', $slug)->firstOrFail();

        // ดึงนิยายที่อยู่ในหมวดหมู่นี้
        $novels = Novel::where('category_id', $category->id)
                    ->whereIn('status', ['published', 'ongoing'])
                    ->with('author')
                    ->latest()
                    ->paginate(12);

        return view('categories.show', compact('category', 'novels'));
    }
}