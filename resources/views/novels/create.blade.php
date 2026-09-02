<x-app-layout>
    <div class="py-10 sm:py-12 bg-slate-50 min-h-screen font-sans antialiased text-slate-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- หัวข้อหน้า --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">✍️ เขียนนิยายเรื่องใหม่</h2>
                    <p class="text-sm text-slate-500 mt-0.5">กรอกข้อมูลเพื่อเริ่มต้นเปิดเรื่องนิยายของคุณ</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-2xs transition">
                    ← ยกเลิก
                </a>
            </div>

            {{-- กล่องฟอร์ม --}}
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200/80">
                <form action="{{ route('novels.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    {{-- 1. ชื่อเรื่อง --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ชื่อเรื่อง <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition @error('title') border-rose-400 @enderror" 
                               placeholder="ระบุชื่อเรื่องนิยายของคุณ..." required>
                        @error('title')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- 2. หมวดหมู่นิยาย --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                หมวดหมู่ <span class="text-rose-500">*</span>
                            </label>
                            <select name="category_id" class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs bg-white transition @error('category_id') border-rose-400 @enderror" required>
                                <option value="" disabled selected>-- เลือกหมวดหมู่ --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 3. ระดับเนื้อหา (Content Rating) --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                ระดับเนื้อหา <span class="text-rose-500">*</span>
                            </label>
                            <select name="content_rating" class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs bg-white transition @error('content_rating') border-rose-400 @enderror" required>
                                <option value="ทั่วไป (General)" {{ old('content_rating') == 'ทั่วไป (General)' ? 'selected' : '' }}>ทั่วไป (General)</option>
                                <option value="PG-13" {{ old('content_rating') == 'PG-13' ? 'selected' : '' }}>PG-13 (อายุ 13 ปีขึ้นไป)</option>
                                <option value="NC-18" {{ old('content_rating') == 'NC-18' ? 'selected' : '' }}>NC-18 (สำหรับผู้ใหญ่)</option>
                            </select>
                            @error('content_rating')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 4. คำโปรยสั้น (Blurb) --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">คำโปรยสั้น (Blurb)</label>
                            <span class="text-[11px] text-slate-400">แสดงเด่นที่ส่วนหัวของหน้านิยาย</span>
                        </div>
                        <textarea name="blurb" rows="2" 
                                  class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition placeholder-slate-400" 
                                  placeholder="คำโปรยสั้นๆ 1-2 ประโยคเพื่อดึงดูดนักอ่าน...">{{ old('blurb') }}</textarea>
                        @error('blurb')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 5. เรื่องย่อฉบับเต็ม --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            เรื่องย่อฉบับเต็ม <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="description" rows="5" 
                                  class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition placeholder-slate-400 @error('description') border-rose-400 @enderror" 
                                  placeholder="เขียนเนื้อเรื่องย่อแบบละเอียด แนะนำปมเรื่อง ตัวละคร..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. แท็กนิยาย (Tags) --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">แท็กนิยาย (Tags)</label>
                            <span class="text-[11px] text-slate-400">คั่นด้วยเครื่องหมายจุลภาค ( , )</span>
                        </div>
                        <input type="text" name="tags" value="{{ old('tags') }}" 
                               class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition placeholder-slate-400" 
                               placeholder="เช่น รักแฟนตาซี, ย้อนเวลา, เกิดใหม่, ระบบ">
                        @error('tags')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 7. อัปโหลดรูปหน้าปก --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">รูปหน้าปก (Cover Image)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-2xl hover:border-orange-400 transition bg-slate-50/50">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-slate-600 justify-center">
                                    <label class="relative cursor-pointer bg-transparent rounded-md font-bold text-orange-600 hover:text-orange-500">
                                        <span>เลือกไฟล์รูปภาพ</span>
                                        <input type="file" name="cover_image" accept="image/*" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-[11px] text-slate-400">PNG, JPG, JPEG ขนาดไม่เกิน 2MB</p>
                            </div>
                        </div>
                        @error('cover_image')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ปุ่มกดบันทึก --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            ยกเลิก
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-orange-500/20 hover:shadow-lg transition duration-150 transform active:scale-95">
                            บันทึกและลงทะเบียน
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>