"use strict";
import { toastError, toastSuccess } from "@/shared/js//toast";
import collapse from "@alpinejs/collapse";
import Alpine from "alpinejs";
import axios from "axios";
import $ from "jquery";
import { Sortable } from "sortablejs/modular/sortable.core.esm";

Alpine.plugin(collapse);

const initializeSortable = (element) => {
    new Sortable(element, {
        group: {
            name: "nested",
            pull: true, // Allow items to be moved out
            put: true, // Allow items to be moved in
        },
        animation: 150,
        handle: ".handle",
        ghostClass: "sortable-ghost",
        chosenClass: "sortable-chosen",
        dragClass: "sortable-drag",
        fallbackOnBody: true,
    });
};

// Initialize Sortable on the main list
const sortableId = document.getElementById("nestable2");

if (sortableId) {
    initializeSortable(sortableId);

    // Observe for dynamically added nested lists
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === "childList") {
                mutation.addedNodes.forEach((node) => {
                    // Ensure node is an element
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node.tagName === "OL") {
                            initializeSortable(node);
                        } else {
                            const nestedLists = node.querySelectorAll("ol");
                            nestedLists.forEach((nestedList) => {
                                initializeSortable(nestedList);
                            });
                        }
                    }
                });
            }
        });
    });

    observer.observe(sortableId, { childList: true, subtree: true });
}

Alpine.data("menuBar", () => ({
    mainMenu: {},
    menuItems: [],
    validationErrors: {},
    pages: [],
    selectedPages: [],
    itemInputs: [],
    isSaving: false,
    addNewMenu() {
        let order = this.menuItems.length + 1;
        const newItems = [];
        this.selectedPages.entries().forEach(([index, pageSlug]) => {
            this.pages
                .filter((page) => page.slug === pageSlug)
                .forEach((page) => {
                    newItems.push({
                        page_id: page.id,
                        order: order++,
                        title: page.title,
                    });
                });
        });
        if (newItems.length === 0) {
            toastError("Please select at least one page");
            return;
        }
        axios
            .post(`/admin/settings/navigation/menus/${this.mainMenu?.id}/item/store`, {
                newItems: newItems,
            })
            .then((response) => {
                toastSuccess(response?.data?.message);
                this.menuItems.push(...response.data.items);
            })
            .catch((error) => {
                let message = error?.response?.data?.message;
                if (error.response.status === 422) {
                    this.validationErrors = error.response.data.errors;
                }

                toastError(message);
            });
        this.selectedPages = [];
    },
    removeItemWithChildren(menus, id) {
        if (!Array.isArray(menus) || menus.length === 0) {
            return [];
        }

        return menus
            .filter((menu) => menu.id !== id)
            .map((menu) => ({
                ...menu,
                children: menu.children ? this.removeItemWithChildren(menu.children, id) : [],
            }));
    },

    // Define the removeMenuItem method
    removeMenuItem(id) {
        axios
            .post(`/admin/settings/navigation/menus/${this.mainMenu?.id}/item/delete`, {
                id,
            })
            .then((response) => {
                toastSuccess(response?.data?.message);
                this.menuItems = this.removeItemWithChildren(this.menuItems, id);
            })
            .catch((error) => {
                let message = error?.response?.data?.message;
                toastError(message);
            });
    },
    saveSingleMenuItem(id) {
        const form = $(`#item-form-${id}`);
        const inputs = form.find("input");
        const item = {
            id: id,
        };
        inputs.each((i, input) => {
            const name = $(input).attr("name");
            let value = $(input).val();

            if (value === "" || value === "null") {
                value = null;
            }

            item[name] = value;
        });

        const select = form.find("select");
        select.each((i, input) => {
            const name = $(input).attr("name");
            let value = $(input).val();

            if (value === "" || value === "null") {
                value = null;
            }

            item[name] = value;
        });

        axios
            .post(`/admin/settings/navigation/menus/${this.mainMenu?.id}/item/update`, item)
            .then((response) => {
                toastSuccess(response?.data?.message);
            })
            .catch((error) => {
                let message = error?.response?.data?.message;
                if (error.response.status === 422) {
                    this.validationErrors = error.response.data.errors;
                }

                toastError(message);
            });
    },
    saveMenu() {
        this.isSaving = true;
        axios
            .post(`/admin/settings/navigation/menus/${this.mainMenu?.id}/item/bulk-update`, {
                menu_title: this.mainMenu?.name,
                menu_location: this.mainMenu?.location,
                menu_items: this.getNewData(),
            })
            .then((response) => {
                this.isSaving = false;
                toastSuccess(response?.data?.message);
            })
            .catch((error) => {
                if (error.response.status === 422) {
                    this.validationErrors = error.response.data.errors;
                }
                this.isSaving = false;
                toastError(error?.response?.data?.message);
            });
    },
    getNewData(element = null, parentId = null) {
        let menuElements = element ? element : $("#nestable2").children("li");
        const self = this;
        let data = [];

        menuElements.each(function (index, item) {
            const id = $(this).data("id");
            const form = $(this).children(".handle").find(`#item-form-${id}`);
            const newData = {};
            if (form) {
                const inputs = form.find("input");
                if (inputs.length > 0) {
                    inputs.each((i, input) => {
                        const name = $(input).attr("name");
                        const value = $(input).val();
                        newData[name] = value;
                    });
                }
                const select = form.find("select");
                if (select.length > 0) {
                    select.each((i, input) => {
                        const name = $(input).attr("name");
                        const value = $(input).val();
                        newData[name] = value;
                    });
                }
            }
            data.push({
                ...newData,
                id: id,
                order: index,
                parent_id: parentId ? Number(parentId) : null,
            });
            const children = $(this).children("ol").children("li");
            if (children.length > 0) {
                const childrenData = self.getNewData(children, id);
                data = [...data, ...childrenData];
            }
        });

        return data;
    },
    renderChildren(children, index) {
        if (!children || children.length === 0) {
            return "";
        }
        return children
            .map(
                (child, childIndex) => `
                <li class="dd-item" data-position="${child.order}" data-id="${child.id}">
                    <div class="handle" x-data="{ open: false }">
                        <div class="dd-container" :class="open ? 'expanded' : ''">
                            <div class="dd-handle">
                                <div class="flex items-center gap-2">
                                    <i class="ph ph-dots-six-vertical text-lg cursor-pointer"></i>
                                    <p>${child.title}</p>
                                </div>
                                <span class="text-xs">Page</span>
                            </div>
                            <button x-on:click="open = !open" class="arrow"><i class="ph ph-caret-down"></i></button>
                        </div>
                        <div x-show="open" x-collapse>
                            <div id="item-form-${child.id}" class="space-y-3 p-3 xl:p-4 border border-neutral-30 border-t-0 dark:border-neutral-500">
                                <input type="hidden" value="${child.page_id||""}" name="page_id">
                                <div>
                                    <label class="form-level block mb-2 text-xs">Title</label>
                                    <input type="text" value="${child.title}" name="title" class="px-3 py-2.5 rounded-md border border-neutral-30 dark:border-neutral-500 w-full text-sm" placeholder="Enter Text" />
                                </div>
                                <div>
                                    <label class="form-level block mb-2 text-xs">URL</label>
                                    <input type="text" value="${child.url||""}" ${child.is_primary ? "disabled" : ""} name="url" class="px-3 py-2.5 rounded-md border border-neutral-30 dark:border-neutral-500 w-full text-sm" placeholder="Enter Text" />
                                </div>
                               <div>
                                    <label class="form-level block mb-2 text-xs">Icon</label>
                                    <div class="icon-picker">
                                        <input type="text" name="icon" class="text-input icon-input w-full"
                                            id="icon${child.id}" placeholder="Select an icon">

                                        <div class="icon-picker-modal dark:bg-neutral-800">
                                            <div class="icon-list">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p class="mb-1 text-xs">Target</p>
                                <select class="select-2 full-width" name="target" value="${child.target}">
                                    <option value="__blank">Open Link Directly</option>
                                    <option value="__self">Open Link in Direct Tab</option>
                                </select>
                                <div class="flex gap-3 mt-3">
                                    <span @click="removeMenuItem(${child.id})" class="inline-flex px-3 py-1.5 cursor-pointer rounded-md items-center gap-1 bg-error text-white button-delete pull-right" data-owner-id="1"> <i class="ph ph-x" aria-hidden="true"></i> Delete </span>
                                    <span @click="saveSingleMenuItem(${child.id})" class="button-edit cursor-pointer px-3 py-1.5 rounded-md items-center gap-1 bg-primary text-white inline-flex pull-right" data-owner-id="1"> <i class="ph ph-pencil-simple" aria-hidden="true"></i> Save </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ol class="dd-list">
                        ${this.renderChildren(child.children, childIndex)}
                    </ol>
                </li>`
            )
            .join(""); // Join the array without commas
    },
}));

Alpine.start();
