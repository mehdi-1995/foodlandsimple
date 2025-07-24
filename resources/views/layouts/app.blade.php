<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ auth()->id() ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'اپلیکیشن غذا')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body class="bg-gray-100">
    <!-- هدر -->
    <header class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold text-pink-600">
                <a href="{{ route('home') }}">لوگوی وب‌سایت</a>
            </div>
            <div class="search-bar flex items-center w-1/2">
                <input type="text" id="searchInput" placeholder="جستجوی رستوران، غذا یا محله..."
                    class="w-full bg-transparent outline-none">
                <i class="fas fa-search text-gray-500"></i>
            </div>
            <div class="flex items-center space-x-4">
                <div class="cart-icon">
                    <a href="{{ route('cart') }}" class="flex items-center">
                        <i class="fas fa-shopping-cart text-2xl text-gray-700"></i>
                        <span id="cartCount" class="badge ml-1">
                            {{ auth()->check() ? \App\Models\Cart::where('user_id', auth()->id())->count() : 0 }}
                        </span>
                    </a>
                </div>
                @auth
                    <div class="flex items-center gap-2">
                        <a href="{{ route('profile') }}" class="bg-pink-600 text-white px-4 py-2 rounded-full">پروفایل</a>
                        <form action="{{ route('logout') }}" method="POST" class="logout-form">
                            @csrf
                            <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-full">خروج</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" id="loginBtn" class="bg-pink-600 text-white px-4 py-2 rounded-full">ورود
                        / ثبت‌نام</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- محتوای اصلی -->
    <main class="container mx-auto my-6">
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <!-- فوتر -->
    <footer class="bg-gray-800 text-white p-6">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-bold">درباره ما</h3>
                    <p class="mt-2">وب‌سایت سفارش آنلاین غذا با هدف ارائه بهترین خدمات.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold">لینک‌های مفید</h3>
                    <ul class="mt-2">
                        <li><a href="{{ route('contact') }}" class="hover:underline">تماس با ما</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:underline">سوالات متداول</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:underline">قوانین و مقررات</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold">تماس با ما</h3>
                    <p class="mt-2">ایمیل: support@example.com</p>
                    <p>تلفن: 021-12345678</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
