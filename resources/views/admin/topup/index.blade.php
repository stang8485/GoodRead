<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ตรวจสอบรายการเติมเงิน') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @php
                    // แยกหมวดหมู่ข้อมูล (แก้ไขคำว่า pending, approved, rejected ให้ตรงกับค่าในฐานข้อมูลของคุณ)
                    $pendingItems = $topup->where('status', 'pending');
                    $approvedItems = $topup->where('status', 'approved');
                    $rejectedItems = $topup->where('status', 'rejected');
                @endphp

                <!-- เมนูแท็บ -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab('pending')" id="pending-btn" class="tab-btn border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            รอดำเนินการ ({{ $pendingItems->count() }})
                        </button>
                        <button onclick="switchTab('approved')" id="approved-btn" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            อนุมัติแล้ว ({{ $approvedItems->count() }})
                        </button>
                        <button onclick="switchTab('rejected')" id="rejected-btn" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                            ปฏิเสธแล้ว ({{ $rejectedItems->count() }})
                        </button>
                    </nav>
                </div>

                <!-- แท็บ: รอดำเนินการ -->
                <div id="pending" class="tab-content block">
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ผู้ใช้</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวนเงิน (บาท)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เหรียญที่จะได้รับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">หลักฐาน (สลิป)</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pendingItems as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->user->username }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        ฿{{ number_format($item->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-bold">
                                        {{ number_format($item->coins_to_receive) }} Coins
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ asset('storage/' . $item->slip_image) }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            เปิดดูสลิป
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <form action="{{ route('admin.topup.approve', $item->id) }}" method="POST" onsubmit="return confirm('ยืนยันการเพิ่มเหรียญให้ผู้ใช้?')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow-sm transition">
                                                    อนุมัติ
                                                </button>
                                            </form>

                                            <button type="button" 
                                                    onclick="rejectTopup({{ $item->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 border border-transparent rounded-md font-bold text-xs text-red-700 uppercase tracking-widest hover:bg-red-200 transition">
                                                ปฏิเสธ
                                            </button>

                                            <form id="reject-form-{{ $item->id }}" action="{{ route('admin.topup.reject', $item->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="reason" id="reason-{{ $item->id }}">
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                        ไม่มีรายการที่รอการตรวจสอบในขณะนี้
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- แท็บ: อนุมัติแล้ว -->
                <div id="approved" class="tab-content hidden">
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-green-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">ผู้ใช้</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">จำนวนเงิน (บาท)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">เหรียญที่ได้รับ</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-green-700 uppercase tracking-wider">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($approvedItems as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->user->username }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->updated_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        ฿{{ number_format($item->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-bold">
                                        {{ number_format($item->coins_to_receive) }} Coins
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            อนุมัติแล้ว
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                                        ยังไม่มีรายการที่ได้รับการอนุมัติ
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- แท็บ: ปฏิเสธแล้ว -->
                <div id="rejected" class="tab-content hidden">
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">ผู้ใช้</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">จำนวนเงิน (บาท)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">เหตุผลที่ปฏิเสธ</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-red-700 uppercase tracking-wider">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($rejectedItems as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $item->user->username }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->updated_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                        ฿{{ number_format($item->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-red-600">
                                        <!-- สมมติว่ามีฟิลด์ reason ไว้เก็บเหตุผลที่ปฏิเสธ -->
                                        {{ $item->reason ?? 'ไม่ระบุเหตุผล' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ปฏิเสธ
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                                        ยังไม่มีรายการที่ถูกปฏิเสธ
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <script>
    // สคริปต์สำหรับการเปลี่ยนแท็บ
    function switchTab(tabId) {
        // ซ่อนเนื้อหาทั้งหมด
        document.querySelectorAll('.tab-content').forEach(function(el) {
            el.classList.add('hidden');
            el.classList.remove('block');
        });
        
        // รีเซ็ตสไตล์ของปุ่มแท็บทั้งหมด
        document.querySelectorAll('.tab-btn').forEach(function(el) {
            el.classList.remove('border-blue-500', 'text-blue-600');
            el.classList.add('border-transparent', 'text-gray-500');
        });
        
        // แสดงเนื้อหาของแท็บที่เลือก
        document.getElementById(tabId).classList.remove('hidden');
        document.getElementById(tabId).classList.add('block');
        
        // เพิ่มไฮไลต์ให้กับปุ่มแท็บที่เลือก
        const activeBtn = document.getElementById(tabId + '-btn');
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
    }

    // สคริปต์ฟอร์มปฏิเสธเดิมของคุณ
    function rejectTopup(id) {
        const reason = prompt("กรุณาระบุเหตุผลที่ปฏิเสธ :");
        
        if (reason != null && reason != "") {
            document.getElementById('reason-' + id).value = reason;
            document.getElementById('reject-form-' + id).submit();
        } else if (reason == "") {
            alert("ต้องระบุเหตุผลก่อนปฏิเสธครับ");
        }
    }
    </script>
</x-app-layout>