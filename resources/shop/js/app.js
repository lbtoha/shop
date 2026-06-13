/**
 * Storefront interactivity: AJAX add-to-cart with a slide-in cart drawer,
 * header cart badge, hero carousel, and mobile menu toggle.
 * Vanilla JS, no framework dependency.
 */

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

const jsonHeaders = {
    "Content-Type": "application/json",
    "X-CSRF-TOKEN": csrfToken,
    Accept: "application/json",
    "X-Requested-With": "XMLHttpRequest",
};

function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.className =
        "fixed top-5 right-5 z-[60] px-4 py-3 rounded-lg shadow-lg text-white text-sm transition-opacity duration-300 " +
        (type === "success" ? "bg-emerald-600" : "bg-red-600");
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

function updateCartBadge(count) {
    document.querySelectorAll("[data-cart-count]").forEach((el) => {
        const currentCount = parseInt(el.textContent, 10) || 0;
        el.textContent = count;
        // The drawer's own badge always shows; header badges hide at 0.
        if (el.closest("[data-cart-drawer]")) return;
        el.classList.toggle("hidden", !count);

        // Dynamic pulse animation if count increased
        if (count > currentCount && !el.closest("[data-cart-drawer]")) {
            el.classList.add("scale-150");
            setTimeout(() => {
                el.classList.remove("scale-150");
            }, 300);
        }
    });
}

/* ---------------- Cart drawer ---------------- */
const drawer = () => document.querySelector("[data-cart-drawer]");
const overlay = () => document.querySelector("[data-cart-overlay]");

function openCart() {
    drawer()?.classList.remove("translate-x-full");
    const o = overlay();
    if (o) {
        o.classList.remove("opacity-0", "invisible");
    }
    document.body.style.overflow = "hidden";
}

function closeCart() {
    drawer()?.classList.add("translate-x-full");
    const o = overlay();
    if (o) {
        o.classList.add("opacity-0", "invisible");
    }
    document.body.style.overflow = "";
}

function renderDrawer(html, count) {
    const body = document.querySelector("[data-cart-body]");
    if (body && typeof html === "string") body.innerHTML = html;
    if (typeof count === "number") updateCartBadge(count);
}

async function refreshDrawer() {
    try {
        const res = await fetch("/cart/fragment", { headers: jsonHeaders });
        const data = await res.json();
        renderDrawer(data.drawer, data.count);
    } catch (_) {
        /* ignore */
    }
}

/* Open the drawer from the header cart icon */
document.addEventListener("click", (e) => {
    const opener = e.target.closest("[data-cart-open]");
    if (!opener) return;
    e.preventDefault();
    openCart();
    refreshDrawer();
});

/* Close: X button, "Continue Shopping", overlay click, Esc */
document.addEventListener("click", (e) => {
    if (e.target.closest("[data-cart-close]")) {
        // allow links (Continue Shopping) to still navigate, but close first
        closeCart();
        return;
    }
    if (e.target.matches("[data-cart-overlay]")) closeCart();
});
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeCart();
});

/* AJAX add to cart -> open drawer with fresh contents */
document.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-add-to-cart]");
    if (!btn) return;
    e.preventDefault();

    const url = btn.getAttribute("data-add-to-cart");
    const qtyInput = document.querySelector("[data-quantity-input]");
    const quantity = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
    const variantEl = document.getElementById("selected-variant-id");
    const variantId = variantEl && variantEl.value ? parseInt(variantEl.value, 10) : null;

    btn.disabled = true;
    try {
        const res = await fetch(url, {
            method: "POST",
            headers: jsonHeaders,
            body: JSON.stringify({ quantity, variant_id: variantId }),
        });
        const data = await res.json();
        if (res.ok && data.success) {
            renderDrawer(data.drawer, data.count);
            openCart();
        } else {
            showToast(data.message || "Could not add to cart.", "error");
        }
    } catch (err) {
        showToast("Something went wrong.", "error");
    } finally {
        btn.disabled = false;
    }
});

/* Buy now -> add to cart (with variant) then go straight to checkout */
document.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-buy-now]");
    if (!btn) return;
    e.preventDefault();

    const url = btn.getAttribute("data-buy-now");
    const qtyInput = document.querySelector("[data-quantity-input]");
    const quantity = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
    const variantEl = document.getElementById("selected-variant-id");
    const variantId = variantEl && variantEl.value ? parseInt(variantEl.value, 10) : null;

    btn.style.pointerEvents = "none";
    try {
        const res = await fetch(url, {
            method: "POST",
            headers: jsonHeaders,
            body: JSON.stringify({ quantity, variant_id: variantId }),
        });
        const data = await res.json();
        if (res.ok && data.success) {
            window.location.href = "/checkout";
        } else {
            showToast(data.message || "Could not add to cart.", "error");
            btn.style.pointerEvents = "";
        }
    } catch (err) {
        showToast("Something went wrong.", "error");
        btn.style.pointerEvents = "";
    }
});

/* Drawer qty stepper (delegated) */
document.addEventListener("click", async (e) => {
    const qtyBtn = e.target.closest("[data-cart-qty]");
    if (!qtyBtn) return;
    const url = qtyBtn.getAttribute("data-cart-qty");
    const delta = parseInt(qtyBtn.getAttribute("data-delta"), 10);
    const qtyEl = qtyBtn.parentElement.querySelector("[data-qty]");
    const current = parseInt(qtyEl?.textContent || "1", 10);
    const next = current + delta; // 0 removes the line server-side

    qtyBtn.disabled = true;
    try {
        const res = await fetch(url, {
            method: "PUT",
            headers: jsonHeaders,
            body: JSON.stringify({ quantity: Math.max(0, next) }),
        });
        const data = await res.json();
        renderDrawer(data.drawer, data.count);
    } finally {
        qtyBtn.disabled = false;
    }
});

/* Drawer remove + clear (delegated) */
document.addEventListener("click", async (e) => {
    const removeBtn = e.target.closest("[data-cart-remove]");
    const clearBtn = e.target.closest("[data-cart-clear]");
    const target = removeBtn || clearBtn;
    if (!target) return;
    const url = target.getAttribute("data-cart-remove") || target.getAttribute("data-cart-clear");
    target.disabled = true;
    try {
        const res = await fetch(url, { method: "DELETE", headers: jsonHeaders });
        const data = await res.json();
        renderDrawer(data.drawer, data.count);
    } finally {
        target.disabled = false;
    }
});

/* Quantity steppers on product / cart full pages */
document.addEventListener("click", (e) => {
    const stepper = e.target.closest("[data-step]");
    if (!stepper) return;
    const wrap = stepper.closest("[data-qty-wrap]");
    const input = wrap?.querySelector("input");
    if (!input) return;
    const step = parseInt(stepper.getAttribute("data-step"), 10);
    const next = Math.max(1, (parseInt(input.value, 10) || 1) + step);
    input.value = next;
    input.dispatchEvent(new Event("change"));
});

/* Simple hero carousel */
function initHero() {
    const hero = document.querySelector("[data-hero]");
    if (!hero) return;
    const slides = hero.querySelectorAll("[data-slide]");
    const dots = hero.querySelectorAll("[data-hero-dot]");
    const prevBtn = hero.querySelector("[data-hero-prev]");
    const nextBtn = hero.querySelector("[data-hero-next]");
    if (slides.length === 0) return;

    let current = 0;
    let timer = null;

    function showSlide(index) {
        const nextSlide = (index + slides.length) % slides.length;

        // Hide current slide
        slides[current].classList.add("hidden", "opacity-0", "z-0");
        slides[current].classList.remove("active", "opacity-100", "z-10");
        if (dots.length > current) {
            dots[current].classList.remove("bg-brand", "w-8");
            dots[current].classList.add("bg-white/40", "w-3");
        }

        // Show new slide
        current = nextSlide;
        slides[current].classList.remove("hidden", "opacity-0", "z-0");
        void slides[current].offsetWidth;
        slides[current].classList.add("active", "opacity-100", "z-10");
        if (dots.length > current) {
            dots[current].classList.add("bg-brand", "w-8");
            dots[current].classList.remove("bg-white/40", "w-3");
        }
    }

    function startAutoPlay() {
        stopAutoPlay();
        if (slides.length < 2) return;
        timer = setInterval(() => {
            showSlide(current + 1);
        }, 5000);
    }

    function stopAutoPlay() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    // Set initial slide active state
    slides[current].classList.add("active", "opacity-100", "z-10");
    slides[current].classList.remove("opacity-0", "z-0");

    // Controls
    if (prevBtn) {
        prevBtn.addEventListener("click", (e) => {
            e.preventDefault();
            showSlide(current - 1);
            startAutoPlay();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener("click", (e) => {
            e.preventDefault();
            showSlide(current + 1);
            startAutoPlay();
        });
    }

    // Dots
    dots.forEach((dot) => {
        dot.addEventListener("click", (e) => {
            e.preventDefault();
            const idx = parseInt(dot.getAttribute("data-hero-dot"), 10);
            showSlide(idx);
            startAutoPlay();
        });
    });

    // Auto play and mouse hover pausing
    hero.addEventListener("mouseenter", stopAutoPlay);
    hero.addEventListener("mouseleave", startAutoPlay);

    startAutoPlay();
}

/* Mobile menu */
function initMobileMenu() {
    const toggle = document.querySelector("[data-menu-toggle]");
    const menu = document.querySelector("[data-mobile-menu]");
    toggle?.addEventListener("click", () => menu?.classList.toggle("hidden"));
}

document.addEventListener("DOMContentLoaded", () => {
    initHero();
    initMobileMenu();
});
