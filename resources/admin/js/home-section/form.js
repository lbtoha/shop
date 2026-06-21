"use strict";

import $ from "jquery";
import Sortable from "sortablejs";
import { destroySelect2, select2Init } from "@/shared/js/select2";

$(function () {
    const form = document.querySelector(".home-section-form");
    if (!form) {
        return;
    }

    // Searchable category picker (used by the Category source and as an optional
    // filter for the Custom Product List source).
    select2Init("category_id", "Select a category");

    const sourceSelect = form.querySelector("#source");
    const categorySelect = form.querySelector("#category_id");
    const picker = form.querySelector("#product_picker");
    const list = form.querySelector("#selected-products");

    function currentSource() {
        return sourceSelect ? sourceSelect.value : "";
    }

    function selectedCategory() {
        return categorySelect && categorySelect.value ? String(categorySelect.value) : "";
    }

    // ── Custom product selection (search → chips → drag to reorder) ──────
    function chipMarkup(id, name, category) {
        return `
            <li class="selected-product-item flex items-center gap-3 p-2.5 rounded-lg border border-neutral-30 dark:border-neutral-500 bg-neutral-0 dark:bg-neutral-904" data-id="${id}" data-category="${category || ""}">
                <span class="product-drag-handle cursor-grab text-gray-400 hover:text-primary shrink-0"><i class="ph ph-dots-six-vertical text-lg"></i></span>
                <span class="flex-1 s-text truncate">${name}</span>
                <button type="button" class="remove-product text-gray-400 hover:text-danger shrink-0" title="Remove"><i class="ph ph-x-circle text-lg"></i></button>
                <input type="hidden" name="product_ids[]" value="${id}">
            </li>`;
    }

    if (picker && list) {
        // Keep the full option list so we can restore it when the filter clears.
        const originalPickerHTML = picker.innerHTML;
        select2Init("product_picker", "Search products to add…");

        // Restrict the picker (and existing chips) to a single category, or show
        // everything when no category filter is set.
        function applyCategoryFilter() {
            const catId = selectedCategory();

            picker.innerHTML = originalPickerHTML;

            if (catId) {
                picker.querySelectorAll("option[data-category]").forEach((opt) => {
                    if (opt.dataset.category !== catId) {
                        opt.remove();
                    }
                });
                picker.querySelectorAll("optgroup").forEach((group) => {
                    if (!group.querySelector("option")) {
                        group.remove();
                    }
                });

            }

            destroySelect2("product_picker");
            select2Init("product_picker", "Search products to add…");
        }

        // Delegated so it survives the picker being re-rendered by the filter.
        $(form).on("select2:select", "#product_picker", function (e) {
            const id = e.params.data.id;
            if (!id) {
                return;
            }

            if (!list.querySelector(`.selected-product-item[data-id="${id}"]`)) {
                const el = e.params.data.element;
                const name = el?.dataset?.name || e.params.data.text;
                const category = el?.dataset?.category || "";
                list.insertAdjacentHTML("beforeend", chipMarkup(id, name, category));
            }

            $("#product_picker").val("").trigger("change");
        });

        $(list).on("click", ".remove-product", function () {
            this.closest(".selected-product-item")?.remove();
        });

        // Drag to reorder — hidden product_ids[] inputs move with their rows.
        Sortable.create(list, {
            handle: ".product-drag-handle",
            animation: 150,
            ghostClass: "opacity-40",
        });

        // Re-filter whenever the category changes while building a custom list.
        if (categorySelect) {
            $(categorySelect).on("change", function () {
                if (currentSource() === "products") {
                    applyCategoryFilter();
                }
            });
        }

        // Expose for the source toggler below.
        form._applyCategoryFilter = applyCategoryFilter;
    }

    // ── Show only the fields relevant to the chosen content source ──────
    function syncSourceFields() {
        const source = currentSource();

        form.querySelectorAll("[data-source-field]").forEach((el) => {
            const sources = el.dataset.sourceField.split(",");
            el.classList.toggle("hidden", !sources.includes(source));
        });

        // Source-specific hints inside a shared field.
        form.querySelectorAll("[data-source-hint]").forEach((el) => {
            el.hidden = el.dataset.sourceHint !== source;
        });

        if (source === "products" && typeof form._applyCategoryFilter === "function") {
            form._applyCategoryFilter();
        }
    }

    if (sourceSelect) {
        syncSourceFields();
        $(sourceSelect).on("change", syncSourceFields);
    }
});
