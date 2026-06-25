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
    document.documentElement.style.overflow = "hidden";
}

function closeCart() {
    drawer()?.classList.add("translate-x-full");
    const o = overlay();
    if (o) {
        o.classList.add("opacity-0", "invisible");
    }
    document.body.style.overflow = "";
    document.documentElement.style.overflow = "";
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

/* Mobile menu drawer */
function initMobileMenu() {
    const menu = document.querySelector("[data-mobile-menu]");
    const overlay = document.querySelector("[data-menu-overlay]");
    const toggles = document.querySelectorAll("[data-menu-toggle]");
    const closeBtn = document.querySelector("[data-menu-close]");

    function openMenu() {
        if (!menu) return;
        menu.classList.remove("-translate-x-full");
        if (overlay) {
            overlay.classList.remove("opacity-0", "invisible");
        }
        document.body.style.overflow = "hidden";
        document.documentElement.style.overflow = "hidden";
    }

    function closeMenu() {
        if (!menu) return;
        menu.classList.add("-translate-x-full");
        if (overlay) {
            overlay.classList.add("opacity-0", "invisible");
        }
        document.body.style.overflow = "";
        document.documentElement.style.overflow = "";
    }

    toggles.forEach((toggle) => {
        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            if (menu.classList.contains("-translate-x-full")) {
                openMenu();
            } else {
                closeMenu();
            }
        });
    });

    closeBtn?.addEventListener("click", closeMenu);
    overlay?.addEventListener("click", closeMenu);

    // Search Toggle in bottom bar opens and focuses
    const searchToggle = document.querySelector("[data-search-toggle]");
    if (searchToggle && menu) {
        searchToggle.addEventListener("click", (e) => {
            e.preventDefault();
            openMenu();
            const searchInput = menu.querySelector('input[name="search"]');
            if (searchInput) {
                setTimeout(() => {
                    searchInput.focus();
                }, 300);
            }
        });
    }
}

/* AJAX Wishlist Toggle */
document.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-wishlist-toggle]");
    if (!btn) return;
    e.preventDefault();

    const url = btn.getAttribute("data-wishlist-toggle");
    const productId = btn.getAttribute("data-product-id");

    btn.disabled = true;
    try {
        const res = await fetch(url, {
            method: "POST",
            headers: jsonHeaders,
        });
        const data = await res.json();
        if (res.ok && data.success) {
            // Update the button state
            const icon = btn.querySelector("i");
            if (data.added) {
                btn.classList.remove("bg-white/95", "text-ink", "hover:bg-ink", "hover:text-white", "bg-white", "text-neutral-700", "hover:bg-neutral-50", "border-neutral-200");
                btn.classList.add("bg-brand", "text-white", "border-brand");
                if (icon) {
                    icon.classList.add("ph-fill");
                }
            } else {
                if (btn.classList.contains("w-12")) {
                    btn.classList.add("bg-white", "text-neutral-700", "hover:bg-neutral-50", "border-neutral-200");
                    btn.classList.remove("bg-brand", "text-white", "border-brand");
                } else {
                    btn.classList.add("bg-white/95", "text-ink", "hover:bg-ink", "hover:text-white");
                    btn.classList.remove("bg-brand", "text-white");
                }
                if (icon) {
                    icon.classList.remove("ph-fill");
                }
                
                // If we are currently on the wishlist page, smoothly remove the card
                if (window.location.pathname.endsWith("/wishlist") || window.location.pathname.includes("/wishlist")) {
                    const card = btn.closest(".product-card");
                    if (card) {
                        card.style.opacity = "0";
                        card.style.transform = "scale(0.9)";
                        card.style.transition = "all 0.3s ease";
                        setTimeout(() => {
                            card.remove();
                            // Check if grid is empty now
                            const grid = document.querySelector(".grid");
                            if (grid && grid.children.length === 0) {
                                window.location.reload(); // Reload to show empty state
                            }
                        }, 300);
                    }
                }
            }

            // Update all badges with the new wishlist count
            document.querySelectorAll("[data-wishlist-count]").forEach((badge) => {
                badge.textContent = data.count;
                badge.classList.toggle("hidden", !data.count);
            });

            showToast(data.message, "success");
        } else {
            showToast(data.message || "Could not update wishlist.", "error");
        }
    } catch (err) {
        showToast("Something went wrong.", "error");
    } finally {
        btn.disabled = false;
    }
});

/* Sticky header shadow toggle on scroll */
function initHeaderScroll() {
    const header = document.getElementById("main-header");
    if (!header) return;

    const handleScroll = () => {
        if (window.scrollY > 10) {
            header.classList.remove("shadow-none");
            header.classList.add("shadow-[0_2px_12px_rgba(0,0,0,.06)]");
        } else {
            header.classList.remove("shadow-[0_2px_12px_rgba(0,0,0,.06)]");
            header.classList.add("shadow-none");
        }
    };

    window.addEventListener("scroll", handleScroll, { passive: true });
    // Run immediately in case the page loads scrolled
    handleScroll();
}

/* Floating cart scroll visibility */
function initFloatingCart() {
    const floatCart = document.getElementById("floating-cart");
    if (!floatCart) return;

    floatCart.classList.remove("translate-y-20", "opacity-0", "pointer-events-none");
    floatCart.classList.add("translate-y-0", "opacity-100", "pointer-events-auto");
}

document.addEventListener("DOMContentLoaded", () => {
    initHero();
    initMobileMenu();
    initHeaderScroll();
    initFloatingCart();
});
