<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 text-center">
                <a href="{{ route('novels.show', ['novel' => $novel->id]) }}" class="text-indigo-600 hover:underline text-sm">
                    ← กลับไปที่สารบัญ
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">{{ $chapter->title }}</h1>
                <p class="text-gray-500 mt-2">ตอนที่ {{ $chapter->chapter_number }} | ยอดวิว: {{ $chapter->view_count }}</p>
            </div>

            {{-- --- ส่วนตรวจสอบสิทธิ์การอ่าน --- --}}
            @if($needsPurchase)
                <div class="bg-white shadow-sm sm:rounded-lg p-12 text-center border-2 border-yellow-400">
                    <div class="flex justify-center mb-4">
                        <svg class="w-16 h-16 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">ตอนนิยายนี้ถูกล็อกอยู่</h2>
                    <p class="text-gray-600 mt-2 mb-6">กรุณาใช้ {{ $chapter->price }} เหรียญ เพื่ออ่านเนื้อหาตอนนี้</p>
                    
                    @auth
                        {{-- ถ้า Login แล้ว แสดงฟอร์มซื้อตามปกติ --}}
                        <form action="{{ route('purchase.chapter', $chapter->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-full font-bold shadow-md transition transform hover:scale-105">
                                ปลดล็อกด้วย {{ $chapter->price }} เหรียญ
                            </button>
                        </form>
                        
                        @if(auth()->user()->coin_balance < $chapter->price)
                            <p class="mt-4 text-sm text-red-500">
                                เหรียญของคุณไม่พอ (คงเหลือ: {{ auth()->user()->coin_balance }}) 
                                <a href="{{ route('topup.index') }}" class="underline font-bold">เติมเหรียญที่นี่</a>
                            </p>
                        @endif
                    @else
                        {{-- ถ้ายังไม่ได้ Login ให้เปลี่ยนปุ่มเป็นลิงก์ไปหน้า Login --}}
                        <a href="{{ route('login') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-full font-bold shadow-md transition transform hover:scale-105">
                            เข้าสู่ระบบเพื่อปลดล็อก
                        </a>
                        <p class="mt-4 text-sm text-gray-500 italic">
                            คุณต้องเข้าสู่ระบบเพื่อใช้เหรียญในการซื้อตอนนิยาย
                        </p>
                    @endauth
                </div>
            @else
                {{-- แสดงเนื้อหาจริง --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-8 md:p-12 leading-relaxed text-lg text-gray-800 break-words whitespace-pre-line">
                    {{ $chapter->content }}
                </div>
            @endif
            {{-- -------------------------- --}}

            {{-- ส่วนปุ่ม ตอนก่อนหน้า / ตอนถัดไป --}}
            <div class="mt-8 flex justify-between">
                @php
                    $prev = $novel->chapters->where('chapter_number', $chapter->chapter_number - 1)->first();
                    $next = $novel->chapters->where('chapter_number', $chapter->chapter_number + 1)->first();
                @endphp

                @if($prev)
                    <a href="{{ route('chapters.show', [$novel->id, $prev->id]) }}" class="bg-white border px-4 py-2 rounded shadow-sm hover:bg-gray-50 text-gray-700">
                        ← ตอนก่อนหน้า
                    </a>
                @else
                    <div></div>
                @endif

                @if($next)
                    <a href="{{ route('chapters.show', [$novel->id, $next->id]) }}" class="bg-indigo-600 text-white px-6 py-2 rounded shadow-sm hover:bg-indigo-700 font-bold">
                        ตอนถัดไป →
                    </a>
                @endif
            </div>
        </div>
    </div>

<div class="mt-10 bg-white p-6 rounded-lg shadow-sm">
    <h3 class="text-xl font-bold mb-4">ความคิดเห็น </h3>

    @auth
        <form action="{{ route('comments.store', $novel->id) }}" method="POST" class="mb-6">
            @csrf
            {{-- ส่ง chapter_id --}}
            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
            
            <textarea name="comment_text" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 shadow-sm" placeholder="เขียนความคิดเห็นของคุณ..."></textarea>
            <button type="submit" class="mt-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">ส่งคอมเมนต์</button>
        </form>
    @else
        <p class="mb-6 text-gray-500 text-sm">กรุณา <a href="{{ route('login') }}" class="text-indigo-600 underline">เข้าสู่ระบบ</a> เพื่อแสดงความคิดเห็น</p>
    @endauth

    @php
        $currentChapterId = isset($chapter) ? $chapter->id : null;
        $displayComments = $novel->comments->where('chapter_id', $currentChapterId);
    @endphp

    @foreach($displayComments as $comment)
        <div class="flex space-x-3 border-b pb-4 mb-4">
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $comment->user->name }}</h4>
                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- เฉพาะเจ้าของคอมเมนต์หรือแอดมิน  --}}
                    @auth
                        @if(auth()->id() === $comment->user_id || auth()->user()->role_id <= 2)
                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" 
                                onsubmit="return confirm('คุณต้องการลบคอมเมนต์นี้ใช่หรือไม่?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    ลบ
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
                <p class="text-gray-700 mt-2">{{ $comment->comment_text }}</p>
            </div>
        </div>
    @endforeach
</div>
</x-app-layout>