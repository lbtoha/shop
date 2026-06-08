export default class Dropdown {
    constructor(element) {
        this.toggleButton = element.querySelector(".dropdown-toggle");
        this.menu = element.querySelector(".dropdown-menu");
        // Error Handling: Check if required elements exist
        if (!this.toggleButton) {
            console.warn("Dropdown toggle button not found.");
            return;
        }
        if (!this.menu) {
            console.warn("Dropdown menu not found.");
            return;
        }
        // Initialize event listener for the dropdown toggle
        this.init();
    }
    init() {
        // Toggle the dropdown menu when the button is clicked
        this.toggleButton.addEventListener("click", (e) => {
            e.preventDefault();
            this.toggleDropdown();
        });
        // Close the dropdown if clicked outside
        document.addEventListener("click", (e) => {
            if (
                !this.menu.contains(e.target) &&
                !this.toggleButton.contains(e.target)
            ) {
                this.closeDropdown();
            }
        });
        // Close dropdown when a menu item is clicked
        const items = this.menu.querySelectorAll(".dropdown-item");
        items.forEach((item) => {
            item.addEventListener("click", (e) => {
                e.preventDefault();
                this.closeDropdown();
            });
        });
    }
    toggleDropdown() {
        // Toggle between hidden and visible states
        if (this.menu.classList.contains("hidden")) {
            this.openDropdown();
        } else {
            this.closeDropdown();
        }
    }
    openDropdown() {
        this.menu.classList.remove("hidden");
    }
    closeDropdown() {
        this.menu.classList.add("hidden");
    }
}
