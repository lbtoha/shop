/**
 * Storefront interactivity: AJAX add-to-cart, header cart badge, hero carousel,
 * and mobile menu toggle. Vanilla JS, no framework dependency.
 */

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.className =
        "fixed top-5 right-5 z-50 px-4 py-3 rounded shadow-lg text-white text-sm transition-opacity duration-300 " +
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
        el.textContent = count;
        el.classList.toggle("hidden", !count);
    });
}

/* AJAX add to cart */
document.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-add-to-cart]");
    if (!btn) return;
    e.preventDefault();

    const url = btn.getAttribute("data-add-to-cart");
    const qtyInput = document.querySelector("[data-quantity-input]");
    const quantity = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;

    btn.disabled = true;
    try {
        const res = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ quantity }),
        });
        const data = await res.json();
        if (res.ok && data.success) {
            updateCartBadge(data.count);
            showToast(data.message, "success");
        } else {
            showToast(data.message || "Could not add to cart.", "error");
        }
    } catch (err) {
        showToast("Something went wrong.", "error");
    } finally {
        btn.disabled = false;
    }
});

/* Quantity steppers on product / cart pages */
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
    if (slides.length < 2) return;
    let current = 0;
    setInterval(() => {
        slides[current].classList.add("hidden");
        current = (current + 1) % slides.length;
        slides[current].classList.remove("hidden");
    }, 4000);
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
