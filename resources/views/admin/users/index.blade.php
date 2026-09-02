<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('จัดการผู้ใช้งานและประวัติการเข้าใช้งาน') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 border-b">Username</th>
                            <th class="p-3 border-b">ชื่อ-นามสกุล</th>
                            <th class="p-3 border-b">ระดับ</th>
                            <th class="p-3 border-b text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">{{ $user->username }}</td>
                            <td class="p-3 border-b">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td class="p-3 border-b">
                                <span class="px-2 py-1 rounded text-xs {{ $user->role_id == 1 ? 'bg-purple-100 text-purple-700' : ($user->role_id == 2 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $user->role->role_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-3 border-b text-center">
                                @if($user->role_id != 1) {{-- ห้ามแสดงปุ่มลบถ้าเป็น Super Admin --}}
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">ลบผู้ใช้</button>
                                </form>
                                @else
                                <span class="text-gray-400 text-sm">ห้ามลบ</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>