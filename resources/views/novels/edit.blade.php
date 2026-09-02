<x-app-layout>
    <div class="py-10 sm:py-12 bg-slate-50 min-h-screen font-sans antialiased text-slate-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- หัวข้อหน้า --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">✏️ แก้ไขนิยาย</h2>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $novel->title }}</p>
                </div>
                <a href="{{ route('novels.show', $novel->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-2xs transition">
                    ← กลับไปหน้านิยาย
                </a>
            </div>

            {{-- กล่องฟอร์ม --}}
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200/80">
                <form action="{{ route('novels.update', $novel->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('ยืนยันการแก้ไขข้อมูลนิยาย?');" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- 1. ชื่อเรื่อง --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            ชื่อเรื่อง <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $novel->title) }}" 
                               class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition @error('title') border-rose-400 @enderror" 
                               placeholder="ระบุชื่อเรื่องนิยายของคุณ..." required>
                        @error('title')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- 2. หมวดหมู่นิยาย --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                หมวดหมู่ <span class="text-rose-500">*</span>
                            </label>
                            <select name="category_id" class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs bg-white transition @error('category_id') border-rose-400 @enderror" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $novel->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
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
                                <option value="ทั่วไป (General)" {{ old('content_rating', $novel->content_rating) == 'ทั่วไป (General)' ? 'selected' : '' }}>ทั่วไป (General)</option>
                                <option value="PG-13" {{ old('content_rating', $novel->content_rating) == 'PG-13' ? 'selected' : '' }}>PG-13 (อายุ 13 ปีขึ้นไป)</option>
                                <option value="NC-18" {{ old('content_rating', $novel->content_rating) == 'NC-18' ? 'selected' : '' }}>NC-18 (สำหรับผู้ใหญ่)</option>
                            </select>
                            @error('content_rating')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 4. สถานะนิยาย --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                สถานะนิยาย <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs bg-white transition @error('status') border-rose-400 @enderror" required>
                                <option value="ongoing" {{ old('status', $novel->status) == 'ongoing' ? 'selected' : '' }}>กำลังเขียน</option>
                                <option value="completed" {{ old('status', $novel->status) == 'completed' ? 'selected' : '' }}>จบแล้ว</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 5. คำโปรยสั้น (Blurb) --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">คำโปรยสั้น (Blurb)</label>
                            <span class="text-[11px] text-slate-400">แสดงเด่นที่ส่วนหัวของหน้านิยาย</span>
                        </div>
                        <textarea name="blurb" rows="2" 
                                  class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition placeholder-slate-400" 
                                  placeholder="คำโปรยสั้นๆ 1-2 ประโยคเพื่อดึงดูดนักอ่าน...">{{ old('blurb', $novel->blurb) }}</textarea>
                        @error('blurb')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 6. เรื่องย่อฉบับเต็ม --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            เรื่องย่อฉบับเต็ม <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="description" rows="5" 
                                  class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition placeholder-slate-400 @error('description') border-rose-400 @enderror" 
                                  placeholder="เขียนเนื้อเรื่องย่อแบบละเอียด แนะนำปมเรื่อง ตัวละคร..." required>{{ old('description', $novel->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 7. แท็กนิยาย (Tags) --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs font-bold text-slate-700">แท็กนิยาย (Tags)</label>
                            <span class="text-[11px] text-slate-400">คั่นด้วยเครื่องหมายจุลภาค ( , )</span>
                        </div>
                        <input type="text" name="tags" 
                               value="{{ old('tags', optional($novel->tags)->pluck('name')->implode(', ')) }}" 
                               class="w-full border-slate-200 rounded-xl text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 p-3 shadow-2xs transition placeholder-slate-400" 
                               placeholder="เช่น รักแฟนตาซี, ย้อนเวลา, เกิดใหม่, ระบบ">
                        @error('tags')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 8. รูปหน้าปก (แสดงรูปเดิม + อัปโหลดรูปใหม่) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">รูปหน้าปก (Cover Image)</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 border border-slate-200 rounded-2xl bg-slate-50/50">
                            @if($novel->cover_image)
                                <div class="relative flex-shrink-0 w-24 h-36 rounded-xl overflow-hidden shadow-sm border border-slate-200">
                                    <img src="{{ asset('storage/' . $novel->cover_image) }}" alt="ปกปัจจุบัน" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0 inset-x-0 bg-slate-900/70 text-white text-[10px] text-center py-0.5">รูปปัจจุบัน</span>
                                </div>
                            @else
                                <div class="w-24 h-36 bg-slate-200 rounded-xl flex items-center justify-center text-slate-400 text-xs border border-slate-300">
                                    ไม่มีรูป
                                </div>
                            @endif

                            <div class="space-y-1.5 flex-grow">
                                <label class="block text-xs text-slate-600 font-semibold">อัปโหลดรูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</label>
                                <input type="file" name="cover_image" accept="image/*" 
                                       class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100 cursor-pointer">
                                <p class="text-[11px] text-slate-400">รองรับ PNG, JPG, JPEG ขนาดไม่เกิน 2MB</p>
                            </div>
                        </div>
                        @error('cover_image')
                            <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ปุ่มบันทึกข้อมูล --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <a href="{{ route('novels.show', $novel->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            ยกเลิก
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md shadow-orange-500/20 hover:shadow-lg transition duration-150 transform active:scale-95">
                            บันทึกการแก้ไข
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>