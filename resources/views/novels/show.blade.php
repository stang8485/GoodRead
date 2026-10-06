<x-app-layout>
    <div class="py-8 sm:py-12 bg-slate-50 min-h-screen font-sans antialiased text-slate-800" 
         x-data="{ openReviewModal: false, activeTab: 'chapters' }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- 1. HERO SECTION (กล่องข้อมูลหลักด้านบน) --}}
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-200/80 p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-start">
                    
                    {{-- คอลัมน์ซ้าย: ปกนิยาย --}}
                    <div class="md:col-span-4 lg:col-span-3 flex justify-center">
                        <div class="relative w-48 sm:w-56 h-72 sm:h-80 flex-shrink-0 group">
                            @if($novel->cover_image)
                                <img src="{{ asset('storage/' . $novel->cover_image) }}" 
                                     alt="{{ $novel->title }}" 
                                     class="w-full h-full object-cover rounded-2xl shadow-lg group-hover:scale-[1.02] transition-transform duration-300 border border-slate-100">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl shadow-inner border border-slate-200 flex flex-col items-center justify-center text-slate-400 gap-2">
                                    <svg class="w-10 h-10 stroke-current opacity-60" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-xs font-semibold">ไม่มีรูปหน้าปก</span>
                                </div>
                            @endif

                            {{-- ป้ายสถานะ --}}
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold tracking-wide rounded-full shadow-md backdrop-blur-md {{ $novel->status == 'ongoing' ? 'bg-blue-600/90 text-white' : 'bg-emerald-600/90 text-white' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $novel->status == 'ongoing' ? 'bg-blue-200 animate-pulse' : 'bg-emerald-200' }}"></span>
                                    {{ $novel->status == 'ongoing' ? 'กำลังเขียน' : 'จบแล้ว' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- คอลัมน์กลาง: ข้อมูลนิยาย, ผู้แต่ง, คำโปรย, แท็ก --}}
                    <div class="md:col-span-8 lg:col-span-6 flex flex-col justify-between h-full space-y-5">
                        <div class="space-y-3">
                            {{-- หมวดหมู่ และ เรตติ้งอายุ --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-gradient-to-r from-orange-500 to-amber-500 text-white text-[11px] font-extrabold px-2.5 py-0.5 rounded-md shadow-sm">
                                    {{ $novel->content_rating ?? 'PG ทั่วไป' }}
                                </span>
                                <span class="inline-flex items-center text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-md border border-slate-200">
                                    หมวด: {{ $novel->category->name ?? 'ทั่วไป' }}
                                </span>
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-snug tracking-tight">
                                {{ $novel->title }}
                            </h1>

                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <span>ผู้เขียน:</span>
                                <span class="font-bold text-orange-600 hover:text-orange-700 transition cursor-pointer">
                                    {{ $novel->author->username ?? $novel->author->name ?? 'ไม่ระบุชื่อ' }}
                                </span>
                            </div>

                            {{-- คำโปรยสั้น (Blurb) --}}
                            @if($novel->blurb)
                                <div class="relative bg-orange-50/50 border-l-4 border-orange-500 rounded-r-xl p-4 text-sm text-slate-700 leading-relaxed italic">
                                    "{{ $novel->blurb }}"
                                </div>
                            @endif

                            {{-- แท็ก --}}
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                @forelse($novel->tags ?? [] as $tag)
                                    <a href="{{ route('home', ['tag' => $tag->name]) }}" class="text-xs bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-600 px-2.5 py-1 rounded-full border border-slate-200 transition">
                                        #{{ $tag->name }}
                                    </a>
                                @empty
                                    <span class="text-xs text-slate-400 italic">ไม่มีแท็ก</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- ปุ่ม Call to Action ด้านล่าง --}}
                        <div class="flex flex-wrap items-center gap-2.5 pt-4 border-t border-slate-100">
                            @if($novel->chapters->count() > 0)
                                @php $firstChapter = $novel->chapters->sortBy('chapter_number')->first(); @endphp
                                <a href="{{ route('chapters.show', ['novel' => $novel->id, 'chapter' => $firstChapter->id]) }}" 
                                   class="inline-flex items-center justify-center bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-orange-500/20 hover:shadow-lg transition-all duration-200 transform active:scale-95">
                                    <svg class="w-4 h-4 mr-2 fill-current" viewBox="0 0 20 20"><path d="M4 4v12l12-6L4 4z"/></svg>
                                    อ่านตอนแรก
                                </a>
                            @endif

                            {{-- ปุ่มถูกใจ (Like Button) --}}
                            @auth
                                <form action="{{ route('novels.like', $novel->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border text-sm font-bold shadow-sm transition-all duration-150 {{ $novel->isLikedBy(auth()->user()) ? 'bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100' : 'bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-600 border-slate-200 hover:border-rose-200' }}">
                                        <svg class="w-4 h-4 mr-1.5 transition-transform duration-150 hover:scale-110 {{ $novel->isLikedBy(auth()->user()) ? 'fill-rose-500 text-rose-500' : 'fill-none stroke-current' }}" 
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span>{{ $novel->isLikedBy(auth()->user()) ? 'ถูกใจแล้ว' : 'ถูกใจ' }}</span>
                                        <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $novel->isLikedBy(auth()->user()) ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $novel->likes->count() }}
                                        </span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-600 text-slate-700 text-sm font-bold shadow-sm transition">
                                    <svg class="w-4 h-4 mr-1.5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span>ถูกใจ</span>
                                    <span class="ml-1.5 text-xs bg-slate-100 px-1.5 py-0.5 rounded-full text-slate-600">
                                        {{ $novel->likes->count() }}
                                    </span>
                                </a>
                            @endauth

                            {{-- ปุ่มติดตามเข้าชั้น --}}
                            @auth
                                <form action="{{ route('bookmarks.toggle', $novel->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border text-sm font-bold shadow-sm transition-all duration-150 {{ auth()->user()->bookmarkedNovels->contains($novel->id) ? 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200' : 'bg-white hover:bg-indigo-50 text-indigo-600 border-indigo-200 hover:border-indigo-300' }}">
                                        <svg class="w-4 h-4 mr-1.5" fill="{{ auth()->user()->bookmarkedNovels->contains($novel->id) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                        {{ auth()->user()->bookmarkedNovels->contains($novel->id) ? 'อยู่ในชั้นหนังสือ' : '+ ติดตามเข้าชั้น' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-bold shadow-sm transition">
                                    + ติดตามเข้าชั้น
                                </a>
                            @endauth

                            {{-- ปุ่มเปิด Modal รีวิว --}}
                            <button type="button" @click="openReviewModal = true" 
                                    class="inline-flex items-center justify-center bg-amber-400 hover:bg-amber-500 text-slate-900 px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition-colors duration-150">
                                ✍️ เขียนรีวิว
                            </button>
                        </div>
                    </div>

                    {{-- คอลัมน์ขวา: การ์ดสถิติ (Stats Card) --}}
                    <div class="md:col-span-12 lg:col-span-3 bg-slate-50/80 rounded-2xl p-5 border border-slate-200/70 flex flex-col justify-between">
                        <div>
                            {{-- เรตติ้งดาว --}}
                            <div class="text-center pb-4 mb-4 border-b border-slate-200">
                                <div class="text-3xl font-black text-amber-500 flex items-center justify-center gap-1">
                                    <span>★</span>
                                    <span>{{ number_format($novel->reviews_avg_rating ?? $novel->ratings_avg_stars ?? 0, 1) }}</span>
                                </div>
                                <span class="text-xs text-slate-500 font-medium">({{ $novel->reviews_count ?? $novel->reviews->count() }} รีวิว)</span>
                            </div>

                            {{-- รายการสถิติ --}}
                            <div class="space-y-2.5 text-xs text-slate-600 font-medium">
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500">ยอดอ่านทั้งหมด</span>
                                    <span class="font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-100 shadow-2xs">{{ number_format($novel->view_count) }}</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500">ยอดถูกใจ</span>
                                    <span class="font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-100 shadow-2xs">{{ number_format($novel->likes->count()) }} ครั้ง</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500">ผู้ติดตาม</span>
                                    <span class="font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-100 shadow-2xs">{{ number_format($novel->followers->count() ?? 0) }} คน</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500">จำนวนตอน</span>
                                    <span class="font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-100 shadow-2xs">{{ $novel->chapters->count() }} ตอน</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-slate-500">อัปเดตล่าสุด</span>
                                    <span class="font-bold text-slate-900">{{ $novel->chapters->max('updated_at')?->diffForHumans() ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Menu สำหรับเจ้าของนิยาย / Admin --}}
                        @auth
                            @if(auth()->id() == $novel->author_id || auth()->user()->role_id <= 2)
                                <div class="mt-5 pt-3 border-t border-slate-200 flex items-center justify-between gap-1.5">
                                    <a href="{{ route('novels.edit', $novel->id) }}" class="flex-1 text-center text-xs bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold py-1.5 rounded-lg transition">แก้ไข</a>
                                    <a href="{{ route('chapters.create', ['novel' => $novel->id]) }}" class="flex-1 text-center text-xs bg-indigo-100 hover:bg-indigo-200 text-indigo-900 font-bold py-1.5 rounded-lg transition">+ เพิ่มตอน</a>
                                    <form action="{{ route('novels.destroy', $novel->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบ?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-bold px-2.5 py-1.5 rounded-lg transition">ลบ</button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>

                </div>
            </div>

            {{-- 2. TABS SECTION (สารบัญ / เรื่องย่อ / รีวิว) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                {{-- แถบ Tab Headers --}}
                <div class="flex border-b border-slate-200 bg-slate-50/60 px-4 text-sm font-bold">
                    <button @click="activeTab = 'chapters'" 
                            :class="activeTab === 'chapters' ? 'text-orange-600 border-b-2 border-orange-500 bg-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'" 
                            class="px-5 py-4 transition-all duration-150 rounded-t-lg">
                        สารบัญตอน ({{ $novel->chapters->count() }})
                    </button>
                    <button @click="activeTab = 'synopsis'" 
                            :class="activeTab === 'synopsis' ? 'text-orange-600 border-b-2 border-orange-500 bg-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'" 
                            class="px-5 py-4 transition-all duration-150 rounded-t-lg">
                        เรื่องย่อฉบับเต็ม
                    </button>
                    <button @click="activeTab = 'reviews'" 
                            :class="activeTab === 'reviews' ? 'text-orange-600 border-b-2 border-orange-500 bg-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'" 
                            class="px-5 py-4 transition-all duration-150 rounded-t-lg">
                        รีวิวทั้งหมด ({{ $novel->reviews->count() }})
                    </button>
                </div>

                <div class="p-6 sm:p-8">
                    {{-- TAB 1: สารบัญตอน --}}
                    <div x-show="activeTab === 'chapters'" class="divide-y divide-slate-100">
                        @forelse($novel->chapters->sortBy('chapter_number') as $chapter)
                            @php
                                $isPurchased = auth()->check() && auth()->user()->hasPurchased($chapter->id);
                                $isFree = $chapter->chapter_number <= 3 || $chapter->is_free;
                                $canManage = auth()->check() && (auth()->id() == $novel->author_id || auth()->user()->role_id <= 2);
                            @endphp
                            <div class="flex justify-between items-center py-4 hover:bg-orange-50/40 px-3 rounded-xl transition duration-150 group">
                                {{-- หมายเลขตอนและชื่อตอน --}}
                                <div class="flex items-center space-x-4 flex-grow min-w-0 pr-4">
                                    <span class="text-slate-400 font-mono w-8 text-center text-sm font-bold group-hover:text-orange-500 transition-colors flex-shrink-0">
                                        {{ $chapter->chapter_number }}
                                    </span>
                                    <a href="{{ route('chapters.show', ['novel' => $novel->id, 'chapter' => $chapter->id]) }}" class="text-slate-800 group-hover:text-orange-600 text-sm font-medium transition-colors truncate">
                                        {{ $chapter->title }}
                                    </a>
                                </div>

                                {{-- สถานะราคา และปุ่มจัดการ --}}
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    {{-- ป้ายราคา/ฟรี/ซื้อแล้ว --}}
                                    @if($isFree)
                                        <span class="text-emerald-700 text-[11px] font-bold bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                            อ่านฟรี
                                        </span>
                                    @elseif($isPurchased)
                                        <span class="text-blue-700 text-[11px] font-bold bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200">
                                            ซื้อแล้ว
                                        </span>
                                    @else
                                        <span class="text-amber-800 text-xs font-bold bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200 flex items-center gap-1">
                                            🔒 {{ $chapter->price }} เหรียญ
                                        </span>
                                    @endif

                                    {{-- ปุ่มแก้ไข/ลบตอน (แสดงเฉพาะเจ้าของนิยาย หรือ Admin) --}}
                                    @if($canManage)
                                        <div class="flex items-center gap-1.5 pl-2 border-l border-slate-200">
                                            {{-- ปุ่มแก้ไข --}}
                                            <a href="{{ route('chapters.edit', ['novel' => $novel->id, 'chapter' => $chapter->id]) }}" 
                                            class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold px-2 py-1 rounded-lg border border-amber-200 transition">
                                                แก้ไข
                                            </a>

                                            {{-- ปุ่มลบตอน --}}
                                            <form action="{{ route('chapters.destroy', ['novel' => $novel->id, 'chapter' => $chapter->id]) }}" 
                                                method="POST" 
                                                onsubmit="return confirm('ยืนยันการลบตอนที่ {{ $chapter->chapter_number }} หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้');" 
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold px-2 py-1 rounded-lg border border-rose-200 transition">
                                                    ลบ
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-400">
                                <p class="text-sm">ยังไม่มีตอนที่เผยแพร่ในขณะนี้</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- TAB 2: เรื่องย่อ --}}
                    <div x-show="activeTab === 'synopsis'" style="display: none;">
                        <h3 class="font-bold text-slate-900 border-l-4 border-orange-500 pl-3 mb-4 text-base">เรื่องย่อ</h3>
                        <div class="text-slate-700 leading-relaxed whitespace-pre-line text-sm bg-slate-50/80 p-6 rounded-2xl border border-slate-200/60 font-serif">
                            {{ $novel->synopsis ?? $novel->description ?? 'ไม่มีข้อมูลเรื่องย่อ' }}
                        </div>
                    </div>

                    {{-- TAB 3: รีวิว --}}
                    <div x-show="activeTab === 'reviews'" style="display: none;" class="space-y-4">
                        @forelse($novel->reviews as $review)
                            <div class="bg-slate-50 border border-slate-200/70 rounded-2xl p-5 transition hover:shadow-xs">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-sm text-slate-900">
                                            {{ $review->is_anonymous ? '@ผู้ใช้นิรนาม' : ($review->user->username ?? $review->user->name ?? 'สมาชิก') }}
                                        </span>
                                        <span class="text-amber-500 font-black text-xs">★ {{ $review->rating }}</span>
                                    </div>
                                    <span class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-sm text-slate-700 leading-relaxed">{{ $review->content }}</p>

                                @if(!empty($review->review_tags))
                                    @php $tags = is_array($review->review_tags) ? $review->review_tags : json_decode($review->review_tags, true); @endphp
                                    @if($tags)
                                        <div class="mt-3 flex flex-wrap gap-1.5">
                                            @foreach($tags as $t)
                                                <span class="text-[11px] bg-orange-100/70 text-orange-800 px-2.5 py-0.5 rounded-full font-medium border border-orange-200/50">{{ $t }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12 text-slate-400">
                                <p class="text-sm">ยังไม่มีรีวิวสำหรับเรื่องนี้ มาร่วมเป็นคนแรกที่รีวิวกันเถอะ!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 3. ส่วนคอมเมนต์ --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6 border-l-4 border-indigo-500 pl-3">ความคิดเห็น</h3>

                @auth
                    <form action="{{ route('comments.store', $novel->id) }}" method="POST" class="mb-8">
                        @csrf
                        <input type="hidden" name="chapter_id" value="">
                        <textarea name="comment_text" rows="3" 
                                  class="w-full border-slate-200 rounded-xl shadow-2xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm placeholder-slate-400 p-3.5 transition" 
                                  placeholder="พิมพ์ความคิดเห็นของคุณที่นี่..."></textarea>
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm hover:shadow transition transform active:scale-95">
                                ส่งคอมเมนต์
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-center mb-6">
                        <p class="text-sm text-slate-600">กรุณา <a href="{{ route('login') }}" class="text-indigo-600 underline font-bold hover:text-indigo-700">เข้าสู่ระบบ</a> เพื่อแสดงความคิดเห็น</p>
                    </div>
                @endauth

                <div class="divide-y divide-slate-100">
                    @forelse($novel->comments->whereNull('chapter_id') as $comment)
                        <div class="py-4 first:pt-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-bold text-xs text-slate-900">{{ $comment->user->name }}</span>
                                    <span class="text-[11px] text-slate-400 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                @auth
                                    @if(auth()->id() === $comment->user_id || auth()->user()->role_id <= 2)
                                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('ลบคอมเมนต์นี้หรือไม่?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-medium">ลบ</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                            <p class="text-xs text-slate-700 mt-1.5 leading-relaxed">{{ $comment->comment_text }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">ยังไม่มีความคิดเห็น</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- 4. MODAL POPUP รีวิว  --}}
        <div x-show="openReviewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div x-show="openReviewModal" 
                     x-transition:enter="ease-out duration-200" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-150" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     @click="openReviewModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div x-show="openReviewModal" 
                     x-transition:enter="ease-out duration-200" 
                     x-transition:enter-start="opacity-0 scale-95" 
                     x-transition:enter-end="opacity-100 scale-100" 
                     x-transition:leave="ease-in duration-150" 
                     x-transition:leave-start="opacity-100 scale-100" 
                     x-transition:leave-end="opacity-0 scale-95" 
                     class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full p-6 sm:p-8 z-10">
                    
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-5">
                        <h3 class="text-lg font-black text-slate-900">เขียนรีวิวนิยาย</h3>
                        <button @click="openReviewModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
                    </div>

                    <form action="{{ route('novels.review', $novel->id) }}" method="POST" class="space-y-5">
                        @csrf
                        
                        {{-- ให้คะแนนดาว --}}
                        <div class="text-center py-2 bg-slate-50 rounded-2xl border border-slate-100" x-data="{ currentRating: 5, hoverRating: 0 }">
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                ให้คะแนนเรื่องนี้: 
                                <span class="text-amber-500 font-bold text-sm" x-text="(hoverRating || currentRating) + ' ดาว'"></span>
                                <span class="text-rose-500">*</span>
                            </label>

                            <input type="hidden" name="rating" :value="currentRating" required>

                            <div class="flex justify-center items-center space-x-1.5">
                                <template x-for="star in 5" :key="star">
                                    <button type="button"
                                            @click="currentRating = star"
                                            @mouseenter="hoverRating = star"
                                            @mouseleave="hoverRating = 0"
                                            class="text-3xl focus:outline-none transition-transform duration-150 hover:scale-125"
                                            :class="(hoverRating || currentRating) >= star ? 'text-amber-400' : 'text-slate-200'">
                                        ★
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- ข้อความรีวิว --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">ข้อความรีวิว <span class="text-rose-500">*</span></label>
                            <textarea name="content" rows="3" class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3" placeholder="บอกเล่าความประทับใจต่อนิยายเรื่องนี้..." required></textarea>
                        </div>

                        {{-- แท็กคำนิยาม --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">คำนิยามของเรื่องนี้ (เลือกได้หลายข้อ)</label>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <label class="cursor-pointer bg-slate-100 hover:bg-orange-50 px-3 py-1.5 rounded-full border border-slate-200 transition">
                                    <input type="checkbox" name="review_tags[]" value="ตัวละครมีเสน่ห์" class="rounded text-orange-500 focus:ring-orange-400"> ตัวละครมีเสน่ห์
                                </label>
                                <label class="cursor-pointer bg-slate-100 hover:bg-orange-50 px-3 py-1.5 rounded-full border border-slate-200 transition">
                                    <input type="checkbox" name="review_tags[]" value="ดำเนินเรื่องกระชับ" class="rounded text-orange-500 focus:ring-orange-400"> ดำเนินเรื่องกระชับ
                                </label>
                                <label class="cursor-pointer bg-slate-100 hover:bg-orange-50 px-3 py-1.5 rounded-full border border-slate-200 transition">
                                    <input type="checkbox" name="review_tags[]" value="อ่านแล้วอบอุ่นใจ" class="rounded text-orange-500 focus:ring-orange-400"> อ่านแล้วอบอุ่นใจ
                                </label>
                            </div>
                        </div>

                        {{-- ไม่เปิดเผยตัวตน --}}
                        <div class="flex items-center space-x-2 pt-1">
                            <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" class="rounded text-orange-500 focus:ring-orange-400 border-slate-300">
                            <label for="is_anonymous" class="text-xs text-slate-600 font-medium">ไม่เปิดเผยตัวตน (แสดงเป็น @ผู้ใช้นิรนาม)</label>
                        </div>

                        <div class="mt-6 flex justify-end gap-2.5">
                            <button type="button" @click="openReviewModal = false" class="px-5 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition">ยกเลิก</button>
                            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-xl text-xs font-bold shadow-md shadow-orange-500/20 transition">ส่งรีวิว</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>