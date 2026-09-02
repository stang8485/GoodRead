<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('แก้ไขตอน: ') }} {{ $chapter->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('chapters.update', [$novel->id, $chapter->id]) }}" method="POST"onsubmit="return confirm('ยืนยันการแก้ไขนิยายตอนนี้?');">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">ตอนที่</label>
                            <input type="number" name="chapter_number" 
                                   value="{{ old('chapter_number', $chapter->chapter_number) }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">ชื่อตอน</label>
                            <input type="text" name="title" 
                                   value="{{ old('title', $chapter->title) }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-md mb-4 border border-yellow-200">
                        <label class="block font-bold text-sm text-yellow-800 mb-1">ตั้งค่าการเข้าถึง</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="mr-2 text-sm text-gray-600">ราคา (เหรียญ):</span>
                                <input type="number" id="price_input" name="price" 
                                       value="{{ old('price', $chapter->price) }}" 
                                       class="w-24 border-gray-300 rounded-md shadow-sm {{ $chapter->chapter_number <= 3 ? 'bg-gray-100' : '' }}"
                                       {{ $chapter->chapter_number <= 3 ? 'readonly' : '' }}>
                            </div>
                            <div id="price_warning" class="text-xs text-red-600 font-bold {{ $chapter->chapter_number <= 3 ? '' : 'hidden' }}">
                                * 3 ตอนแรกต้องอ่านฟรีเท่านั้น
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 mb-1">เนื้อหานิยาย</label>
                        <textarea name="content" rows="15" 
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                  required>{{ old('content', $chapter->content) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('novels.show', $novel->id) }}" class="mr-4 text-sm text-gray-600 hover:underline">ยกเลิก</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition duration-150">
                            บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const chapterInput = document.querySelector('input[name="chapter_number"]');
        const priceInput = document.getElementById('price_input');
        const warning = document.getElementById('price_warning');

        chapterInput.addEventListener('input', function() {
            if (this.value <= 3 && this.value !== '') {
                priceInput.value = 0;
                priceInput.readOnly = true;
                priceInput.classList.add('bg-gray-100');
                warning.classList.remove('hidden');
            } else {
                priceInput.readOnly = false;
                priceInput.classList.remove('bg-gray-100');
                warning.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>