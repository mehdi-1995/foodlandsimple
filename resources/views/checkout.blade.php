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

@section('title', 'تسویه حساب')

@section('content')
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">تسویه حساب</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div id="checkoutItems"></div>
            <p class="text-lg font-bold mt-4">مجموع: <span id="totalPrice">0</span> تومان</p>
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ request()->query('restaurant_id') ?? 1 }}">
                <div class="mt-4">
                    <h3 class="text-lg font-bold">روش پرداخت</h3>
                    <label class="block mt-2">
                        <input type="radio" name="payment_method" value="online" checked> پرداخت آنلاین
                    </label>
                    <label class="block mt-2">
                        <input type="radio" name="payment_method" value="cod"> پرداخت در محل
                    </label>
                </div>
                <div class="mt-4">
                    <label for="address" class="block text-lg font-bold">آدرس</label>
                    <input type="text" id="address" name="address" placeholder="آدرس تحویل"
                        class="w-full p-2 border rounded">
                </div>
                <button type="submit" id="confirmOrder" class="mt-4 bg-pink-600 text-white px-4 py-2 rounded-full">تأیید
                    سفارش</button>
            </form>
        </div>
    </section>
@endsection
