<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">รายงานเหรียญสำหรับผู้ดูแล</h2></x-slot>
    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- ฟอร์มค้นหา -->
            <form method="GET" class="bg-white p-5 rounded-lg shadow-sm flex flex-wrap items-end gap-4">
                
                <!-- 1. ค้นหาแบบวันเดียว -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">รายวัน</label>
                    <input type="date" name="date" value="{{ $date ?? '' }}" class="mt-1 block rounded border-gray-300">
                </div>

                <!-- 2. ค้นหาแบบทั้งเดือน -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">รายเดือน</label>
                    <input type="month" name="month" value="{{ $month ?? '' }}" class="mt-1 block rounded border-gray-300">
                </div>

                <!-- 3. ค้นหาแบบสัปดาห์ (ของเดิม) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">สัปดาห์</label>
                    <input type="week" name="week" value="{{ $week ?? '' }}" class="mt-1 block rounded border-gray-300">
                </div>

                <!-- 4. ค้นหาตามชื่อนิยาย -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">ชื่อนิยาย</label>
                    <select name="novel_id" class="mt-1 block rounded border-gray-300 min-w-[200px]">
                        <option value="">-- นิยายทั้งหมด --</option>
                        <!-- แก้เว้นวรรคให้ถูกต้องแล้ว -->
                        @foreach($novels as $novel)
                            <option value="{{ $novel->id }}" {{ ($novelId ?? '') == $novel->id ? 'selected' : '' }}>
                                {{ $novel->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ปุ่มดำเนินการ -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">ดูรายงาน</button>
                    <!-- ปุ่มสำหรับล้างค่ากลับไปเป็นค่าเริ่มต้น (URL ปัจจุบันแบบไม่มี Query String) -->
                    <a href="{{ url()->current() }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center">ล้างค่า</a>
                </div>
            </form>

            <!-- แสดงช่วงเวลา -->
            <p class="text-gray-600">
                ข้อมูลช่วงวันที่ {{ $start->format('d/m/Y') }} – {{ $end->format('d/m/Y') }}
            </p>

            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-6 shadow"><p class="text-gray-500">ยอดใช้เหรียญซื้อตอน</p><p class="text-3xl font-bold text-red-600">{{ number_format($coinsSpent) }} <span class="text-base">เหรียญ</span></p></div>
                <div class="bg-white rounded-lg p-6 shadow"><p class="text-gray-500">ยอดเหรียญจากการโอนที่อนุมัติ</p><p class="text-3xl font-bold text-green-600">{{ number_format($coinsToppedUp) }} <span class="text-base">เหรียญ</span></p></div>
                <div class="bg-white rounded-lg p-6 shadow"><p class="text-gray-500">ยอดเงินโอนที่อนุมัติ</p><p class="text-3xl font-bold text-blue-600">฿{{ number_format($approvedTopupMoney, 2) }}</p></div>
            </div>

            <!-- เพิ่มส่วนตารางประวัติกลับเข้าไป (ของเดิมถูกตัดขาดไป) -->
            <section class="bg-white p-6 rounded-lg shadow overflow-x-auto"><h3 class="font-bold text-lg mb-4">ประวัติการโอนเงินซื้อเหรียญ</h3>
                <table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">วันเวลา</th><th class="p-3 text-left">ผู้โอน</th><th class="p-3 text-right">บาท</th><th class="p-3 text-right">เหรียญ</th><th class="p-3 text-left">สถานะ</th><th class="p-3 text-left">ผู้ตรวจ</th></tr></thead><tbody>
                @forelse($topups as $topup)<tr class="border-t"><td class="p-3">{{ $topup->created_at->format('d/m/Y H:i') }}</td><td class="p-3">{{ $topup->user->username }}</td><td class="p-3 text-right">฿{{ number_format($topup->amount, 2) }}</td><td class="p-3 text-right">{{ number_format($topup->coins_to_receive) }}</td><td class="p-3">{{ ['pending'=>'รอตรวจ','approved'=>'อนุมัติ','rejected'=>'ปฏิเสธ'][$topup->status] }}</td><td class="p-3">{{ $topup->admin?->username ?? '-' }}</td></tr>@empty<tr><td colspan="6" class="p-5 text-center text-gray-500">ไม่มีรายการ</td></tr>@endforelse
                </tbody></table><div class="mt-4">{{ $topups->withQueryString()->links() }}</div>
            </section>

            <section class="bg-white p-6 rounded-lg shadow overflow-x-auto"><h3 class="font-bold text-lg mb-4">ประวัติการใช้เหรียญซื้อตอน</h3>
                <table class="min-w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">วันเวลา</th><th class="p-3 text-left">ผู้อ่าน</th><th class="p-3 text-left">นิยาย</th><th class="p-3 text-center">ตอนที่</th><th class="p-3 text-right">ใช้เหรียญ</th></tr></thead><tbody>
                @forelse($purchases as $purchase)<tr class="border-t"><td class="p-3">{{ $purchase->created_at->format('d/m/Y H:i') }}</td><td class="p-3">{{ $purchase->user->username }}</td><td class="p-3">{{ $purchase->chapter->novel->title }}</td><td class="p-3 text-center">{{ $purchase->chapter->chapter_number }}</td><td class="p-3 text-right">{{ number_format($purchase->price_paid) }}</td></tr>@empty<tr><td colspan="5" class="p-5 text-center text-gray-500">ไม่มีรายการ</td></tr>@endforelse
                </tbody></table><div class="mt-4">{{ $purchases->withQueryString()->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>