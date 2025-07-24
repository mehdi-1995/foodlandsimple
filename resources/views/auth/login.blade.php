@extends('layouts.app')

@section('title', 'ورود')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">ورود</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label for="phone" class="block mb-1">شماره موبایل</label>
            <input type="text" id="phone" name="phone" required autofocus class="w-full p-2 border rounded" value="{{ old('phone') }}">
        </div>
        <div class="mb-4">
            <label for="password" class="block mb-1">رمز عبور</label>
            <input type="password" id="password" name="password" required class="w-full p-2 border rounded">
        </div>
        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded w-full">ورود</button>
    </form>
</div>
@endsection
