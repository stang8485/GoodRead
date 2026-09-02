<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ยินดีต้อนรับคุณ') }} {{ Auth::user()->username }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-400">
                    <div class="text-gray-500 text-sm font-medium">ยอดเหรียญคงเหลือ</div>
                    <div class="text-2xl font-bold text-gray-800">{{ number_format(Auth::user()->coin_balance) }} Coins</div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium">นิยายของฉัน</div>
                    <div class="text-2xl font-bold text-gray-800">
                        {{ \App\Models\Novel::where('author_id', Auth::id())->count() }} เรื่อง
                    </div>
                </div>

                @if(Auth::user()->role_id <= 2)
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-500">
                    <div class="text-gray-500 text-sm font-medium">ผู้ใช้งานทั้งหมด</div>
                    <div class="text-2xl font-bold text-gray-800">
                        {{ \App\Models\User::count() }} คน
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
                    <div class="text-gray-500 text-sm font-medium">รายการรออนุมัติสลิป</div>
                    <div class="text-2xl font-bold text-red-600">
                        {{ \App\Models\CoinTopup::where('status', 'pending')->count() }} รายการ
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">ดำเนินการ</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('novels.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        + เขียนนิยายเรื่องใหม่
                    </a>
                    <a href="{{ route('topup.history') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        ประวัติการเติมเงิน
                    </a>
                    
                    @if(Auth::user()->role_id <= 2)
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            จัดการผู้ใช้งาน
                        </a>
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            จัดการหมวดหมู่
                        </a>
                        <a href="{{ route('admin.topup.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            ตรวจสอบรายการเติมเงิน
                        </a>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">นิยายล่าสุดของคุณ</h3>
                
                @php
                    $latestNovels = \App\Models\Novel::where('author_id', Auth::id())->latest()->take(3)->get();
                @endphp

                @if($latestNovels->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($latestNovels as $novel)
                            <div class="border rounded-lg p-4 flex flex-col items-center text-center hover:shadow-lg transition">
                                @if($novel->cover_image)
                                    <img src="{{ asset('storage/' . $novel->cover_image) }}" class="h-40 w-28 object-cover rounded shadow mb-3">
                                @else
                                    <div class="h-40 w-28 bg-gray-200 flex items-center justify-center rounded mb-3 text-gray-400 text-xs">No Cover</div>
                                @endif
                                <h4 class="font-bold text-sm truncate w-full">{{ $novel->title }}</h4>
                                <p class="text-xs text-gray-500 mb-3">อัปเดต: {{ $novel->updated_at->diffForHumans() }}</p>
                                
                                <a href="{{ route('novels.show', $novel->slug) }}" class="mt-auto bg-indigo-500 hover:bg-indigo-600 text-white text-[10px] py-1 px-3 rounded">
                                    ดูหน้าสารบัญ
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('novels.index') }}" class="text-indigo-600 text-sm hover:underline">ดูนิยายทั้งหมดของฉัน →</a>
                    </div>
                @else
                    <p class="text-gray-500 italic">คุณยังไม่มีนิยายในระบบ เริ่มเขียนเรื่องแรกกันเลย!</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
