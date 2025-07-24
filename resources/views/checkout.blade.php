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
            <div id="checkoutItems" class="space-y-4 mb-6">
                @foreach ($cartItems as $item)
                    <div class="flex justify-between items-center border-b py-2">
                        <div>
                            <h3 class="text-lg font-bold">{{ $item->menuItem->name }}</h3>
                            <p class="text-gray-600">{{ number_format($item->menuItem->price) }} تومان ×
                                {{ $item->quantity }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-lg font-bold mb-4">مجموع: <span
                    id="totalPrice">{{ number_format($cartItems->sum(function ($item) {return $item->menuItem->price * $item->quantity;})) }}</span>
                تومان</p>

            <form action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="address" class="block text-gray-700">آدرس</label>
                    <input type="text" id="address" name="address" class="w-full p-2 border rounded"
                        value="{{ Auth::user()->address }}" required>
                    @error('address')
                        <p class="text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <h3 class="text-lg font-bold">روش پرداخت</h3>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_method" value="online" class="form-radio" required>
                            <span class="mr-2">پرداخت آنلاین</span>
                        </label>
                        <label class="inline-flex items-center mr-4">
                            <input type="radio" name="payment_method" value="cod" class="form-radio">
                            <span class="mr-2">پرداخت در محل</span>
                        </label>
                    </div>
                    @error('payment_method')
                        <p class="text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" id="confirmOrder" class="bg-pink-600 text-white px-4 py-2 rounded-full">تأیید
                    سفارش</button>
            </form>
        </div>
    </section>
@endsection
