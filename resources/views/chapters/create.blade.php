<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('เขียนตอนใหม่: ') }} {{ $novel->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('chapters.store', $novel->id) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">ตอนที่</label>
                            <input type="number" name="chapter_number" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="เช่น 1">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">ชื่อตอน</label>
                            <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="เช่น จุดเริ่มต้นของตำนาน">
                        </div>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded-md mb-4 border border-yellow-200">
                        <label class="block font-bold text-sm text-yellow-800 mb-1">ตั้งค่าการเข้าถึง</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="mr-2 text-sm text-gray-600">ราคา (เหรียญ):</span>
                                <input type="number" id="price_input" name="price" value="0" 
                                    class="w-24 border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div id="price_warning" class="text-xs text-red-600 font-bold hidden">
                                * 3 ตอนแรกต้องอ่านฟรีเท่านั้น
                            </div>
                            <p id="free_info" class="text-xs text-yellow-700">
                                * หากใส่ 0 จะเป็นตอนอ่านฟรี
                            </p>
                        </div>
                    </div>

                    <script>
                        const chapterInput = document.querySelector('input[name="chapter_number"]');
                        const priceInput = document.getElementById('price_input');
                        const warning = document.getElementById('price_warning');
                        const info = document.getElementById('free_info');

                        chapterInput.addEventListener('input', function() {
                            if (this.value <= 3 && this.value !== '') {
                                priceInput.value = 0;
                                priceInput.readOnly = true;
                                priceInput.classList.add('bg-gray-100');
                                warning.classList.remove('hidden');
                                info.classList.add('hidden');
                            } else {
                                priceInput.readOnly = false;
                                priceInput.classList.remove('bg-gray-100');
                                warning.classList.add('hidden');
                                info.classList.remove('hidden');
                            }
                        });
                    </script>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 mb-1">เนื้อหานิยาย</label>
                        <textarea name="content" rows="15" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="พิมพ์เนื้อหาที่นี่..."></textarea>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('novels.index') }}" class="mr-4 text-sm text-gray-600 hover:underline">ยกเลิก</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            บันทึกและเผยแพร่
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>