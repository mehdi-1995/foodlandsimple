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
