import { getQueryParamsValue, setQueryValue } from "../helper";

export default class Tabs {
    constructor(element, defaultLang) {
        this.defaultLang = defaultLang;
        this.tabLinks = element.querySelectorAll(".tab-link");
        this.tabPanels = element.querySelectorAll(".tab-panel");

        // Error Handling: Check if we have the necessary elements
        if (!this.tabLinks.length) {
            console.warn("No tab links found for this tab group. Skipping initialization.");
            return;
        }
        if (!this.tabPanels.length) {
            console.warn("No tab panels found for this tab group. Skipping initialization.");
            return;
        }
        if (this.tabLinks.length !== this.tabPanels.length) {
            console.warn("The number of tab links does not match the number of tab panels.");
        }

        this.init();
    }

    init() {
        // Add event listeners to all tab links
        this.tabLinks.forEach((link, index) => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                this.switchTab(index); // Pass the index of the clicked tab
                const dataLink = link.getAttribute("data-link");
                if (dataLink) {
                    // If a data-link attribute is present, redirect to that link
                    setQueryValue("tab", dataLink);
                }
            });

            if (this.defaultLang && link.getAttribute("data-link") === this.defaultLang) {
                // If the current tab matches the default language, activate it
                this.switchTab(index);
            }

            const tab = getQueryParamsValue("tab");
            if (tab && link.getAttribute("data-link") === tab) {
                // If the current tab matches the query parameter, activate it
                this.switchTab(index);
            }

            // if url not contain tab query param, activate the first tab
            if (!tab && index === 0) {
                this.switchTab(index);
            }
        });
    }

    switchTab(index) {
        // Remove active state from all tabs
        this.tabLinks.forEach((link) => {
            link.classList.remove("active");
        });

        // Hide all panels
        this.tabPanels.forEach((panel) => {
            panel.classList.add("hidden");
        });

        // Activate the clicked tab and corresponding panel
        if (this.tabLinks[index]) {
            this.tabLinks[index].classList.add("active");
        }
        if (this.tabPanels[index]) {
            this.tabPanels[index].classList.remove("hidden");
        }
    }
}
