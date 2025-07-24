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

@section('title', 'قوانین و مقررات')

@section('content')
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">قوانین و مقررات</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 mb-4">لطفاً قبل از استفاده از خدمات ما، قوانین و مقررات زیر را مطالعه کنید.</p>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-bold">1. شرایط استفاده</h3>
                    <p class="text-gray-600 mt-2">با ثبت‌نام و استفاده از خدمات وب‌سایت، شما موافقت خود را با این قوانین اعلام می‌کنید.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold">2. ثبت سفارش</h3>
                    <p class="text-gray-600 mt-2">سفارشات شما پس از تأیید پرداخت یا انتخاب پرداخت در محل پردازش می‌شوند. لطفاً اطلاعات دقیق آدرس و شماره تماس را وارد کنید.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold">3. لغو سفارش</h3>
                    <p class="text-gray-600 mt-2">امکان لغو سفارش تا قبل از آماده‌سازی توسط رستوران وجود دارد. برای لغو، با پشتیبانی تماس بگیرید.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold">4. مسئولیت‌ها</h3>
                    <p class="text-gray-600 mt-2">ما مسئول کیفیت غذا یا خدمات ارائه‌شده توسط رستوران‌ها نیستیم، اما در صورت بروز مشکل، پشتیبانی لازم را ارائه می‌دهیم.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold">5. حریم خصوصی</h3>
                    <p class="text-gray-600 mt-2">اطلاعات شخصی شما طبق سیاست حریم خصوصی ما محافظت می‌شود و با اشخاص ثالث به اشتراک گذاشته نمی‌شود.</p>
                </div>
            </div>
        </div>
    </section>
@endsection