<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('รายการนิยายของฉัน') }}
            </h2>
            <a href="{{ route('novels.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                + เขียนเรื่องใหม่
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">หน้าปก</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ชื่อเรื่อง</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">หมวดหมู่</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">แก้ไข</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($novels as $novel)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($novel->cover_image)
                                        <img src="{{ asset('storage/' . $novel->cover_image) }}" class="h-20 w-14 object-cover rounded shadow">
                                    @else
                                        <div class="h-20 w-14 bg-gray-200 flex items-center justify-center text-[10px] text-gray-400">No Image</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <a href="{{ route('novels.show', $novel->slug) }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-900">
                                        {{ $novel->title }}
                                    </a>
                                    <div class="text-xs text-gray-500">สร้างเมื่อ: {{ $novel->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    {{ $novel->category->name ?? 'ทั่วไป' }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $novel->status == 'ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $novel->status == 'ongoing' ? 'กำลังเขียน' : 'จบแล้ว' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium space-x-2">
                                    <a href="{{ route('chapters.create', $novel->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">
                                        เพิ่มตอน
                                    </a>
                                    
                                    <a href="{{ route('novels.edit', $novel->id) }}" class="text-yellow-600 hover:text-yellow-900">แก้ไข</a>

                                    <form action="{{ route('novels.destroy', $novel->id) }}" method="POST" class="inline shadow-none">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('ยืนยันการลบนิยายเรื่องนี้?')">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 italic">
                                    คุณยังไม่มีนิยายในระบบ เริ่มเขียนกันเลย!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $novels->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>