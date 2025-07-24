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

@section('title', 'سوالات متداول')

@section('content')
    <section class="my-6">
        <h2 class="text-2xl font-bold mb-4">سوالات متداول</h2>
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-600 mb-4">پاسخ به سوالات رایج درباره خدمات ما</p>
            
            <div class="space-y-4">
                <div class="border-b pb-4">
                    <h3 class="text-lg font-bold">چگونه می‌توانم سفارش خود را ثبت کنم؟</h3>
                    <p class="text-gray-600 mt-2">برای ثبت سفارش، ابتدا وارد حساب کاربری خود شوید، رستوران مورد نظر را انتخاب کنید، آیتم‌های منو را به سبد خرید اضافه کنید و سپس به صفحه تسویه حساب بروید.</p>
                </div>
                <div class="border-b pb-4">
                    <h3 class="text-lg font-bold">هزینه ارسال چگونه محاسبه می‌شود؟</h3>
                    <p class="text-gray-600 mt-2">هزینه ارسال بسته به رستوران و فاصله شما از آن متفاوت است. این مبلغ در صفحه رستوران نمایش داده می‌شود.</p>
                </div>
                <div class="border-b pb-4">
                    <h3 class="text-lg font-bold">آیا امکان پرداخت در محل وجود دارد؟</h3>
                    <p class="text-gray-600 mt-2">بله، می‌توانید گزینه پرداخت در محل را در مرحله تسویه حساب انتخاب کنید.</p>
                </div>
                <div class="border-b pb-4">
                    <h3 class="text-lg font-bold">چگونه می‌توانم با پشتیبانی تماس بگیرم؟</h3>
                    <p class="text-gray-600 mt-2">شما می‌توانید از طریق فرم تماس در صفحه "تماس با ما" یا با ایمیل support@example.com و شماره 021-12345678 با ما در ارتباط باشید.</p>
                </div>
            </div>
        </div>
    </section>
@endsection