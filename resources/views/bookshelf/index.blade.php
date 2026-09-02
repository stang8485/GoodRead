<x-app-layout>
    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-l-4 border-orange-500 pl-3">ชั้นหนังสือของฉัน</h1>

            @if($books->isEmpty())
                <div class="bg-white rounded-xl p-10 text-center text-gray-500 shadow-sm">
                    <p class="text-base mb-3">ยังไม่มีนิยายในชั้นหนังสือของคุณ</p>
                    <a href="{{ url('/') }}" class="inline-block bg-orange-500 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                        ไปค้นหานิยายอ่านเลย
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($books as $novel)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between">
                            <a href="{{ route('novels.show', $novel->id) }}" class="block">
                                @if($novel->cover_image)
                                    <img src="{{ asset('storage/' . $novel->cover_image) }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-xs text-gray-400">ไม่มีรูปภาพ</div>
                                @endif
                                <div class="p-2.5">
                                    <h3 class="font-bold text-gray-800 text-sm truncate">{{ $novel->title }}</h3>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $novel->author->username ?? $novel->author->name }}</p>
                                </div>
                            </a>
                            
                            {{-- ปุ่มลบออกจากชั้นหนังสือ --}}
                            <div class="p-2 border-t border-gray-100 bg-gray-50">
                                <form action="{{ route('bookmarks.toggle', $novel->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-center text-[11px] text-red-600 hover:text-red-800 font-semibold">
                                        ลบออกจากชั้น
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>