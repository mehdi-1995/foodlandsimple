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

@section('title', 'داشبورد فروشنده')

@section('content')
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">داشبورد فروشنده</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold">اطلاعات رستوران</h3>
            <p class="mt-2">نام: {{ $restaurant->name }}</p>
            <p class="mt-2">دسته‌بندی: {{ $restaurant->category }}</p>
            <button id="editVendor" class="mt-4 bg-pink-600 text-white px-4 py-2 rounded-full">ویرایش اطلاعات</button>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-bold">مدیریت سفارشات</h3>
            <div id="vendorOrders">
                @foreach ($orders as $order)
                    <div class="border-b py-2">
                        <h3 class="text-lg font-bold">سفارش شماره {{ $order->id }}</h3>
                        <p class="text-gray-600">تاریخ:
                            {{ \Carbon\Carbon::parse($order->created_at)->locale('fa')->toDateTimeString() }}</p>
                        <p class="text-gray-600">مجموع: {{ number_format($order->total) }} تومان</p>
                        <p class="text-gray-600">وضعیت:
                            {{ (($order->status === 'pending' ? 'در انتظار' : $order->status === 'preparing') ? 'در حال آماده‌سازی' : $order->status === 'shipped') ? 'ارسال شده' : 'تحویل داده شده' }}
                        </p>
                        <form action="{{ route('vendor.orders.status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <select name="status" class="update-status p-2 border rounded"
                                data-order-id="{{ $order->id }}">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>در انتظار
                                </option>
                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>در حال
                                    آماده‌سازی</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>ارسال شده
                                </option>
                            </select>
                            <button type="submit"
                                class="mt-2 bg-pink-600 text-white px-4 py-2 rounded-full">به‌روزرسانی</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-bold">مدیریت منو</h3>
            <form id="menuForm" action="{{ route('vendor.menu.store') }}" method="POST" class="mb-4">
                @csrf
                <input type="text" id="menuItemName" name="name" placeholder="نام غذا"
                    class="w-full p-2 mb-2 border rounded">
                <input type="text" id="menuItemPrice" name="price" placeholder="قیمت (تومان)"
                    class="w-full p-2 mb-2 border rounded">
                <input type="text" id="menuItemDescription" name="description" placeholder="توضیحات"
                    class="w-full p-2 mb-2 border rounded">
                <select id="menuItemCategory" name="category" class="w-full p-2 mb-2 border rounded">
                    <option value="appetizer">پیش‌غذا</option>
                    <option value="main">غذای اصلی</option>
                    <option value="dessert">دسر</option>
                </select>
                <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-full">افزودن غذا</button>
            </form>
            <div id="vendorMenu">
                @foreach ($menuItems as $item)
                    <div class="border-b py-2">
                        <h3 class="text-lg font-bold">{{ $item->name }}</h3>
                        <p class="text-gray-600">{{ $item->description }}</p>
                        <p class="text-gray-600">{{ number_format($item->price) }} تومان</p>
                        <form action="{{ route('vendor.menu.delete', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">حذف</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
