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
                var workbook = XLSX.read(gk_fileData[filename], { type: 'base64' });
                var firstSheetName = workbook.SheetNames[0];
                var worksheet = workbook.Sheets[firstSheetName];

                // Convert sheet to JSON to filter blank rows
                var jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1, blankrows: false, defval: '' });
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
                var csv = XLSX.utils.aoa_to_sheet(filteredData.slice(headerRowIndex)); // Create a new sheet from filtered array of arrays
                csv = XLSX.utils.sheet_to_csv(csv, { header: 1 });
                return csv;
            } catch (e) {
                console.error(e);
                return "";
            }
        }
        return gk_fileData[filename] || "";
        }
        </script>@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-4">سفارشات شما</h2>
    @if ($orders->isEmpty())
        <p class="text-gray-600">شما هنوز سفارشی ثبت نکرده‌اید.</p>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach ($orders as $order)
                <div class="order bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-bold">سفارش شماره {{ $order->id }}</h3>
                    <p class="text-gray-600">تاریخ: {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    <p class="text-gray-600">وضعیت: {{ $order->status === 'pending' ? 'در انتظار' : $order->status === 'preparing' ? 'در حال آماده‌سازی' : $order->status === 'shipped' ? 'ارسال شده' : 'تحویل داده شده' }}</p>
                    <p class="text-gray-600">مجموع: {{ number_format($order->total) }} تومان</p>
                    <h4 class="text-md font-bold mt-2">آیتم‌ها:</h4>
                    <ul class="list-disc mr-4">
                        @foreach ($order->items as $item)
                            <li>{{ $item->menuItem->name }} - {{ number_format($item->menuItem->price) }} تومان × {{ $item->quantity }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection