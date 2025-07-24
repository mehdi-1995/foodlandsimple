@extends('layouts.app')

@section('title', 'ثبت‌نام')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">ثبت‌نام</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-4">
            <label for="name" class="block mb-1">نام</label>
            <input type="text" id="name" name="name" required class="w-full p-2 border rounded" value="{{ old('name') }}">
        </div>
        <div class="mb-4">
            <label for="email" class="block mb-1">ایمیل</label>
            <input type="email" id="email" name="email" required class="w-full p-2 border rounded" value="{{ old('email') }}">
        </div>
        <div class="mb-4">
            <label for="phone" class="block mb-1">شماره موبایل</label>
            <input type="text" id="phone" name="phone" required class="w-full p-2 border rounded" value="{{ old('phone') }}">
        </div>
        <div class="mb-4">
            <label for="password" class="block mb-1">رمز عبور</label>
            <input type="password" id="password" name="password" required class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="block mb-1">تکرار رمز عبور</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full p-2 border rounded">
        </div>
        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded w-full">ثبت‌نام</button>
    </form>
</div>
@endsection
