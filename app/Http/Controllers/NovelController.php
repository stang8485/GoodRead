<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Novel;
use App\Models\Category;
use App\Models\User;
use App\Models\rating;
use App\Models\Review;
use App\Models\Tag;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Storage; 

class NovelController extends Controller
{
    public function index() {
    $query = Novel::with(['author', 'category']);

    // ถ้าไม่ใช่ Admin หรือ Super Admin ให้เห็นเฉพาะของตัวเอง
    if (auth()->user()->role_id > 2) {
        $query->where('author_id', auth()->id());
    }

    $novels = $query->latest()->paginate(10);
    return view('novels.index', compact('novels'));
    }

    // หน้าฟอร์มสร้างนิยาย
    public function create() {
        $categories = Category::all();
        return view('novels.create', compact('categories'));
    }

    // บันทึกข้อมูลการสร้างนิยาย
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|max:255', //ชื่อเรื่อง
            'blurb' => 'nullable|string|max:500', // คำโปรย
            'content_rating' => 'required|in:ทั่วไป (General),PG-13,NC-18', // ระดับเนื้อหา
            'description' => 'required', //เรื่องย่อ
            'category_id' => 'required|exists:categories,id', //ประเภท
            'tags' => 'nullable|string', // รับเป็นสตริงคั่นด้วยลูกน้ำ เช่น "รักแฟนตาซี, ย้อนเวลา"
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' //รูปปก
        ]);

        $path = null;
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
        }

        $novel = Novel::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'blurb' => $request->blurb,
            'content_rating' => $request->content_rating,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'author_id' => auth()->id(),
            'cover_image' => $path,
            'status' => 'ongoing'
        ]);

        // จัดการบันทึกแท็ก (สร้างแท็กใหม่ถ้ายังไม่มี และผูกความสัมพันธ์ Many-to-Many)
        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->tags));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if (!empty($name)) {
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $tagIds[] = $tag->id;
                }
            }
            $novel->tags()->sync($tagIds);
        }

        return redirect()->route('novels.show', $novel->id)->with('success', 'ลงทะเบียนนิยายเรียบร้อยแล้ว!');
    }
    // ดึงนิยายพร้อมตอนทั้งหมด เรียงตามเลขตอน
        public function show($idOrSlug) {
            $novel = Novel::where(function($query) use ($idOrSlug) {
                $query->where('id', $idOrSlug)
                      ->orWhere('slug', $idOrSlug);
            })
            ->with([
                'chapters' => function($query) {
                    $query->orderBy('chapter_number', 'asc');
                },
                'category',
                'author',
                'tags',          // ดึงแท็กมาแสดงใต้คำโปรย
                'reviews.user',  // ดึงรายการรีวิวพร้อมชื่อผู้เขียนรีวิว
                'comments.user', // ดึงคอมเมนต์ท้ายเรื่อง
            ])
            ->withAvg('reviews', 'rating') // สร้างตัวแปร $novel->reviews_avg_rating อัตโนมัติ
            ->withCount('reviews')        // สร้างตัวแปร $novel->reviews_count อัตโนมัติ
            ->firstOrFail();

        $novel->increment('view_count');

        return view('novels.show', compact('novel'));
    }

    // ลบนิยาย
    public function destroy($id)
    {
        $novel = Novel::findOrFail($id);

        // ตรวจสอบสิทธิ์ (เฉพาะเจ้าของหรือ Admin)
        if (auth()->id() !== $novel->author_id && auth()->user()->role_id > 2) {
            abort(403, 'คุณไม่มีสิทธิ์ลบนิยายเรื่องนี้');
        }

        // ลบรูปหน้าปกออกจาก Storage 
        if ($novel->cover_image) {
            \Storage::disk('public')->delete($novel->cover_image);
        }

        // ลบตอนย่อยทั้งหมดที่ผูกกับนิยายเรื่องนี้ 
        $novel->chapters()->delete();

        // ลบตัวนิยาย
        $novel->delete();

        return redirect()->route('dashboard')->with('success', 'ลบนิยายเรียบร้อยแล้ว');
    }

    // ฟังก์ชันแสดงหน้าฟอร์มแก้ไข
    public function edit($id)
    {
        $novel = Novel::findOrFail($id);
        $categories = Category::all();

        // ตรวจสอบสิทธิ์: เฉพาะเจ้าของเรื่องหรือ Admin เท่านั้นที่แก้ได้
        if (auth()->id() !== $novel->author_id && auth()->user()->role_id > 2) {
            abort(403, 'คุณไม่มีสิทธิ์แก้ไขนิยายเรื่องนี้');
        }

        return view('novels.edit', compact('novel', 'categories'));
    }

    // ฟังก์ชันบันทึกการแก้ไข
    public function update(Request $request, $id)
    {
        $novel = Novel::findOrFail($id);

        if (auth()->id() !== $novel->author_id && auth()->user()->role_id > 2) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:255',
            'blurb' => 'nullable|string|max:500',
            'content_rating' => 'required|in:ทั่วไป (General),PG-13,NC-18',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:ongoing,completed',
            'tags' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['title', 'blurb', 'content_rating', 'description', 'category_id', 'status']);

        if ($request->hasFile('cover_image')) {
            if ($novel->cover_image) {
                \Storage::disk('public')->delete($novel->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $novel->update($data);

        // ซิงก์แท็กที่อัปเดตใหม่
        if ($request->has('tags')) {
            $tagNames = array_map('trim', explode(',', $request->tags ?? ''));
            $tagIds = [];
            foreach ($tagNames as $name) {
                if (!empty($name)) {
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $tagIds[] = $tag->id;
                }
            }
            $novel->tags()->sync($tagIds);
        }

        return redirect()->route('novels.show', $novel->slug)->with('success', 'อัปเดตข้อมูลนิยายเรียบร้อยแล้ว!');
    }

    public function review(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'content' => 'required|string',
            'review_tags' => 'nullable|array',
        ]);

        Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'novel_id' => $id,
            ],
            [
                'rating' => $request->rating,
                'content' => $request->content,
                'review_tags' => $request->review_tags ? json_encode($request->review_tags) : null,
                'is_anonymous' => $request->has('is_anonymous'),
            ]
        );

        return back()->with('success', 'ส่งรีวิวเรียบร้อยแล้ว!');
    }
    // กดถูกใจ
    public function toggleLike($id)
    {
        $novel = Novel::findOrFail($id);
        $user = auth()->user();
        
        // เช็คว่าเคยไลก์ยัง ถ้าเคยแล้วให้ลบ like ถ้ายังให้เพิ่ม Like
        $like = $novel->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $message = 'ยกเลิกการถูกใจแล้ว';
        } else {
            $novel->likes()->create(['user_id' => $user->id]);
            $message = 'ถูกใจนิยายเรื่องนี้แล้ว';
        }

        return back()->with('success', $message);
    }
}