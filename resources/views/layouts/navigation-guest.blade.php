<nav class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-indigo-600">GoodRead</a>
            </div>

            <div class="flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">หน้าหลัก</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700">เข้าสู่ระบบ</a>
                        <a href="{{ route('register') }}" class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">สมัครสมาชิก</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>