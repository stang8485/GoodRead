<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('จัดการหมวดหมู่นิยาย') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <x-label for="name" value="ชื่อหมวดหมู่ใหม่" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                        </div>
                        <x-button class="mt-6">
                            เพิ่มหมวดหมู่
                        </x-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-4 border-b">ID</th>
                            <th class="p-4 border-b">ชื่อหมวดหมู่</th>
                            <th class="p-4 border-b">Slug</th>
                            <th class="p-4 border-b text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td class="p-4 border-b">{{ $category->id }}</td>
                            <td class="p-4 border-b">{{ $category->name }}</td>
                            <td class="p-4 border-b text-gray-500">{{ $category->slug }}</td>
                            <td class="p-4 border-b text-center">
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('ยืนยันการลบ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">ลบ</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>