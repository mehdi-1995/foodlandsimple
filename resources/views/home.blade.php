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

@section('title', 'صفحه اصلی')

@section('content')
    <!-- دسته‌بندی‌ها -->
    <section class="my-6">
        <div class="flex space-x-4 overflow-x-auto">
            <button class="category-filter px-4 py-2 bg-pink-600 text-white rounded-full" data-category="all">همه</button>
            <button class="category-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-full"
                data-category="restaurant">رستوران‌ها</button>
            <button class="category-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-full"
                data-category="cafe">کافه</button>
            <button class="category-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-full"
                data-category="bakery">شیرینی‌فروشی</button>
            <button class="category-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-full"
                data-category="supermarket">سوپرمارکت</button>
        </div>
    </section>

    <!-- لیست رستوران‌ها -->
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">رستوران‌های نزدیک شما</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="restaurantList">
            @foreach ($restaurants as $restaurant)
                <div class="restaurant-card bg-white rounded-lg shadow-md p-4">
                    <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}"
                        class="w-full h-40 object-cover rounded-lg">
                    <h3 class="text-lg font-bold mt-2">{{ $restaurant->name }}</h3>
                    <p class="text-gray-600">{{ $restaurant->category }}</p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="ml-1">{{ $restaurant->rating }} ({{ $restaurant->reviews_count }} نظر)</span>
                    </div>
                    <p class="text-gray-500 mt-2">هزینه ارسال: {{ number_format($restaurant->delivery_cost) }} تومان</p>
                    <a href="{{ route('restaurants.show', $restaurant->id) }}"
                        class="mt-4 block bg-pink-600 text-white px-4 py-2 rounded-full text-center">مشاهده منو</a>
                </div>
            @endforeach
        </div>
    </section>
@endsection
