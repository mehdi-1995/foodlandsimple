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
</script>@extends('layouts.app')

@section('title', 'جزئیات رستوران')

@section('content')
    <!-- جزئیات رستوران -->
    <section class="my-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <img src="{{ $restaurant->image }}" id="restaurantImage" alt="{{ $restaurant->name }}"
                class="w-full h-48 object-cover rounded-lg">
            <h2 class="text-2xl font-bold mt-4">{{ $restaurant->name }}</h2>
            <p class="text-gray-600">{{ $restaurant->category }}</p>
            <div class="flex items-center mt-2">
                <i class="fas fa-star text-yellow-400"></i>
                <span class="ml-1">{{ $restaurant->rating }} ({{ $restaurant->reviews_count }} نظر)</span>
            </div>
            <p class="text-gray-500 mt-2">هزینه ارسال: {{ number_format($restaurant->delivery_cost) }} تومان</p>
            <p class="text-gray-500 mt-2">زمان تحویل: {{ $restaurant->delivery_time }}</p>
        </div>
    </section>

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- منوی غذا -->
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">منوی رستوران</h2>
        <div class="flex space-x-4 mb-4">
            <button class="menu-filter px-4 py-2 bg-pink-600 text-white rounded-full" data-category="all">همه</button>
            <button class="menu-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-full"
                data-category="appetizer">پیش‌غذا</button>
            <button class="menu-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-full" data-category="main">غذای
                اصلی</button>
            <button class="menu-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-full"
                data-category="dessert">دسر</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="menuList">
            @foreach ($restaurant->menuItems as $item)
                <div class="menu-item bg-white rounded-lg shadow-md p-4 flex" data-category="{{ $item->category }}">
                    <img src="{{ $item->image ?? '/images/placeholder.jpg' }}" alt="{{ $item->name }}"
                        class="w-24 h-24 object-cover rounded-lg">
                    <div class="mr-4">
                        <h3 class="text-lg font-bold">{{ $item->name }}</h3>
                        <p class="text-gray-600">{{ $item->description }}</p>
                        <p class="text-gray-500">{{ number_format($item->price) }} تومان</p>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                class="add-to-cart mt-2 bg-pink-600 text-white px-4 py-2 rounded-full">افزودن به سبد
                                خرید</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- نظرات کاربران -->
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">نظرات کاربران</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            @auth
                <form id="reviewForm" action="{{ route('restaurants.reviews.store', $restaurant->id) }}" method="POST">
                    @csrf
                    <textarea id="reviewText" name="text" placeholder="نظر خود را بنویسید..." class="w-full p-2 border rounded"></textarea>
                    <div class="flex items-center mt-2">
                        <label class="ml-2">امتیاز:</label>
                        <select id="reviewRating" name="rating" class="p-2 border rounded">
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                    <button type="submit" class="mt-4 bg-pink-600 text-white px-4 py-2 rounded-full">ارسال نظر</button>
                </form>
            @else
                <p class="text-gray-600">برای ارسال نظر، لطفاً وارد شوید.</p>
            @endauth
            <div id="reviewList">
                @foreach ($restaurant->reviews as $review)
                    <div class="border-b py-2">
                        <p class="text-gray-600">{{ $review->text }}</p>
                        <div class="flex items-center mt-1">
                            <i class="fas fa-star text-yellow-400"></i>
                            <span class="ml-1">{{ $review->rating }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
