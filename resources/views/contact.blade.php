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

@section('title', 'تماس با ما')

@section('content')
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">تماس با ما</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 mb-4">برای ارتباط با ما می‌توانید از اطلاعات زیر استفاده کنید یا فرم تماس را پر کنید.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-bold">اطلاعات تماس</h3>
                    <p class="mt-2"><strong>ایمیل:</strong> support@example.com</p>
                    <p class="mt-2"><strong>تلفن:</strong> 021-12345678</p>
                    <p class="mt-2"><strong>آدرس:</strong> تهران، خیابان نمونه، پلاک 123</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold">ساعات کاری</h3>
                    <p class="mt-2">شنبه تا پنج‌شنبه: 9 صبح تا 9 شب</p>
                    <p class="mt-2">جمعه: 12 ظهر تا 6 عصر</p>
                </div>
            </div>

            <h3 class="text-lg font-bold mb-4">فرم تماس</h3>
            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-gray-700">نام</label>
                    <input type="text" id="name" name="name" class="w-full p-2 border rounded" placeholder="نام شما">
                </div>
                <div>
                    <label for="email" class="block text-gray-700">ایمیل</label>
                    <input type="email" id="email" name="email" class="w-full p-2 border rounded" placeholder="ایمیل شما">
                </div>
                <div>
                    <label for="message" class="block text-gray-700">پیام</label>
                    <textarea id="message" name="message" class="w-full p-2 border rounded" rows="5" placeholder="پیام خود را بنویسید..."></textarea>
                </div>
                <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-full">ارسال پیام</button>
            </form>
        </div>
    </section>
@endsection