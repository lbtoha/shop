export default class Submenu {
    constructor(menuSelector) {
        try {
            this.menus = document.querySelectorAll(menuSelector);
            if (this.menus.length === 0) {
                throw new Error(`No submenu items found with selector "${menuSelector}"`);
            }
            this.init();
        } catch (error) {
            console.error("Submenu initialization error:", error.message);
        }
    }

    init() {
        this.menus.forEach((menu, index) => {
            const button = menu.querySelector(":scope > .submenu-btn"); // Direct child button
            const content = menu.querySelector(":scope > .submenu-content"); // Direct child content

            if (!button || !content) {
                console.log(`Submenu item ${index} is missing required elements`);
                return;
            }

            if (menu.querySelector(".submenu_active")) {
                menu.classList.add("active");
                content.style.display = "block";
            }

            button.addEventListener("click", (event) => {
                event.stopPropagation();
                this.toggleMenu(menu);
            });

            // Check for nested submenus
            const nestedSubmenus = content.querySelectorAll(".submenu");
            if (nestedSubmenus.length > 0) {
                // Initialize nested submenu instances
                new Submenu(".submenu");
            }
        });
    }

    toggleMenu(menu) {
        const content = menu.querySelector(":scope > .submenu-content");
        const isOpen = menu.classList.contains("active");

        // Close all sibling menus at the same level
        const siblings = Array.from(menu.parentElement.children).filter((child) => child !== menu);
        siblings.forEach((sibling) => this.closeMenu(sibling));

        // Toggle the clicked menu
        if (!isOpen) {
            menu.classList.add("active");
            this.showContent(content);
        } else {
            this.closeMenu(menu);
        }
    }

    showContent(content) {
        if (!content) return;

        // Show the content
        content.style.display = "block";
    }

    closeMenu(menu) {
        const content = menu.querySelector(":scope > .submenu-content");
        menu.classList.remove("active");
        if (content) content.style.display = "none";

        // Recursively close all nested submenus
        const nestedMenus = menu.querySelectorAll(":scope .submenu");
        nestedMenus.forEach((nestedMenu) => this.closeMenu(nestedMenu));
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const activeItem = document.querySelector('.submenu-item.active');
    if (activeItem) {
        activeItem.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }
});