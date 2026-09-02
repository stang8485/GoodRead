<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ประวัติการเติมเหรียญ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold">รายการเติมเงินทั้งหมด</h3>
                    <a href="{{ route('topup.index') }}" class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        + เติมเหรียญเพิ่ม
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">จำนวนเงิน</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เหรียญที่จะได้รับ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($topups as $topup)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $topup->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                    ฿{{ number_format($topup->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-bold">
                                    {{ number_format($topup->coins_to_receive) }} Coins
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($topup->status == 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">รอดำเนินการ</span>
                                    @elseif($topup->status == 'approved')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">สำเร็จ</span>
                                    @elseif($topup->status == 'rejected')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">ไม่ผ่าน</span>
                                    @endif
                                </td>
                                {{-- <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($topup->status == 'rejected')
                                        @php
                                            $log = \App\Models\ActivityLog::where('table_name', 'coin_topups')
                                                    ->where('record_id', $topup->id)
                                                    ->where('action', 'reject_coin_topup')
                                                    ->first();
                                        @endphp
                                        <span class="text-red-500 text-xs italic">{{ $log ? $log->description : 'ข้อมูลไม่ชัดเจน' }}</span>
                                    @else
                                        -
                                    @endif
                                </td> --}}
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    คุณยังไม่มีประวัติการเติมเงิน
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $topups->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>