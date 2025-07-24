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
        </script>
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-4 text-center">ورود به حساب کاربری</h2>
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        @if (session('error'))
            <div id="flash-message" class="bg-red-100 text-red-700 p-4 rounded mb-4 text-center">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div id="flash-message" class="bg-red-100 text-red-700 p-4 rounded mb-4 text-center">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="phone" class="block text-gray-700">شماره موبایل</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required autofocus class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">رمز عبور</label>
                <input type="password" id="password" name="password" required class="w-full p-2 border rounded">
            </div>
            <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-full w-full">ورود</button>
        </form>
        <p class="mt-4 text-center">
            حساب کاربری ندارید؟ <a href="{{ route('register') }}" class="text-pink-600 hover:underline">ثبت‌نام کنید</a>
        </p>
    </div>
</div>
@endsection
