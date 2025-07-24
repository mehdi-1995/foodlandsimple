@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-4">رستوران‌ها</h2>
        <div class="flex justify-center gap-4 mb-4">
            <button class="category-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full" data-category="all">همه</button>
            <button class="category-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full"
                data-category="فست‌فود">فست‌فود</button>
            <button class="category-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full"
                data-category="سنتی">سنتی</button>
            <button class="category-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full"
                data-category="کافه">کافه</button>
        </div>
        <input type="text" id="searchInput" class="w-full p-2 mb-4 border rounded" placeholder="جستجوی رستوران...">
        <div id="restaurantList" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($restaurants as $restaurant)
                <div class="restaurant-card bg-white rounded-lg shadow-md p-4">
                    <img src="{{ $restaurant->image ?? '/images/placeholder.jpg' }}" alt="{{ $restaurant->name }}"
                        class="w-full h-40 object-cover rounded-lg">
                    <h3 class="text-lg font-bold mt-2">{{ $restaurant->name }}</h3>
                    <p class="text-gray-600">{{ $restaurant->category }}</p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="ml-1">{{ $restaurant->rating }} ({{ $restaurant->reviews_count }} نظر)</span>
                    </div>
                    <p class="text-gray-500 mt-2">هزینه ارسال: {{ number_format($restaurant->delivery_cost) }} تومان</p>
                    <a href="{{ route('restaurants.show', $restaurant->id) }}"
                        class="mt-4 block bg-pink-600 text-white px-4 py- astronomy2 rounded-full text-center">مشاهده
                        منو</a>
                </div>
            @endforeach
        </div>
        <div class="pagination mt-4">
            {{ $restaurants->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
