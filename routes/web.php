<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NovelController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\Admin\AdminTopupController;
use App\Http\Controllers\Admin\CoinReportController;
use App\Http\Controllers\AuthorEarningsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

    // Route สำหรับผู้ใช้งานที่ Login แล้ว
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
    // Dashboard หลัก
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('novels/create', [NovelController::class, 'create'])->name('novels.create');
    // เส้นทางสำหรับกดปุ่มติดตามเข้าชั้น
    Route::post('/novels/{id}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    // เส้นทางสำหรับเปิดดูหน้าชั้นหนังสือ
    Route::get('/bookshelf', [BookmarkController::class, 'index'])->name('bookshelf.index');
    //จัดหนักนิยาย
    Route::resource('novels', NovelController::class)->except(['show','create']);
    // คอมเมนต์
    Route::post('novels/{novel}/comments', [CommentController::class, 'store'])->name('comments.store');
    // ลบคอมเมนต์
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    // กดถูกใจ
    Route::post('/novels/{novel}/like', [NovelController::class, 'toggleLike'])->name('novels.like');
    // รีวิว
    Route::post('/novels/{id}/review', [NovelController::class, 'review'])->name('novels.review');

    // จัดการตอนนิยาย (เพิ่ม/บันทึก)
    Route::get('novels/{novel}/chapters/create', [ChapterController::class, 'create'])->name('chapters.create');
    Route::post('novels/{novel}/chapters', [ChapterController::class, 'store'])->name('chapters.store');

    //route สำหรับ Edit, Update, Destroy  
    Route::get('novels/{novel}/chapters/{chapter}/edit', [ChapterController::class, 'edit'])->name('chapters.edit');
    Route::put('novels/{novel}/chapters/{chapter}', [ChapterController::class, 'update'])->name('chapters.update');
    Route::delete('novels/{novel}/chapters/{chapter}', [ChapterController::class, 'destroy'])->name('chapters.destroy');

    // หน้าเลือกจำนวนเหรียญ (Packages)
    Route::get('/topup', [HomeController::class, 'show'])->name('topup.index');
    // หน้าโอนเงินและอัปโหลดสลิป 
    Route::get('/topup/payment', [HomeController::class, 'payment'])->name('topup.payment');
    // จัดการบันทึกข้อมูลและไฟล์สลิป
    Route::post('/topup/process', [HomeController::class, 'process'])->name('topup.process');
    // ประวัติเติมเงิน
    Route::get('/topup/history', [HomeController::class, 'history'])->name('topup.history');
    //ซื้อตอน
    Route::post('/chapters/{chapter}/purchase', [ChapterController::class, 'purchase'])->name('purchase.chapter');
    Route::get('/author/earnings', [AuthorEarningsController::class, 'index'])->name('author.earnings');
    });

    // หน้าแรกสำหรับทุกคน (Guest & User)
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/novels/{novel}', [NovelController::class, 'show'])->name('novels.show');

    // Route สำหรับการอ่านตอนนิยาย
    Route::get('novels/{novel}/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show');

    // Route สำหรับ Admin และ Super Admin
    Route::middleware(['auth', 'can:admin-access'])->prefix('admin')->group(function () 
    {
    Route::get('/dashboard', [UserController::class, 'index'])->name('admin.dashboard');
    // หน้าแสดงรายการที่รออนุมัติ และฟอร์มเติมเหรียญโดยตรง
    Route::get('/topup', [AdminTopupController::class, 'index'])->name('admin.topup.index');
    // อนุมัติการเติมเหรียญ
    Route::post('/topup/{user}/approve', [AdminTopupController::class, 'approve'])->name('admin.topup.approve');
    // ปฏิเสธสลิป
    Route::post('/topup/{user}/reject', [AdminTopupController::class, 'reject'])->name('admin.topup.reject');
    Route::get('/coin-reports', [CoinReportController::class, 'index'])->name('admin.coin-reports.index');
    // จัดการหมวดหมู่
    Route::resource('categories', AdminCategoryController::class);
    
    // จัดการผู้ใช้และดู Log
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/logs', [UserController::class, 'activityLogs'])->name('admin.logs');
    });
    //ประเภทหมวดหมู่
    Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');
