<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-center mb-8 text-gray-800">เลือกจำนวนเหรียญที่ต้องการเติม</h2>
            
            {{-- แพ็กเกจ --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                @foreach([['baht' => 50, 'coins' => 50], ['baht' => 100, 'coins' => 100], ['baht' => 300, 'coins' => 300]
                , ['baht' => 500, 'coins' => 500], ['baht' => 1000, 'coins' => 1000], ['baht' => 3000, 'coins' => 3000]] as $pkg)
                <div class="bg-white p-6 rounded-2xl shadow-sm border hover:border-indigo-500 transition text-center group">
                    <div class="text-3xl font-bold text-indigo-600 mb-2">{{ number_format($pkg['coins']) }}</div>
                    <div class="text-gray-500 mb-4 font-medium text-sm">Coins</div>
                    <div class="text-xl font-semibold mb-6">฿{{ number_format($pkg['baht']) }}</div>
                    <a href="{{ route('topup.payment', ['amount' => $pkg['baht'], 'coins' => $pkg['coins']]) }}" 
                       class="block w-full py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-md">
                        เลือกแพ็กเกจนี้
                    </a>
                </div>
                @endforeach
            </div>

            {{-- ระบุจำนวนเอง --}}
            <div class="bg-white p-8 rounded-2xl shadow-sm border-2 border-dashed border-gray-200 text-center">
                <h3 class="text-lg font-bold text-gray-700 mb-4">หรือระบุจำนวนที่ต้องการเติมเอง</h3>
                
                <form action="{{ route('topup.payment') }}" method="GET" class="max-w-sm mx-auto">
                    <div class="relative mb-6">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 font-bold text-xl">฿</span>
                        <input type="number" name="amount" min="20" required
                               id="custom_amount"
                               placeholder="เติมขั้นต่ำ 20 บาท"
                               class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-2xl font-bold text-indigo-600"
                               oninput="document.getElementById('custom_coins').value = this.value">
                        {{-- ซ่อนค่า coins(ถ้า 1 บาท = 1 เหรียญ) --}}
                        <input type="hidden" name="coins" id="custom_coins">
                    </div>

                    <button type="submit" class="w-full py-4 bg-gray-800 text-white rounded-xl font-bold text-lg hover:bg-black transition shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        ดำเนินการต่อ
                    </button>
                    <p class="text-xs text-gray-400 mt-4 italic">* อัตราแลกเปลี่ยน 1 บาท : 1 เหรียญ</p>
                </form>
            </div>
            
        </div>
    </div>
</x-app-layout>