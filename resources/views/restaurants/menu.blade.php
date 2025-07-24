@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-4">منوی {{ $restaurant->name }}</h2>
        <div class="menu-filters flex justify-center gap-4 mb-4">
            <button class="menu-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full" data-category="all">همه</button>
            <button class="menu-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full" data-category="پیتزا">پیتزا</button>
            <button class="menu-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full"
                data-category="ساندویچ">ساندویچ</button>
            <button class="menu-filter bg-gray-200 text-gray-700 px-4 py-2 rounded-full"
                data-category="نوشیدنی">نوشیدنی</button>
        </div>
        <div id="menuList" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($menuItems as $item)
                <div class="menu-item bg-white rounded-lg shadow-md p-4 flex" data-category="{{ $item->category }}">
                    <img src="{{ $item->image ?? '/images/placeholder.jpg' }}" alt="{{ $item->name }}"
                        class="w-24 h-24 object-cover rounded-lg">
                    <div class="mr-4">
                        <h3 class="text-lg font-bold">{{ $item->name }}</h3>
                        <p class="text-gray-600">{{ $item->description }}</p>
                        <p class="text-gray-500">{{ number_format($item->price) }} تومان</p>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                class="add-to-cart mt-2 bg-pink-600 text-white px-4 py-2 rounded-full">افزودن به سبد
                                خرید</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pagination mt-4">
            {{ $menuItems->links() }}
        </div>
    </div>
@endsection
