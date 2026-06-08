import Alpine from "alpinejs";
import tippy from "tippy.js";
import Dropdown from "./dropdown";
import Modal from "../Modal";
import Submenu from "./submenu";
import Tabs from "./tabs";
import $ from "jquery";
import { select2dynamic, select2Init, select2InitByClass } from "../select2";

// initialize select with number input for symbol change

window.Alpine = Alpine;

// switch to dark mode
const theme = localStorage.getItem("theme");
setTheLogo(theme);
const modeSwitchBtn = document.querySelector(".mode-switcher");
let iconEl = null;
if (modeSwitchBtn) {
    iconEl = modeSwitchBtn.querySelector("i");
    if (theme == "dark" && iconEl) {
        iconEl.classList.remove("ph-sun");
        iconEl.classList.add("ph-moon");
    }
    modeSwitchBtn.addEventListener("click", () => {
        if (document.documentElement.classList.contains("dark")) {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("theme", "light");
            iconEl.classList.remove("ph-moon");
            iconEl.classList.add("ph-sun");
            setTheLogo("light");
        } else {
            document.documentElement.classList.add("dark");
            iconEl.classList.remove("ph-sun");
            iconEl.classList.add("ph-moon");
            localStorage.setItem("theme", "dark");
            setTheLogo("dark");
        }
    });
}

function setTheLogo(theme) {
    const img = document.querySelector(".application-logo");
    if (!img) {
        return;
    }
    const darkLogo = img.getAttribute("data-dark-logo");
    const lightLogo = img.getAttribute("data-light-logo");
    if (theme == "dark" && darkLogo) {
        img.setAttribute("src", darkLogo);
    } else if (lightLogo) {
        img.setAttribute("src", lightLogo);
    }
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        // If no element is in fullscreen, make the document fullscreen
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
            /* Firefox */
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
            /* Chrome, Safari and Opera */
            document.documentElement.webkitRequestFullscreen();
        } else if (document.documentElement.msRequestFullscreen) {
            /* IE/Edge */
            document.documentElement.msRequestFullscreen();
        }
    } else {
        // Exit fullscreen
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            /* Firefox */
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            /* Chrome, Safari and Opera */
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            /* IE/Edge */
            document.msExitFullscreen();
        }
    }
}
const fullScreenBtn = document.getElementById("fullscreenButton");
fullScreenBtn && fullScreenBtn.addEventListener("click", toggleFullscreen);

// popover with tippy js
const buttons = document.querySelectorAll(".popover-button");
if (buttons.length) {
    // Initialize Tippy for each button
    buttons.forEach((button) => {
        const content = button.nextElementSibling;
        tippy(button, {
            content: content,
            allowHTML: true,
            interactive: true,
            trigger: "click",
            placement: "left",
            animation: "scale",
            arrow: false,
            appendTo: document.body,
            onShow(instance) {
                // initialize modals
                function initModals() {
                    const modals = content.querySelectorAll("[data-modal-target]");
                    if (modals.length) {
                        modals.forEach((buttonEl) => {
                            new Modal(buttonEl);
                        });
                    }
                }
                setTimeout(initModals, 10);

                // Ensure content is visible when popover is shown
                instance.popper.querySelector(".popover-content").style.display = "block";
                const listItems = instance.popper.querySelectorAll(".popover-content li");
                listItems.forEach((item) => {
                    item.addEventListener("click", () => {
                        instance.hide(); // Hide the popover when item is clicked
                    });
                });
            },
            onHide(instance) {
                // Hide content when popover is hidden
                instance.popper.querySelector(".popover-content").style.display = "none";
            },
        });
    });
}

// initialize components
document.addEventListener("DOMContentLoaded", () => {
    // dropdown
    const dropdown = document.querySelectorAll(".dropdown");
    if (dropdown.length) {
        dropdown.forEach((item) => {
            new Dropdown(item);
        });
    }
    // modals
    const modals = document.querySelectorAll("[data-modal-target]");
    if (modals.length) {
        modals.forEach((buttonEl) => {
            new Modal(buttonEl);
        });
    }

    // submenu
    if (document.querySelector(".submenu-item")) {
        new Submenu(".submenu-item");
    }

    // tabs
    const tabGroups = document.querySelectorAll(".tabs");
    tabGroups.forEach((tabGroup) => {
        new Tabs(tabGroup, tabGroup.getAttribute("data-default-lang"));
    });
});

if (document.querySelector(".sidebar")) {
    const sidebar = document.querySelector(".sidebar");
    const topbar = document.querySelector(".topbar");
    const mainContent = document.querySelector(".main-content");
    const sidebarToggleBtn = document.querySelector(".sidebar-toggle-btn");
    const sidebarCloseBtn = document.querySelector(".sidebar-close-btn");
    const sidebarOverlay = document.querySelector(".sidebar-overlay");
    window.addEventListener("resize", () => {
        if (window.innerWidth > 1200) {
            sidebar.classList.add("opened");
            topbar.classList.remove("closed");
            mainContent.classList.remove("closed");
        } else {
            sidebar.classList.remove("opened");
            topbar.classList.add("closed");
            mainContent.classList.add("closed");
        }
    });
    window.addEventListener("DOMContentLoaded", () => {
        if (window.innerWidth > 1200) {
            sidebar.classList.add("opened");
        } else {
            sidebar.classList.remove("opened");
        }
    });
    sidebarToggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("opened");
        topbar.classList.toggle("closed");
        mainContent.classList.toggle("closed");
    });
    sidebarCloseBtn.addEventListener("click", () => {
        sidebar.classList.remove("opened");
        topbar.classList.add("closed");
        mainContent.classList.add("closed");
    });
    sidebarOverlay.addEventListener("click", () => {
        sidebar.classList.remove("opened");
        topbar.classList.add("closed");
        mainContent.classList.add("closed");
    });

    const sidebarul = document.querySelector(".vertical-sidebar");

    const currentUrl = window.location.href;
    const addActiveClass = () => {
        const links = sidebarul.querySelectorAll(".menu-link");
        links.forEach((link) => {
            const href = link.getAttribute("href");
            if (currentUrl.includes(href)) {
                link.classList.add("active");
            }
        });
    };

    const setOpenedMenu = () => {
        const submenus = document.querySelectorAll(".submenu-link-v");
        submenus.forEach((submenu) => {
            const href = submenu.getAttribute("href");
            if (currentUrl.includes(href)) {
                submenu.classList.add("text-primary");
                const sidebarRect = sidebarul.getBoundingClientRect();
                const elementRect = submenu.getBoundingClientRect();
                const offsetTop = elementRect.top - sidebarRect.top;

                // Calculate the scroll position to center the element within the sidebar
                const scrollPosition = offsetTop - sidebarRect.height / 2 + elementRect.height / 2;

                sidebarul.scrollTo({
                    top: (scrollPosition * 35) / 100,
                    behavior: "smooth",
                });
                const submenuContent = submenu.parentElement.parentElement.parentElement;
                const submenuItem = submenu.parentElement.parentElement.parentElement.parentElement;
                submenuItem.classList.add("active");
                submenuContent.style.maxHeight = `${submenuContent.scrollHeight}px`;
            }
        });
    };
    setTimeout(addActiveClass, 1);
    setTimeout(setOpenedMenu, 5);
}

$(() => {
    select2dynamic();
    select2InitByClass();
});

select2Init("select_option");

const fileInput = document.getElementById("fileupload");
const fileList = document.getElementById("fileList");

if (fileInput) {
    fileInput.addEventListener("change", function (event) {
        const files = event.target.files;
        fileList.innerHTML = ""; // Clear previous file list

        if (files.length > 0) {
            Array.from(files).forEach((file) => {
                const fileItem = document.createElement("div");
                fileItem.classList.add("flex", "relative", "items-center", "space-x-4", "py-2.5", "bg-neutral-20", "rounded-lg", "shadow-sm", "text-sm", "px-3", "dark:bg-neutral-903");

                const icon = document.createElement("span");
                icon.classList.add("f-center", "size-10", "bg-primary/10", "text-lg", "text-primary", "rounded-lg");
                icon.innerHTML = '<i class="ph ph-file"></i>';

                const fileName = document.createElement("span");
                fileName.classList.add("truncate");
                fileName.textContent = file.name;

                const fileSize = document.createElement("span");
                fileSize.classList.add("text-neutral-100", "text-xs");
                fileSize.textContent = `${(file.size / 1024).toFixed(2)} KB`;

                const removeBtn = document.createElement("button");
                removeBtn.classList.add("text-red-600", "hover:text-red-800", "focus:outline-none", "absolute", "right-5", "top-1/2", "-translate-y-1/2");
                removeBtn.innerHTML = '<i class="ph ph-x"></i>';
                removeBtn.addEventListener("click", () => {
                    fileItem.remove();
                });

                fileItem.appendChild(icon);
                fileItem.appendChild(fileName);
                fileItem.appendChild(fileSize);
                fileItem.appendChild(removeBtn);

                fileList.appendChild(fileItem);
            });
        }
    });
}

$('.single-file-upload > input').on('change', function () {
    const fileName = $(this).val().split('\\').pop();
    $(this).closest('.single-file-upload').find('.file-name').text(fileName);
});


// tooltip active class
const tooltipTrigger = document.querySelectorAll(".tooltip");

tooltipTrigger.forEach((trigger) => {
    trigger.addEventListener("mouseenter", () => {
        const text = trigger.getAttribute("data-tooltip");
        if (text) {
            trigger.classList.add("tooltip-active");
        }
    });
    trigger.addEventListener("mouseleave", () => {
        trigger.classList.remove("tooltip-active");
    });
});
