const apiBaseUrl = "http://127.0.0.1:8000/api";
const token = document.querySelector('meta[name="csrf-token"]')?.content;

if (!token) {
    console.error("CSRF token not found");
}

async function getCsrfToken() {
    try {
        const response = await fetch(
            "http://127.0.0.1:8000/sanctum/csrf-cookie",
            {
                method: "GET",
                credentials: "include",
            }
        );
        if (!response.ok) {
            throw new Error("Failed to fetch CSRF token");
        }
    } catch (error) {
        console.error("Error fetching CSRF token:", error);
    }
}

async function updateCartCount() {
    try {
        await getCsrfToken();
        const response = await fetch(`${apiBaseUrl}/cart/count`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "X-CSRF-TOKEN": token,
            },
        });
        if (!response.ok) {
            throw new Error(`Failed to fetch cart count: ${response.status}`);
        }
        const { count } = await response.json();
        const cartBadge = document.querySelector("#cartCount");
        if (cartBadge) cartBadge.textContent = count;
    } catch (error) {
        console.error("Error updating cart count:", error);
    }
}

async function displayRestaurants(filter = "all") {
    const restaurantList = document.querySelector("#restaurantList");
    if (!restaurantList) return;
    try {
        const params = new URLSearchParams({ type: filter });
        const response = await fetch(`${apiBaseUrl}/restaurants?${params}`);
        if (!response.ok) {
            throw new Error(`Failed to fetch restaurants: ${response.status}`);
        }
        const restaurants = await response.json();
        restaurantList.innerHTML = "";
        restaurants.forEach((restaurant) => {
            const card = document.createElement("div");
            card.className =
                "restaurant-card bg-white rounded-lg shadow-md p-4";
            card.innerHTML = `
                <img src="${
                    restaurant.image || "/images/placeholder.jpg"
                }" alt="${
                restaurant.name
            }" class="w-full h-40 object-cover rounded-lg">
                <h3 class="text-lg font-bold mt-2">${restaurant.name}</h3>
                <p class="text-gray-600">${restaurant.category}</p>
                <div class="flex items-center mt-2">
                    <i class="fas fa-star text-yellow-400"></i>
                    <span class="ml-1">${restaurant.rating} (${
                restaurant.reviews_count
            } نظر)</span>
                </div>
                <p class="text-gray-500 mt-2">هزینه ارسال: ${restaurant.delivery_cost.toLocaleString()} تومان</p>
                <a href="${window.location.origin}/restaurants/${
                restaurant.id
            }" class="mt-4 block bg-pink-600 text-white px-4 py-2 rounded-full text-center">مشاهده منو</a>
            `;
            restaurantList.appendChild(card);
        });
    } catch (error) {
        console.error("Error fetching restaurants:", error);
    }
}

async function displayRestaurantDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const restaurantId = parseInt(urlParams.get("id"));
    if (isNaN(restaurantId)) {
        console.warn("No restaurant ID provided in URL");
        return;
    }
    try {
        const response = await fetch(
            `${apiBaseUrl}/restaurants/${restaurantId}`
        );
        if (!response.ok) {
            throw new Error(`Failed to fetch restaurant: ${response.status}`);
        }
        const restaurant = await response.json();
        if (!restaurant) return;
        document.querySelector("#restaurantImage").src =
            restaurant.image || "/images/placeholder.jpg";
        document.querySelector("#restaurantName").textContent = restaurant.name;
        document.querySelector("#restaurantCategory").textContent =
            restaurant.category;
        document.querySelector(
            "#restaurantRating"
        ).textContent = `${restaurant.rating} (${restaurant.reviews_count} نظر)`;
        document.querySelector(
            "#deliveryCost"
        ).textContent = `هزینه ارسال: ${restaurant.delivery_cost.toLocaleString()} تومان`;
        document.querySelector(
            "#deliveryTime"
        ).textContent = `زمان تحویل: ${restaurant.delivery_time}`;
    } catch (error) {
        console.error("Error fetching restaurant details:", error);
    }
}

function setupAddToCartButtons() {
    const addToCartButtons = document.querySelectorAll(".add-to-cart");
    console.log(`Found ${addToCartButtons.length} add-to-cart buttons`);
    addToCartButtons.forEach((button) => {
        button.addEventListener("click", async () => {
            const itemId = parseInt(button.dataset.id);
            console.log("Add to cart clicked, item ID:", itemId);
            const tokenAuth = localStorage.getItem("token");
            if (!tokenAuth) {
                alert("لطفاً ابتدا وارد حساب کاربری خود شوید.");
                const loginModal = document.querySelector("#loginModal");
                if (loginModal) loginModal.style.display = "flex";
                return;
            }
            try {
                await getCsrfToken();
                const response = await fetch(`${apiBaseUrl}/cart/add`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${tokenAuth}`,
                        "X-CSRF-TOKEN": token,
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ menu_item_id: itemId, quantity: 1 }),
                    credentials: "include",
                });
                const data = await response.json();
                if (response.ok) {
                    await updateCartCount();
                    alert(data.message || "آیتم به سبد خرید اضافه شد!");
                } else {
                    console.error("Server response:", data);
                    alert(
                        `خطا: ${
                            data.message ||
                            "مشکلی در افزودن به سبد خرید رخ داد."
                        }`
                    );
                }
            } catch (error) {
                console.error("Error adding to cart:", error);
                alert(
                    "خطا در افزودن به سبد خرید: مشکلی در ارتباط با سرور رخ داد."
                );
            }
        });
    });
}

async function displayMenu(filter = "all") {
    const urlParams = new URLSearchParams(window.location.search);
    const restaurantId = parseInt(urlParams.get("id"));
    if (isNaN(restaurantId)) {
        console.warn("No restaurant ID provided in URL");
        return;
    }
    const menuList = document.querySelector("#menuList");
    if (!menuList) {
        console.warn("Menu list element not found");
        return;
    }
    try {
        const response = await fetch(
            `${apiBaseUrl}/restaurants/${restaurantId}/menu`
        );
        let menuItems = await response.json();
        if (!response.ok) {
            throw new Error(
                `Failed to fetch menu: ${menuItems.message || "Unknown error"}`
            );
        }
        if (filter !== "all") {
            menuItems = menuItems.filter((item) => item.category === filter);
        }
        menuList.innerHTML = "";
        menuItems.forEach((item) => {
            const menuItem = document.createElement("div");
            menuItem.className =
                "menu-item bg-white rounded-lg shadow-md p-4 flex";
            menuItem.innerHTML = `
                <img src="${item.image || "/images/placeholder.jpg"}" alt="${
                item.name
            }" class="w-24 h-24 object-cover rounded-lg">
                <div class="mr-4">
                    <h3 class="text-lg font-bold">${item.name}</h3>
                    <p class="text-gray-600">${item.description}</p>
                    <p class="text-gray-500">${item.price.toLocaleString()} تومان</p>
                    <button class="add-to-cart mt-2 bg-pink-600 text-white px-4 py-2 rounded-full" data-id="${
                        item.id
                    }">افزودن به سبد خرید</button>
                </div>
            `;
            menuList.appendChild(menuItem);
        });
        setupAddToCartButtons();
    } catch (error) {
        console.error("Error fetching menu:", error);
        alert("خطا در بارگذاری منو: مشکلی در ارتباط با سرور رخ داد.");
    }
}

async function displayCart() {
    const cartItems = document.querySelector("#cartItems");
    if (!cartItems) return;
    try {
        await getCsrfToken();
        const response = await fetch(`${apiBaseUrl}/cart`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "X-CSRF-TOKEN": token,
            },
        });
        if (!response.ok) {
            throw new Error(`Failed to fetch cart: ${response.status}`);
        }
        const items = await response.json();
        cartItems.innerHTML = "";
        let totalPrice = 0;
        items.forEach((item) => {
            totalPrice += item.menu_item.price * item.quantity;
            const cartItem = document.createElement("div");
            cartItem.className =
                "flex justify-between items-center border-b py-2";
            cartItem.innerHTML = `
                <div>
                    <h3 class="text-lg font-bold">${item.menu_item.name}</h3>
                    <p class="text-gray-600">${item.menu_item.price.toLocaleString()} تومان × ${
                item.quantity
            }</p>
                </div>
                <div class="flex items-center">
                    <button class="increment-quantity px-2" data-id="${
                        item.id
                    }">+</button>
                    <span class="mx-2">${item.quantity}</span>
                    <button class="decrement-quantity px-2" data-id="${
                        item.id
                    }">-</button>
                    <button class="remove-item text-red-600 mr-4" data-id="${
                        item.id
                    }">حذف</button>
                </div>
            `;
            cartItems.appendChild(cartItem);
        });
        document.querySelector("#totalPrice").textContent =
            totalPrice.toLocaleString();

        document.querySelectorAll(".increment-quantity").forEach((button) => {
            button.addEventListener("click", async () => {
                const id = parseInt(button.dataset.id);
                try {
                    await getCsrfToken();
                    const response = await fetch(`${apiBaseUrl}/cart/${id}`, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            "X-CSRF-TOKEN": token,
                        },
                        body: JSON.stringify({
                            quantity:
                                parseInt(
                                    button.nextElementSibling.textContent
                                ) + 1,
                        }),
                    });
                    if (response.ok) {
                        updateCartCount();
                        displayCart();
                    }
                } catch (error) {
                    console.error("Error updating cart:", error);
                }
            });
        });

        document.querySelectorAll(".decrement-quantity").forEach((button) => {
            button.addEventListener("click", async () => {
                const id = parseInt(button.dataset.id);
                const currentQuantity = parseInt(
                    button.previousElementSibling.textContent
                );
                try {
                    await getCsrfToken();
                    if (currentQuantity > 1) {
                        const response = await fetch(
                            `${apiBaseUrl}/cart/${id}`,
                            {
                                method: "PUT",
                                headers: {
                                    "Content-Type": "application/json",
                                    Authorization: `Bearer ${localStorage.getItem(
                                        "token"
                                    )}`,
                                    "X-CSRF-TOKEN": token,
                                },
                                body: JSON.stringify({
                                    quantity: currentQuantity - 1,
                                }),
                            }
                        );
                        if (response.ok) {
                            updateCartCount();
                            displayCart();
                        }
                    } else {
                        const response = await fetch(
                            `${apiBaseUrl}/cart/${id}`,
                            {
                                method: "DELETE",
                                headers: {
                                    Authorization: `Bearer ${localStorage.getItem(
                                        "token"
                                    )}`,
                                    "X-CSRF-TOKEN": token,
                                },
                            }
                        );
                        if (response.ok) {
                            updateCartCount();
                            displayCart();
                        }
                    }
                } catch (error) {
                    console.error("Error updating cart:", error);
                }
            });
        });

        document.querySelectorAll(".remove-item").forEach((button) => {
            button.addEventListener("click", async () => {
                const id = parseInt(button.dataset.id);
                try {
                    await getCsrfToken();
                    const response = await fetch(`${apiBaseUrl}/cart/${id}`, {
                        method: "DELETE",
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            "X-CSRF-TOKEN": token,
                        },
                    });
                    if (response.ok) {
                        updateCartCount();
                        displayCart();
                    }
                } catch (error) {
                    console.error("Error removing item:", error);
                }
            });
        });
    } catch (error) {
        console.error("Error fetching cart:", error);
    }
}

async function displayVendorOrders() {
    const vendorOrders = document.querySelector("#vendorOrders");
    if (!vendorOrders) return;
    try {
        await getCsrfToken();
        const response = await fetch(`${apiBaseUrl}/vendor/orders`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "X-CSRF-TOKEN": token,
            },
        });
        if (!response.ok) {
            throw new Error(
                `Failed to fetch vendor orders: ${response.status}`
            );
        }
        const orders = await response.json();
        vendorOrders.innerHTML = "";
        orders.forEach((order) => {
            const orderItem = document.createElement("div");
            orderItem.className = "border-b py-2";
            orderItem.innerHTML = `
                <h3 class="text-lg font-bold">سفارش شماره ${order.id}</h3>
                <p class="text-gray-600">تاریخ: ${new Date(
                    order.created_at
                ).toLocaleString("fa-IR")}</p>
                <p class="text-gray-600">مجموع: ${order.total.toLocaleString()} تومان</p>
                <p class="text-gray-600">وضعیت: ${
                    order.status === "pending"
                        ? "در انتظار"
                        : order.status === "preparing"
                        ? "در حال آماده‌سازی"
                        : order.status === "shipped"
                        ? "ارسال شده"
                        : "تحویل داده شده"
                }</p>
                <select class="update-status p-2 border rounded" data-order-id="${
                    order.id
                }">
                    <option value="pending" ${
                        order.status === "pending" ? "selected" : ""
                    }>در انتظار</option>
                    <option value="preparing" ${
                        order.status === "preparing" ? "selected" : ""
                    }>در حال آماده‌سازی</option>
                    <option value="shipped" ${
                        order.status === "shipped" ? "selected" : ""
                    }>ارسال شده</option>
                </select>
            `;
            vendorOrders.appendChild(orderItem);
        });

        document.querySelectorAll(".update-status").forEach((select) => {
            select.addEventListener("change", async () => {
                const orderId = parseInt(select.dataset.orderId);
                const newStatus = select.value;
                try {
                    await getCsrfToken();
                    await fetch(`${apiBaseUrl}/orders/${orderId}/status`, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            "X-CSRF-TOKEN": token,
                        },
                        body: JSON.stringify({ status: newStatus }),
                    });
                    displayVendorOrders();
                    alert("وضعیت سفارش به‌روزرسانی شد!");
                } catch (error) {
                    console.error("Error updating order status:", error);
                }
            });
        });
    } catch (error) {
        console.error("Error fetching vendor orders:", error);
    }
}

async function displayVendorMenu() {
    const vendorMenu = document.querySelector("#vendorMenu");
    if (!vendorMenu) return;
    try {
        await getCsrfToken();
        const response = await fetch(`${apiBaseUrl}/restaurants/1/menu`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "X-CSRF-TOKEN": token,
            },
        });
        if (!response.ok) {
            throw new Error(`Failed to fetch vendor menu: ${response.status}`);
        }
        const menuItems = await response.json();
        vendorMenu.innerHTML = "";
        menuItems.forEach((item) => {
            const menuItem = document.createElement("div");
            menuItem.className = "border-b py-2";
            menuItem.innerHTML = `
                <h3 class="text-lg font-bold">${item.name}</h3>
                <p class="text-gray-600">${item.description}</p>
                <p class="text-gray-600">${item.price.toLocaleString()} تومان</p>
                <button class="remove-menu-item text-red-600" data-id="${
                    item.id
                }">حذف</button>
            `;
            vendorMenu.appendChild(menuItem);
        });

        document.querySelectorAll(".remove-menu-item").forEach((button) => {
            button.addEventListener("click", async () => {
                const itemId = parseInt(button.dataset.id);
                try {
                    await getCsrfToken();
                    await fetch(`${apiBaseUrl}/menu-items/${itemId}`, {
                        method: "DELETE",
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            "X-CSRF-TOKEN": token,
                        },
                    });
                    displayVendorMenu();
                    alert("آیتم منو حذف شد!");
                } catch (error) {
                    console.error("Error removing menu item:", error);
                }
            });
        });
    } catch (error) {
        console.error("Error fetching vendor menu:", error);
    }
}

async function displayCourierOrders() {
    const courierOrders = document.querySelector("#courierOrders");
    if (!courierOrders) return;
    try {
        await getCsrfToken();
        const response = await fetch(`${apiBaseUrl}/courier/orders`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "X-CSRF-TOKEN": token,
            },
        });
        if (!response.ok) {
            throw new Error(
                `Failed to fetch courier orders: ${response.status}`
            );
        }
        const orders = await response.json();
        courierOrders.innerHTML = "";
        orders.forEach((order) => {
            const orderItem = document.createElement("div");
            orderItem.className = "border-b py-2";
            orderItem.innerHTML = `
                <h3 class="text-lg font-bold">سفارش شماره ${order.id}</h3>
                <p class="text-gray-600">تاریخ: ${new Date(
                    order.created_at
                ).toLocaleString("fa-IR")}</p>
                <p class="text-gray-600">مجموع: ${order.total.toLocaleString()} تومان</p>
                <p class="text-gray-600">آدرس: ${order.address}</p>
                <p class="text-gray-600">وضعیت: ${
                    order.status === "shipped" ? "ارسال شده" : "تحویل داده شده"
                }</p>
                <select class="update-courier-status p-2 border rounded" data-order-id="${
                    order.id
                }">
                    <option value="shipped" ${
                        order.status === "shipped" ? "selected" : ""
                    }>ارسال شده</option>
                    <option value="delivered">تحویل داده شده</option>
                </select>
            `;
            courierOrders.appendChild(orderItem);
        });

        document
            .querySelectorAll(".update-courier-status")
            .forEach((select) => {
                select.addEventListener("change", async () => {
                    const orderId = parseInt(select.dataset.orderId);
                    const newStatus = select.value;
                    try {
                        await getCsrfToken();
                        await fetch(`${apiBaseUrl}/orders/${orderId}/status`, {
                            method: "PUT",
                            headers: {
                                "Content-Type": "application/json",
                                Authorization: `Bearer ${localStorage.getItem(
                                    "token"
                                )}`,
                                "X-CSRF-TOKEN": token,
                            },
                            body: JSON.stringify({ status: newStatus }),
                        });
                        displayCourierOrders();
                        alert("وضعیت سفارش به‌روزرسانی شد!");
                    } catch (error) {
                        console.error(
                            "Error updating courier order status:",
                            error
                        );
                    }
                });
            });
    } catch (error) {
        console.error("Error fetching courier orders:", error);
    }
}

document.querySelectorAll(".menu-filter").forEach((button) => {
    button.addEventListener("click", () => {
        console.log("Menu filter clicked:", button.dataset.category);
        document
            .querySelectorAll(".menu-filter")
            .forEach((btn) =>
                btn.classList.replace("bg-pink-600", "bg-gray-200")
            );
        document
            .querySelectorAll(".menu-filter")
            .forEach((btn) =>
                btn.classList.replace("text-white", "text-gray-700")
            );
        button.classList.replace("bg-gray-200", "bg-pink-600");
        button.classList.replace("text-gray-700", "text-white");
        displayMenu(button.dataset.category);
    });
});

document.querySelectorAll(".category-filter").forEach((button) => {
    button.addEventListener("click", () => {
        console.log("Category filter clicked:", button.dataset.category);
        document
            .querySelectorAll(".category-filter")
            .forEach((btn) =>
                btn.classList.replace("bg-pink-600", "bg-gray-200")
            );
        document
            .querySelectorAll(".category-filter")
            .forEach((btn) =>
                btn.classList.replace("text-white", "text-gray-700")
            );
        button.classList.replace("bg-gray-200", "bg-pink-600");
        button.classList.replace("text-gray-700", "text-white");
        displayRestaurants(button.dataset.category);
    });
});

document
    .querySelector("#searchInput")
    ?.addEventListener("input", async function () {
        const query = this.value;
        try {
            const params = new URLSearchParams({ q: query });
            const response = await fetch(`${apiBaseUrl}/restaurants?${params}`);
            if (!response.ok) {
                throw new Error(
                    `Failed to fetch restaurants: ${response.status}`
                );
            }
            const restaurants = await response.json();
            const restaurantList = document.querySelector("#restaurantList");
            restaurantList.innerHTML = "";
            restaurants.forEach((restaurant) => {
                const card = document.createElement("div");
                card.className =
                    "restaurant-card bg-white rounded-lg shadow-md p-4";
                card.innerHTML = `
                <img src="${
                    restaurant.image || "/images/placeholder.jpg"
                }" alt="${
                    restaurant.name
                }" class="w-full h-40 object-cover rounded-lg">
                <h3 class="text-lg font-bold mt-2">${restaurant.name}</h3>
                <p class="text-gray-600">${restaurant.category}</p>
                <div class="flex items-center mt-2">
                    <i class="fas fa-star text-yellow-400"></i>
                    <span class="ml-1">${restaurant.rating} (${
                    restaurant.reviews_count
                } نظر)</span>
                </div>
                <p class="text-gray-500 mt-2">هزینه ارسال: ${restaurant.delivery_cost.toLocaleString()} تومان</p>
                <a href="${window.location.origin}/restaurants/${
                    restaurant.id
                }" class="mt-4 block bg-pink-600 text-white px-4 py-2 rounded-full text-center">مشاهده منو</a>
            `;
                restaurantList.appendChild(card);
            });
        } catch (error) {
            console.error("Error searching restaurants:", error);
        }
    });

document.querySelector("#loginBtn")?.addEventListener("click", () => {
    const loginModal = document.querySelector("#loginModal");
    if (loginModal) {
        loginModal.style.display = "flex";
    } else {
        console.error("Login modal not found");
    }
});

document.querySelector("#closeModal")?.addEventListener("click", () => {
    const loginModal = document.querySelector("#loginModal");
    if (loginModal) {
        loginModal.style.display = "none";
    }
});

document.querySelector("#loginForm")?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const phone = document.querySelector("#phone")?.value;
    const password = document.querySelector("#password")?.value;
    if (!phone || !password) {
        alert("لطفاً شماره موبایل و رمز عبور را وارد کنید.");
        return;
    }
    try {
        await getCsrfToken();
        const response = await fetch(`${apiBaseUrl}/login`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                Accept: "application/json",
            },
            body: JSON.stringify({ phone, password }),
            credentials: "include",
        });
        const data = await response.json();
        if (response.ok) {
            localStorage.setItem("token", data.token);
            document.querySelector("#loginModal").style.display = "none";
            document.querySelector("#loginBtn").textContent = "پروفایل";
            document.querySelector("#loginBtn").onclick = () =>
                (window.location.href = "/profile");
            alert("ورود با موفقیت انجام شد!");
            updateCartCount();
            displayCart();
            setupAddToCartButtons();
        } else {
            console.error("Login failed:", data);
            alert("خطا در ورود: " + (data.message || "مشکل در احراز هویت"));
        }
    } catch (error) {
        console.error("Error during login:", error);
        alert("خطا در ورود: مشکلی در ارتباط با سرور رخ داد.");
    }
});

document.addEventListener("DOMContentLoaded", async () => {
    await getCsrfToken();
    updateCartCount();
    displayRestaurants();
    displayRestaurantDetails();
    displayMenu();
    displayCart();
    displayVendorOrders();
    displayVendorMenu();
    displayCourierOrders();
    setupAddToCartButtons();
});
