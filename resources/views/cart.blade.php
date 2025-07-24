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

@section('title', 'سبد خرید')

@section('content')
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">سبد خرید</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            @if ($cartItems->isEmpty())
                <p class="text-gray-600">سبد خرید شما خالی است.</p>
            @else
                <div id="cartItems" class="space-y-4">
                    @foreach ($cartItems as $item)
                        <div class="flex justify-between items-center border-b py-2">
                            <div>
                                <h3 class="text-lg font-bold">{{ $item->menuItem->name }}</h3>
                                <p class="text-gray-600">{{ number_format($item->menuItem->price) }} تومان ×
                                    {{ $item->quantity }}</p>
                            </div>
                            <div class="flex items-center">
                                <button class="increment-quantity px-2" data-id="{{ $item->id }}">+</button>
                                <span class="mx-2">{{ $item->quantity }}</span>
                                <button class="decrement-quantity px-2" data-id="{{ $item->id }}">-</button>
                                <button class="remove-item text-red-600 mr-4" data-id="{{ $item->id }}">حذف</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <p class="text-lg font-bold">مجموع: <span
                            id="totalPrice">{{ number_format($cartItems->sum(function ($item) {return $item->menuItem->price * $item->quantity;})) }}</span>
                        تومان</p>
                    <a href="{{ route('checkout') }}"
                        class="mt-4 inline-block bg-pink-600 text-white px-4 py-2 rounded-full">تسویه حساب</a>
                </div>
            @endif
        </div>
    </section>
@endsection
