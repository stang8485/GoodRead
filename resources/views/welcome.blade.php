<x-app-layout>
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

            {{-- แถบส่วนหัว / ลิงก์ส่วนตัว --}}
            <div class="flex justify-between items-center border-b border-gray-200 pb-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">คลังนิยาย</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">อ่านและค้นพบนิยายหลากหลายรสชาติตามใจคุณ</p>
                </div>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">
                        ไปหน้าส่วนตัวของคุณ →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                        เข้าสู่ระบบ
                    </a>
                @endauth
            </div>

            {{-- จุดที่ 1: ตรวจสอบว่าอยู่ในโหมดค้นหา/กรองข้อมูลหรือไม่ --}}
            @if(isset($novels))
                <div>
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl text-gray-700">
                            ผลการค้นหา
                            @if(request('search')) สำหรับ: <span class="font-bold text-indigo-600">"{{ request('search') }}"</span> @endif
                            @if(request('tag')) แท็ก: <span class="font-bold text-indigo-600">#{{ request('tag') }}</span> @endif
                        </h2>
                        <a href="{{ route('home') }}" class="text-xs text-red-500 hover:underline italic">✕ ล้างการค้นหา</a>
                    </div>

                    {{-- จุดที่ 2: แสดงการ์ดนิยายโดยตรง ไม่ต้องพึ่งพา component --}}
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                        @forelse($novels as $novel)
                            <div class="bg-white rounded-md shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group border border-gray-100 flex flex-col justify-between">
                                <a href="{{ route('novels.show', $novel->slug ?? $novel->id) }}" class="block">
                                    <div class="relative aspect-[3/4] overflow-hidden bg-gray-200">
                                        <div class="absolute top-1 left-1 flex flex-col gap-1 z-10">
                                            @if(($novel->ratings_avg_stars ?? 0) > 0)
                                                <span class="bg-yellow-400 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center border border-yellow-500">
                                                    ★ {{ number_format($novel->ratings_avg_stars, 1) }}
                                                </span>
                                            @endif
                                            @if(($novel->likes_count ?? 0) > 0)
                                                <span class="bg-red-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center border border-red-600">
                                                    ♥ {{ $novel->likes_count }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($novel->cover_image)
                                            <img src="{{ asset('storage/' . $novel->cover_image) }}" class="h-full w-full object-cover group-hover:scale-110 transition duration-500">
                                        @else
                                            <div class="flex flex-col items-center justify-center h-full text-gray-400 p-2">
                                                <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="text-[10px] uppercase font-bold">No Image</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>

                                    <div class="p-2">
                                        <h3 class="font-bold text-xs text-gray-800 leading-tight h-8 line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors">
                                            {{ $novel->title }}
                                        </h3>
                                        <div class="flex flex-col gap-1">
                                            <p class="text-[9px] text-gray-500 truncate">
                                                <span class="text-gray-400">👤</span> {{ $novel->author->username ?? $novel->author->name ?? 'ไม่ระบุชื่อ' }}
                                            </p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-[8px] font-medium bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100 truncate max-w-[65%]">
                                                    {{ $novel->category->name ?? 'ทั่วไป' }}
                                                </span>
                                                <span class="text-[9px] text-gray-400 font-medium">
                                                    {{ $novel->chapters->count() }} ตอน
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12 text-gray-400">
                                ไม่พบนิยายที่ตรงกับคำค้นหา
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $novels->links() }}
                    </div>
                </div>

            @else
                {{-- 1. นิยายยอดนิยม (ซ้ายสุดคืออันดับ 1) --}}
                @if(isset($popularNovels) && $popularNovels->isNotEmpty())
                    <section>
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">🔥</span>
                                <h2 class="text-xl sm:text-2xl font-black text-gray-900">นิยายยอดนิยม</h2>
                                <span class="text-xs bg-orange-100 text-orange-700 font-bold px-2 py-0.5 rounded-full">Top Hits</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                            @foreach($popularNovels as $index => $novel)
                                <div class="bg-white rounded-md shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group border border-gray-100 flex flex-col justify-between">
                                    <a href="{{ route('novels.show', $novel->slug ?? $novel->id) }}" class="block">
                                        <div class="relative aspect-[3/4] overflow-hidden bg-gray-200">
                                            <div class="absolute top-1 right-1 z-10">
                                                <span class="text-[10px] font-black px-1.5 py-0.5 rounded shadow-sm {{ $index == 0 ? 'bg-amber-500 text-white ring-1 ring-amber-300' : ($index == 1 ? 'bg-slate-400 text-white' : ($index == 2 ? 'bg-amber-700 text-white' : 'bg-black/60 text-white backdrop-blur-xs')) }}">
                                                    #{{ $index + 1 }}
                                                </span>
                                            </div>

                                            <div class="absolute top-1 left-1 flex flex-col gap-1 z-10">
                                                @if(($novel->ratings_avg_stars ?? 0) > 0)
                                                    <span class="bg-yellow-400 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center border border-yellow-500">
                                                        ★ {{ number_format($novel->ratings_avg_stars, 1) }}
                                                    </span>
                                                @endif
                                                @if(($novel->likes_count ?? 0) > 0)
                                                    <span class="bg-red-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center border border-red-600">
                                                        ♥ {{ $novel->likes_count }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($novel->cover_image)
                                                <img src="{{ asset('storage/' . $novel->cover_image) }}" class="h-full w-full object-cover group-hover:scale-110 transition duration-500">
                                            @else
                                                <div class="flex flex-col items-center justify-center h-full text-gray-400 p-2">
                                                    <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span class="text-[10px] uppercase font-bold">No Image</span>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        </div>

                                        <div class="p-2">
                                            <h3 class="font-bold text-xs text-gray-800 leading-tight h-8 line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors">
                                                {{ $novel->title }}
                                            </h3>
                                            <div class="flex flex-col gap-1">
                                                <p class="text-[9px] text-gray-500 truncate">
                                                    <span class="text-gray-400">👤</span> {{ $novel->author->username ?? $novel->author->name ?? 'ไม่ระบุชื่อ' }}
                                                </p>
                                                <div class="flex items-center justify-between mt-1">
                                                    <span class="text-[8px] font-medium bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100 truncate max-w-[65%]">
                                                        {{ $novel->category->name ?? 'ทั่วไป' }}
                                                    </span>
                                                    <span class="text-[9px] text-gray-400 font-medium">
                                                        {{ $novel->chapters->count() }} ตอน
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- จุดที่ 3: เช็กว่ามี $categories ส่งมาหรือไม่ก่อนวนลูป --}}
                @if(isset($categories) && $categories->isNotEmpty())
                    @foreach($categories as $category)
                        @if($category->novels->isNotEmpty())
                            <section class="border-t border-gray-200/70 pt-8">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-1.5 h-5 bg-indigo-600 rounded-full"></div>
                                        <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                                            {{ $category->name }}
                                        </h2>
                                        <span class="text-xs text-gray-400 font-normal">({{ $category->novels->count() }} เรื่อง)</span>
                                    </div>
                                    <a href="{{ route('home', ['category' => $category->id]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                        ดูทั้งหมด →
                                    </a>
                                </div>

                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                                    @foreach($category->novels as $novel)
                                        <div class="bg-white rounded-md shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group border border-gray-100 flex flex-col justify-between">
                                            <a href="{{ route('novels.show', $novel->slug ?? $novel->id) }}" class="block">
                                                <div class="relative aspect-[3/4] overflow-hidden bg-gray-200">
                                                    <div class="absolute top-1 left-1 flex flex-col gap-1 z-10">
                                                        @if(($novel->ratings_avg_stars ?? 0) > 0)
                                                            <span class="bg-yellow-400 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center border border-yellow-500">
                                                                ★ {{ number_format($novel->ratings_avg_stars, 1) }}
                                                            </span>
                                                        @endif
                                                        @if(($novel->likes_count ?? 0) > 0)
                                                            <span class="bg-red-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm flex items-center border border-red-600">
                                                                ♥ {{ $novel->likes_count }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($novel->cover_image)
                                                        <img src="{{ asset('storage/' . $novel->cover_image) }}" class="h-full w-full object-cover group-hover:scale-110 transition duration-500">
                                                    @else
                                                        <div class="flex flex-col items-center justify-center h-full text-gray-400 p-2">
                                                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                            <span class="text-[10px] uppercase font-bold">No Image</span>
                                                        </div>
                                                    @endif
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                </div>

                                                <div class="p-2">
                                                    <h3 class="font-bold text-xs text-gray-800 leading-tight h-8 line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors">
                                                        {{ $novel->title }}
                                                    </h3>
                                                    <div class="flex flex-col gap-1">
                                                        <p class="text-[9px] text-gray-500 truncate">
                                                            <span class="text-gray-400">👤</span> {{ $novel->author->username ?? $novel->author->name ?? 'ไม่ระบุชื่อ' }}
                                                        </p>
                                                        <div class="flex items-center justify-between mt-1">
                                                            <span class="text-[8px] font-medium bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100 truncate max-w-[65%]">
                                                                {{ $category->name }}
                                                            </span>
                                                            <span class="text-[9px] text-gray-400 font-medium">
                                                                {{ $novel->chapters->count() }} ตอน
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endforeach
                @endif
            @endif

        </div>
    </div>
</x-app-layout>