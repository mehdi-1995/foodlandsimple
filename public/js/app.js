// فیلتر کلاینت‌ساید برای منوی رستوران
function displayMenu(filter = "all") {
    const menuItems = document.querySelectorAll(".menu-item");
    console.log(`Filtering menu with category: ${filter}`);
    menuItems.forEach((item) => {
        const category = item.dataset.category || "all";
        if (filter === "all" || category === filter) {
            item.style.display = "flex";
        } else {
            item.style.display = "none";
        }
    });
}

// مدیریت کلیک روی دکمه‌های فیلتر منو
document.querySelectorAll(".menu-filter").forEach((button) => {
    button.addEventListener("click", () => {
        console.log("Menu filter clicked:", button.dataset.category);
        document.querySelectorAll(".menu-filter").forEach((btn) => {
            btn.classList.replace("bg-pink-600", "bg-gray-200");
            btn.classList.replace("text-white", "text-gray-700");
        });
        button.classList.replace("bg-gray-200", "bg-pink-600");
        button.classList.replace("text-gray-700", "text-white");
        displayMenu(button.dataset.category);
    });
});

// فعال‌سازی دکمه‌های سبد خرید
function setupCartButtons() {
    // دکمه‌های افزودن به سبد خرید
    const addToCartButtons = document.querySelectorAll(".add-to-cart");
    console.log(`Found ${addToCartButtons.length} add-to-cart buttons`);
    addToCartButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            console.log("Add to cart clicked:", button.dataset.id);
            const isLoggedIn = document.querySelector(
                'meta[name="user-id"]'
            )?.content;
            if (!isLoggedIn) {
                e.preventDefault();
                alert("لطفاً ابتدا وارد حساب کاربری خود شوید.");
                window.location.href = "/login";
                return;
            }
            // نمایش دیالوگ تأیید
            const confirmAdd = window.confirm(
                "آیا می‌خواهید این آیتم را به سبد خرید اضافه کنید؟"
            );
            if (!confirmAdd) {
                e.preventDefault(); // جلوگیری از ارسال فرم
            }
            // اگر تأیید شد، فرم به صورت خودکار ارسال می‌شه
        });
    });

    // دکمه‌های افزایش/کاهش تعداد
    const quantityButtons = document.querySelectorAll(
        ".increment-quantity, .decrement-quantity"
    );
    console.log(`Found ${quantityButtons.length} quantity buttons`);
    quantityButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            console.log(`Quantity button clicked: ${button.className}`);
            const isLoggedIn = document.querySelector(
                'meta[name="user-id"]'
            )?.content;
            if (!isLoggedIn) {
                e.preventDefault();
                alert("لطفاً ابتدا وارد حساب کاربری خود شوید.");
                window.location.href = "/login";
            }
        });
    });

    // دکمه‌های حذف آیتم
    const removeButtons = document.querySelectorAll(".remove-item");
    console.log(`Found ${removeButtons.length} remove buttons`);
    removeButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            console.log("Remove item clicked");
            const isLoggedIn = document.querySelector(
                'meta[name="user-id"]'
            )?.content;
            if (!isLoggedIn) {
                e.preventDefault();
                alert("لطفاً ابتدا وارد حساب کاربری خود شوید.");
                window.location.href = "/login";
            }
        });
    });
}

// مدیریت جستجو
document.querySelector("#searchInput")?.addEventListener("input", function () {
    const query = this.value;
    console.log("Search input:", query);
    window.location.href = `/restaurants?q=${encodeURIComponent(query)}`;
});

// مدیریت کلیک روی دکمه‌های فیلتر دسته‌بندی رستوران
document.querySelectorAll(".category-filter").forEach((button) => {
    button.addEventListener("click", () => {
        console.log("Category filter clicked:", button.dataset.category);
        window.location.href = `/restaurants?type=${button.dataset.category}`;
    });
});

// مدیریت جستجو
document.querySelector("#searchInput")?.addEventListener("input", function () {
    const query = this.value;
    console.log("Search input:", query);
    window.location.href = `/restaurants?q=${encodeURIComponent(query)}`;
});

// مدیریت کلیک روی دکمه ورود/پروفایل
document.querySelector("#loginBtn")?.addEventListener("click", () => {
    window.location.href = "/login";
});

// مدیریت رویدادهای DOMContentLoaded
document.addEventListener("DOMContentLoaded", () => {
    displayMenu();
    setupAddToCartButtons();
    // نمایش و مخفی کردن پیام‌های فلش
    const flashMessages = document.querySelectorAll("#flash-message");
    flashMessages.forEach((flashMessage) => {
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.display = "none";
            }, 3000);
        }
    });
});
