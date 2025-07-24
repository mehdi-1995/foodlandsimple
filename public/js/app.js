const apiBaseUrl = 'http://localhost:8000/api';
let cart = JSON.parse(localStorage.getItem('cart')) || [];
const cartBadge = document.querySelector('#cartCount');
const token = document.querySelector('meta[name="csrf-token"]').content;

// به‌روزرسانی تعداد آیتم‌های سبد خرید
function updateCartCount() {
    if (cartBadge) cartBadge.textContent = cart.length;
    localStorage.setItem('cart', JSON.stringify(cart));
}

// نمایش رستوران‌ها
async function displayRestaurants(filter = 'all') {
    const restaurantList = document.querySelector('#restaurantList');
    if (!restaurantList) return;
    const params = new URLSearchParams({ type: filter });
    const response = await fetch(`${apiBaseUrl}/restaurants?${params}`);
    const restaurants = await response.json();
    restaurantList.innerHTML = '';
    restaurants.forEach(restaurant => {
        const card = document.createElement('div');
        card.className = 'restaurant-card bg-white rounded-lg shadow-md p-4';
        card.innerHTML = `
            <img src="${restaurant.image}" alt="${restaurant.name}" class="w-full h-40 object-cover rounded-lg">
            <h3 class="text-lg font-bold mt-2">${restaurant.name}</h3>
            <p class="text-gray-600">${restaurant.category}</p>
            <div class="flex items-center mt-2">
                <i class="fas fa-star text-yellow-400"></i>
                <span class="ml-1">${restaurant.rating} (${restaurant.reviews_count} نظر)</span>
            </div>
            <p class="text-gray-500 mt-2">هزینه ارسال: ${restaurant.delivery_cost.toLocaleString()} تومان</p>
            <a href="${window.location.origin}/restaurants/${restaurant.id}" class="mt-4 block bg-pink-600 text-white px-4 py-2 rounded-full text-center">مشاهده منو</a>
        `;
        restaurantList.appendChild(card);
    });
}

// نمایش جزئیات رستوران
async function displayRestaurantDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const restaurantId = parseInt(urlParams.get('id'));
    const response = await fetch(`${apiBaseUrl}/restaurants/${restaurantId}`);
    const restaurant = await response.json();
    if (!restaurant) return;
    document.querySelector('#restaurantImage').src = restaurant.image;
    document.querySelector('#restaurantName').textContent = restaurant.name;
    document.querySelector('#restaurantCategory').textContent = restaurant.category;
    document.querySelector('#restaurantRating').textContent = `${restaurant.rating} (${restaurant.reviews_count} نظر)`;
    document.querySelector('#deliveryCost').textContent = `هزینه ارسال: ${restaurant.delivery_cost.toLocaleString()} تومان`;
    document.querySelector('#deliveryTime').textContent = `زمان تحویل: ${restaurant.delivery_time}`;
}

// نمایش منوی رستوران
async function displayMenu(filter = 'all') {
    const urlParams = new URLSearchParams(window.location.search);
    const restaurantId = parseInt(urlParams.get('id'));
    const menuList = document.querySelector('#menuList');
    if (!menuList) return;
    const response = await fetch(`${apiBaseUrl}/restaurants/${restaurantId}/menu`);
    let menuItems = await response.json();
    if (filter !== 'all') menuItems = menuItems.filter(item => item.category === filter);
    menuList.innerHTML = '';
    menuItems.forEach(item => {
        const menuItem = document.createElement('div');
        menuItem.className = 'menu-item bg-white rounded-lg shadow-md p-4 flex';
        menuItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="w-24 h-24 object-cover rounded-lg">
            <div class="mr-4">
                <h3 class="text-lg font-bold">${item.name}</h3>
                <p class="text-gray-600">${item.description}</p>
                <p class="text-gray-500">${item.price.toLocaleString()} تومان</p>
                <button class="add-to-cart mt-2 bg-pink-600 text-white px-4 py-2 rounded-full" data-id="${item.id}">افزودن به سبد خرید</button>
            </div>
        `;
        menuList.appendChild(menuItem);
    });

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const itemId = parseInt(button.dataset.id);
            const item = menuItems.find(i => i.id === itemId);
            cart.push({ ...item, quantity: 1 });
            updateCartCount();
            alert(`${item.name} به سبد خرید اضافه شد!`);
        });
    });
}

// نمایش سبد خرید
function displayCart() {
    const cartItems = document.querySelector('#cartItems');
    if (!cartItems) return;
    cartItems.innerHTML = '';
    let totalPrice = 0;
    cart.forEach((item, index) => {
        totalPrice += item.price * item.quantity;
        const cartItem = document.createElement('div');
        cartItem.className = 'flex justify-between items-center border-b py-2';
        cartItem.innerHTML = `
            <div>
                <h3 class="text-lg font-bold">${item.name}</h3>
                <p class="text-gray-600">${item.price.toLocaleString()} تومان × ${item.quantity}</p>
            </div>
            <div class="flex items-center">
                <button class="increment-quantity px-2" data-index="${index}">+</button>
                <span class="mx-2">${item.quantity}</span>
                <button class="decrement-quantity px-2" data-index="${index}">-</button>
                <button class="remove-item text-red-600 mr-4" data-index="${index}">حذف</button>
            </div>
        `;
        cartItems.appendChild(cartItem);
    });
    document.querySelector('#totalPrice').textContent = totalPrice.toLocaleString();
    document.querySelectorAll('.increment-quantity').forEach(button => {
        button.addEventListener('click', () => {
            const index = parseInt(button.dataset.index);
            cart[index].quantity++;
            updateCartCount();
            displayCart();
        });
    });
    document.querySelectorAll('.decrement-quantity').forEach(button => {
        button.addEventListener('click', () => {
            const index = parseInt(button.dataset.index);
            if (cart[index].quantity > 1) {
                cart[index].quantity--;
            } else {
                cart.splice(index, 1);
            }
            updateCartCount();
            displayCart();
        });
    });
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', () => {
            const index = parseInt(button.dataset.index);
            cart.splice(index, 1);
            updateCartCount();
            displayCart();
        });
    });
}

// نمایش آیتم‌های تسویه حساب
function displayCheckout() {
    const checkoutItems = document.querySelector('#checkoutItems');
    if (!checkoutItems) return;
    checkoutItems.innerHTML = '';
    let totalPrice = 0;
    cart.forEach(item => {
        totalPrice += item.price * item.quantity;
        const checkoutItem = document.createElement('div');
        checkoutItem.className = 'flex justify-between items-center border-b py-2';
        checkoutItem.innerHTML = `
            <div>
                <h3 class="text-lg font-bold">${item.name}</h3>
                <p class="text-gray-600">${item.price.toLocaleString()} تومان × ${item.quantity}</p>
            </div>
        `;
        checkoutItems.appendChild(checkoutItem);
    });
    document.querySelector('#totalPrice').textContent = totalPrice.toLocaleString();
}

// نمایش سفارشات فروشنده
async function displayVendorOrders() {
    const vendorOrders = document.querySelector('#vendorOrders');
    if (!vendorOrders) return;
    const response = await fetch(`${apiBaseUrl}/vendor/orders`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}`, 'X-CSRF-TOKEN': token }
    });
    const orders = await response.json();
    vendorOrders.innerHTML = '';
    orders.forEach(order => {
        const orderItem = document.createElement('div');
        orderItem.className = 'border-b py-2';
        orderItem.innerHTML = `
            <h3 class="text-lg font-bold">سفارش شماره ${order.id}</h3>
            <p class="text-gray-600">تاریخ: ${new Date(order.created_at).toLocaleString('fa-IR')}</p>
            <p class="text-gray-600">مجموع: ${order.total.toLocaleString()} تومان</p>
            <p class="text-gray-600">وضعیت: ${order.status === 'pending' ? 'در انتظار' : order.status === 'preparing' ? 'در حال آماده‌سازی' : order.status === 'shipped' ? 'ارسال شده' : 'تحویل داده شده'}</p>
            <select class="update-status p-2 border rounded" data-order-id="${order.id}">
                <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>در انتظار</option>
                <option value="preparing" ${order.status === 'preparing' ? 'selected' : ''}>در حال آماده‌سازی</option>
                <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''}>ارسال شده</option>
            </select>
        `;
        vendorOrders.appendChild(orderItem);
    });

    document.querySelectorAll('.update-status').forEach(select => {
        select.addEventListener('change', async () => {
            const orderId = parseInt(select.dataset.orderId);
            const newStatus = select.value;
            await fetch(`${apiBaseUrl}/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ status: newStatus })
            });
            displayVendorOrders();
            alert('وضعیت سفارش به‌روزرسانی شد!');
        });
    });
}

// نمایش منوی فروشنده
async function displayVendorMenu() {
    const vendorMenu = document.querySelector('#vendorMenu');
    if (!vendorMenu) return;
    const response = await fetch(`${apiBaseUrl}/restaurants/1/menu`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}`, 'X-CSRF-TOKEN': token }
    });
    const menuItems = await response.json();
    vendorMenu.innerHTML = '';
    menuItems.forEach(item => {
        const menuItem = document.createElement('div');
        menuItem.className = 'border-b py-2';
        menuItem.innerHTML = `
            <h3 class="text-lg font-bold">${item.name}</h3>
            <p class="text-gray-600">${item.description}</p>
            <p class="text-gray-600">${item.price.toLocaleString()} تومان</p>
            <button class="remove-menu-item text-red-600" data-id="${item.id}">حذف</button>
        `;
        vendorMenu.appendChild(menuItem);
    });

    document.querySelectorAll('.remove-menu-item').forEach(button => {
        button.addEventListener('click', async () => {
            const itemId = parseInt(button.dataset.id);
            await fetch(`${apiBaseUrl}/menu-items/${itemId}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}`, 'X-CSRF-TOKEN': token }
            });
            displayVendorMenu();
            alert('آیتم منو حذف شد!');
        });
    });
}

// نمایش سفارشات پیک
async function displayCourierOrders() {
    const courierOrders = document.querySelector('#courierOrders');
    if (!courierOrders) return;
    const response = await fetch(`${apiBaseUrl}/courier/orders`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}`, 'X-CSRF-TOKEN': token }
    });
    const orders = await response.json();
    courierOrders.innerHTML = '';
    orders.forEach(order => {
        const orderItem = document.createElement('div');
        orderItem.className = 'border-b py-2';
        orderItem.innerHTML = `
            <h3 class="text-lg font-bold">سفارش شماره ${order.id}</h3>
            <p class="text-gray-600">تاریخ: ${new Date(order.created_at).toLocaleString('fa-IR')}</p>
            <p class="text-gray-600">مجموع: ${order.total.toLocaleString()} تومان</p>
            <p class="text-gray-600">آدرس: ${order.address}</p>
            <p class="text-gray-600">وضعیت: ${order.status === 'shipped' ? 'ارسال شده' : 'در انتظار'}</p>
            <select class="update-courier-status p-2 border rounded" data-order-id="${order.id}">
                <option value="shipped" ${order.status === 'shipped' ? 'selected' : ''}>ارسال شده</option>
                <option value="delivered">تحویل داده شده</option>
            </select>
        `;
        courierOrders.appendChild(orderItem);
    });

    document.querySelectorAll('.update-courier-status').forEach(select => {
        select.addEventListener('change', async () => {
            const orderId = parseInt(select.dataset.orderId);
            const newStatus = select.value;
            await fetch(`${apiBaseUrl}/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ status: newStatus })
            });
            displayCourierOrders();
            alert('وضعیت سفارش به‌روزرسانی شد!');
        });
    });
}

// فیلتر دسته‌بندی‌های منو
document.querySelectorAll('.menu-filter').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.menu-filter').forEach(btn => btn.classList.replace('bg-pink-600', 'bg-gray-200'));
        document.querySelectorAll('.menu-filter').forEach(btn => btn.classList.replace('text-white', 'text-gray-700'));
        button.classList.replace('bg-gray-200', 'bg-pink-600');
        button.classList.replace('text-gray-700', 'text-white');
        displayMenu(button.dataset.category);
    });
});

// فیلتر دسته‌بندی‌های رستوران
document.querySelectorAll('.category-filter').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.category-filter').forEach(btn => btn.classList.replace('bg-pink-600', 'bg-gray-200'));
        document.querySelectorAll('.category-filter').forEach(btn => btn.classList.replace('text-white', 'text-gray-700'));
        button.classList.replace('bg-gray-200', 'bg-pink-600');
        button.classList.replace('text-gray-700', 'text-white');
        displayRestaurants(button.dataset.category);
    });
});

// جستجوی پیشرفته
document.querySelector('#searchInput')?.addEventListener('input', async function() {
    const query = this.value;
    const params = new URLSearchParams({ q: query });
    const response = await fetch(`${apiBaseUrl}/restaurants?${params}`);
    const restaurants = await response.json();
    const restaurantList = document.querySelector('#restaurantList');
    restaurantList.innerHTML = '';
    restaurants.forEach(restaurant => {
        const card = document.createElement('div');
        card.className = 'restaurant-card bg-white rounded-lg shadow-md p-4';
        card.innerHTML = `
            <img src="${restaurant.image}" alt="${restaurant.name}" class="w-full h-40 object-cover rounded-lg">
            <h3 class="text-lg font-bold mt-2">${restaurant.name}</h3>
            <p class="text-gray-600">${restaurant.category}</p>
            <div class="flex items-center mt-2">
                <i class="fas fa-star text-yellow-400"></i>
                <span class="ml-1">${restaurant.rating} (${restaurant.reviews_count} نظر)</span>
            </div>
            <p class="text-gray-500 mt-2">هزینه ارسال: ${restaurant.delivery_cost.toLocaleString()} تومان</p>
            <a href="${window.location.origin}/restaurants/${restaurant.id}" class="mt-4 block bg-pink-600 text-white px-4 py-2 rounded-full text-center">مشاهده منو</a>
        `;
        restaurantList.appendChild(card);
    });
});

// مودال ورود/ثبت‌نام
document.querySelector('#loginBtn')?.addEventListener('click', () => {
    document.querySelector('#loginModal').style.display = 'flex';
});
document.querySelector('#closeModal')?.addEventListener('click', () => {
    document.querySelector('#loginModal').style.display = 'none';
});
document.querySelector('#loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const phone = document.querySelector('#phone').value;
    const password = document.querySelector('#password').value;
    if (phone && password) {
        const response = await fetch(`${apiBaseUrl}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ phone, password })
        });
        const data = await response.json();
        if (response.ok) {
            localStorage.setItem('token', data.token);
            document.querySelector('#loginModal').style.display = 'none';
            document.querySelector('#loginBtn').textContent = 'پروفایل';
            document.querySelector('#loginBtn').onclick = () => window.location.href = '/profile';
            alert('ورود با موفقیت انجام شد!');
        } else {
            alert('خطا در ورود: ' + data.message);
        }
    } else {
        alert('لطفاً شماره موبایل و رمز عبور را وارد کنید.');
    }
});

// بارگذاری اولیه
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    displayRestaurants();
    displayRestaurantDetails();
    displayMenu();
    displayCart();
    displayCheckout();
    displayVendorOrders();
    displayVendorMenu();
    displayCourierOrders();
});