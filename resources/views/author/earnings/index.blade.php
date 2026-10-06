<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">รายได้เหรียญจากนิยายของฉัน</h2>
    </x-slot>
    
    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- ฟอร์มค้นหาที่ปรับปรุงใหม่ -->
            <form method="GET" action="{{ route('author.earnings') }}" class="bg-white p-5 rounded-lg shadow-sm flex flex-wrap items-end gap-4">
                
                <!-- ช่วงเวลา (แบบเดิม) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">ตั้งแต่</label>
                    <input type="date" name="from" value="{{ $from }}" class="mt-1 block rounded border-gray-300 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ถึง</label>
                    <input type="date" name="to" value="{{ $to }}" class="mt-1 block rounded border-gray-300 sm:text-sm">
                </div>

                <!-- ค้นหาแบบรายวัน -->
                <div>
                    <label class="block text-sm font-medium text-gray-700"> รายวัน</label>
                    <input type="date" name="date" value="{{ $date ?? '' }}" class="mt-1 block rounded border-gray-300 sm:text-sm">
                </div>

                <!-- ค้นหาแบบรายเดือน -->
                <div>
                    <label class="block text-sm font-medium text-gray-700"> รายเดือน</label>
                    <input type="month" name="month" value="{{ $month ?? '' }}" class="mt-1 block rounded border-gray-300 sm:text-sm">
                </div>

                <!-- ค้นหาตามชื่อนิยาย -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">ชื่อนิยาย</label>
                    <select name="novel_id" class="mt-1 block rounded border-gray-300 min-w-[200px] sm:text-sm">
                        <option value="">-- รวมทุกเรื่อง --</option>
                        @isset($novels)
                            @foreach($novels as $novel)
                                <option value="{{ $novel->id }}" {{ ($novelId ?? '') == $novel->id ? 'selected' : '' }}>
                                    {{ $novel->title }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <!-- ปุ่มค้นหาและล้างค่า -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                        ดูรายงาน
                    </button>
                    <a href="{{ route('author.earnings') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                        ล้างค่า
                    </a>
                </div>
            </form>

            <!-- สรุปยอดเหรียญรวมที่ได้รับต่อวัน -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold text-lg mb-3">ยอดเหรียญรวมที่ได้รับต่อวัน</h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse($dailyTotals as $date => $coins)
                        <div class="border rounded p-4">
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ number_format($coins) }} เหรียญ</p>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-full">ยังไม่มีรายได้ในช่วงที่เลือก</p>
                    @endforelse
                </div>
            </div>

            <!-- ตารางยอดแยกตามนิยายในแต่ละวัน -->
            <section class="bg-white p-6 rounded-lg shadow overflow-x-auto">
                <h3 class="font-bold text-lg mb-4">ยอดแยกตามนิยาย</h3>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left">วันที่</th>
                            <th class="p-3 text-left">นิยาย</th>
                            <th class="p-3 text-right">เหรียญที่ได้รับ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($earnings as $item)
                            <tr class="border-t">
                                <td class="p-3">{{ \Carbon\Carbon::parse($item->earned_on)->format('d/m/Y') }}</td>
                                <td class="p-3">{{ $item->novel_title }}</td>
                                <td class="p-3 text-right font-semibold text-yellow-600">{{ number_format($item->coins) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-5 text-center text-gray-500">ไม่มีข้อมูล</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
            
        </div>
    </div>
</x-app-layout>