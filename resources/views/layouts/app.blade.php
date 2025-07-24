<script type="text/javascript">
    var gk_isXlsx = false;
    var gk_xlsxFileLookup = {};
    var gk_fileData = {};

    function filledCell(cell) {
        return cell !== '' && cell != null;
    }

    function loadFileData(filename) {
        if (gk_isXlsx && gk_xlsxFileLookup[filename]) {
            try {
                var workbook = XLSX.read(gk_fileData[filename], {
                    type: 'base64'
                });
                var firstSheetName = workbook.SheetNames[0];
                var worksheet = workbook.Sheets[firstSheetName];

                // Convert sheet to JSON to filter blank rows
                var jsonData = XLSX.utils.sheet_to_json(worksheet, {
                    header: 1,
                    blankrows: false,
                    defval: ''
                });
                // Filter out blank rows (rows where all cells are empty, null, or undefined)
                var filteredData = jsonData.filter(row => row.some(filledCell));

                // Heuristic to find the header row by ignoring rows with fewer filled cells than the next row
                var headerRowIndex = filteredData.findIndex((row, index) =>
                    row.filter(filledCell).length >= filteredData[index + 1]?.filter(filledCell).length
                );
                // Fallback
                if (headerRowIndex === -1 || headerRowIndex > 25) {
                    headerRowIndex = 0;
                }

                // Convert filtered JSON back to CSV
                var csv = XLSX.utils.aoa_to_sheet(filteredData.slice(
                    headerRowIndex)); // Create a new sheet from filtered array of arrays
                csv = XLSX.utils.sheet_to_csv(csv, {
                    header: 1
                });
                return csv;
            } catch (e) {
                console.error(e);
                return "";
            }
        }
        return gk_fileData[filename] || "";
    }
</script>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body class="bg-gray-100">
    <!-- هدر -->
    <header class=" 

class="bg-white shadow-md p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold text-pink-600">لوگوی وب‌سایت</div>
            <div class="search-bar flex items-center w-1/2">
                <input type="text" id="searchInput" placeholder="جستجوی رستوران، غذا یا محله..."
                    class="w-full bg-transparent outline-none">
                <i class="fas fa-search text-gray-500"></i>
            </div>
            <div class="flex items-center space-x-4">
                <!-- در بخش ناویگیشن layouts/app.blade.php -->
                <div class="cart-icon">
                    <a href="{{ route('cart') }}" class="flex items-center">
                        <i class="fas fa-shopping-cart text-2xl text-gray-700"></i>
                        <span id="cartCount" class="badge ml-1">
                            {{ auth()->check() ? \App\Models\Cart::where('user_id', auth()->id())->count() : 0 }}
                        </span>
                    </a>
                </div>
                @auth
                    <a href="{{ route('profile') }}" class="bg-pink-600 text-white px-4 py-2 rounded-full">پروفایل</a>
                @else
                    <button id="loginBtn" class="bg-pink-600 text-white px-4 py-2 rounded-full">ورود / ثبت‌نام</button>
                @endauth
            </div>
        </div>
    </header>

    <!-- مودال ورود/ثبت‌نام -->
    <div id="loginModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">ورود / ثبت‌نام</h2>
            <form id="loginForm" action="{{ route('login') }}" method="POST">
                @csrf
                <input type="text" id="phone" name="phone" placeholder="شماره موبایل"
                    class="w-full p-2 mb-4 border rounded">
                <input type="password" id="password" name="password" placeholder="رمز عبور"
                    class="w-full p-2 mb-4 border rounded">
                <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-full w-full">ورود</button>
            </form>
            <button id="closeModal" class="mt-4 text-gray-600">بستن</button>
        </div>
    </div>

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
