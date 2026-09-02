<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ตรวจสอบรายการเติมเงิน') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-700">รายการที่รอการตรวจสอบ</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                        ทั้งหมด {{ $topup->count() }} รายการ
                    </span>
                </div>

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
                            @forelse($topup as $topup)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $topup->user->username }}</div>
                                    <div class="text-xs text-gray-400">{{ $topup->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                    ฿{{ number_format($topup->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-bold">
                                    {{ number_format($topup->coins_to_receive) }} Coins
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ asset('storage/' . $topup->slip_image) }}" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        เปิดดูสลิป
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                        <form action="{{ route('admin.topup.approve', $topup->id) }}" method="POST" onsubmit="return confirm('ยืนยันการเพิ่มเหรียญให้ผู้ใช้?')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow-sm transition">
                                                อนุมัติ
                                            </button>
                                        </form>

                                        <button type="button" 
                                                onclick="rejectTopup({{ $topup->id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-100 border border-transparent rounded-md font-bold text-xs text-red-700 uppercase tracking-widest hover:bg-red-200 transition">
                                            ปฏิเสธ
                                        </button>

                                        <form id="reject-form-{{ $topup->id }}" action="{{ route('admin.topup.reject', $topup->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            <input type="hidden" name="reason" id="reason-{{ $topup->id }}">
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
        </div>
    </div>
    <script>
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