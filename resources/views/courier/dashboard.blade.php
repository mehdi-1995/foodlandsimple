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

@section('title', 'داشبورد پیک')

@section('content')
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">داشبورد پیک</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold">سفارشات تخصیص‌یافته</h3>
            <div id="courierOrders">
                @foreach ($orders as $order)
                    <div class="border-b py-2">
                        <h3 class="text-lg font-bold">سفارش شماره {{ $order->id }}</h3>
                        <p class="text-gray-600">تاریخ:
                            {{ \Carbon\Carbon::parse($order->created_at)->locale('fa')->toDateTimeString() }}</p>
                        <p class="text-gray-600">مجموع: {{ number_format($order->total) }} تومان</p>
                        <p class="text-gray-600">آدرس: {{ $order->address }}</p>
                        <p class="text-gray-600">وضعیت: {{ $order->status === 'shipped' ? 'ارسال شده' : 'در انتظار' }}</p>
                        <form action="{{ route('courier.orders.status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" class="update-courier-status p-2 border rounded"
                                data-order-id="{{ $order->id }}">
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>ارسال شده
                                </option>
                                <option value="delivered">تحویل داده شده</option>
                            </select>
                            <button type="submit"
                                class="mt-2 bg-pink-600 text-white px-4 py-2 rounded-full">به‌روزرسانی</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
