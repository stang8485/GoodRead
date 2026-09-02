<x-app-layout>
    <div class="py-12">
        <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-xl font-bold mb-6 text-center">ชำระเงินและแจ้งโอน</h2>
            
            <div class="bg-indigo-50 p-4 rounded-lg mb-6 border border-indigo-100 text-center">
                <p class="text-sm text-gray-600">ธนาคารแกรมม่า</p>
                <p class="text-lg font-bold text-indigo-800">012-3-45678-9</p>
                <p class="text-sm text-gray-600">ชื่อบัญชี: บจก. นิยายออนไลน์ GoodRead</p>
                <div class="mt-2 text-red-600 font-bold">ยอดที่ต้องโอน: ฿{{ number_format($amount, 2) }}</div>
            </div>

            <form action="{{ route('topup.process') }}" method="POST" onsubmit="return confirm('ยืนยันการส่งหลักฐาน');" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="amount" value="{{ $amount }}">
                <input type="hidden" name="coins_to_receive" value="{{ $coins }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">แนบรูปสลิปหลักฐาน</label>
                    <input type="file" name="slip_image" required 
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700 transition shadow-lg">
                    ยืนยันการโอนเงิน
                </button>
            </form>
        </div>
    </div>
</x-app-layout>