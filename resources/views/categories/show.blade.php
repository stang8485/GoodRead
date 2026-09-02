<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 border-b pb-4 flex justify-between items-end">
                <div>
                    <h2 class="text-3xl font-black text-gray-800">หมวดหมู่: {{ $category->name }}</h2>
                    <p class="text-gray-500 mt-2 italic">รวมนิยายทั้งหมดในหมวด {{ $category->name }}</p>
                </div>
                <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:underline">← กลับหน้าแรก</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @forelse($novels as $novel)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-xl transition overflow-hidden group">
                        <a href="{{ route('novels.show', $novel->slug) }}">
                            <div class="relative">
                                @if($novel->cover_image)
                                    <img src="{{ asset('storage/' . $novel->cover_image) }}" class="h-64 w-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="h-64 bg-gray-200 flex items-center justify-center text-gray-400 font-bold italic text-3xl">G</div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-sm text-gray-900 truncate">{{ $novel->title }}</h3>
                                <p class="text-[10px] text-gray-500 mt-1">โดย {{ $novel->author->username ?? 'ไม่ระบุ' }}</p>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-lg shadow-inner">
                        <p class="text-gray-400 italic text-lg">ขณะนี้ยังไม่มีนิยายในหมวดหมู่ {{ $category->name }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $novels->links() }}
            </div>
        </div>
    </div>
</x-app-layout>