@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-4">سبد خرید</h2>
        @if (session('success'))
            <div id="flash-message" class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if ($cartItems->isEmpty())
            <p class="text-gray-600">سبد خرید شما خالی است.</p>
        @else
            <div id="cartItems" class="grid grid-cols-1 gap-4">
                @foreach ($cartItems as $item)
                    <div class="bg-white rounded-lg shadow-md p-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">{{ $item->menuItem->name }}</h3>
                            <p class="text-gray-600">{{ number_format($item->menuItem->price) }} تومان ×
                                {{ $item->quantity }}</p>
                        </div>
                        <div class="flex items-center">
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PUT')
                                <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                    class="increment-quantity px-2">+</button>
                                <span class="mx-2">{{ $item->quantity }}</span>
                                <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}"
                                    class="decrement-quantity px-2" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                            </form>
                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="remove-item text-red-600 mr-4">حذف</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                <p class="text-lg font-bold">مجموع: {{ number_format($totalPrice) }} تومان</p>
                <a href="{{ route('checkout') }}"
                    class="bg-pink-600 text-white px-4 py-2 rounded-full mt-2 inline-block">نهایی کردن خرید</a>
            </div>
        @endif
    </div>
@endsection
