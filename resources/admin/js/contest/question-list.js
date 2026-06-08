"use strict";
import { toastConfirm } from "@/shared/js/toast";
import Alpine from "alpinejs";

Alpine.data("metaAdd", () => ({
    contest: null,
    selectedItems: [],
    questionLevel: null,
    isDragging: false,
    checkedIds: [],
    bulkDeleteUrl: "",
    searchQuery: "",
    filterType: "",
    filterInputType: "",

    get filteredItems() {
        return this.selectedItems.filter((item) => {
            const query = this.searchQuery.toLowerCase();
            const text = (item.translation?.question_text || "").toLowerCase();
            const matchesSearch = !query || text.includes(query);
            const matchesType = !this.filterType || item.question_type === this.filterType;
            const matchesInputType = !this.filterInputType || item.question_input_type === this.filterInputType;
            return matchesSearch && matchesType && matchesInputType;
        });
    },

    toggleCheck(id) {
        const index = this.checkedIds.indexOf(id);
        if (index === -1) {
            this.checkedIds.push(id);
        } else {
            this.checkedIds.splice(index, 1);
        }
    },

    toggleSelectAll(event) {
        if (event.target.checked) {
            this.checkedIds = this.filteredItems.map((item) => item.id);
        } else {
            this.checkedIds = [];
        }
    },

    bulkDelete() {
        if (!this.checkedIds.length) return;

        toastConfirm({
            title: `Delete ${this.checkedIds.length} question(s)?`,
            text: "You won't be able to revert this!",
            confirmButtonText: "Yes, Delete!",
            action: this.bulkDeleteUrl,
            method: "DELETE",
            data: { ids: this.checkedIds },
        });
    },

    startDrag(event, source, index) {
        this.draggedItem = source === "selected" ? this.selectedItems[index] : [];
        this.draggedFrom = source;
        this.draggedIndex = index;
        this.isDragging = true;
        event.dataTransfer.effectAllowed = "move";
    },

    endDrag() {
        this.isDragging = false;
    },

    allowDrop(event) {
        event.preventDefault();
    },

    handleDrop(event, targetIndex = null) {
        event.preventDefault();

        if (this.draggedFrom === "selected") {
            this.selectedItems.splice(this.draggedIndex, 1);

            if (targetIndex !== null) {
                this.selectedItems.splice(targetIndex, 0, this.draggedItem);
            } else {
                this.selectedItems.push(this.draggedItem);
            }
        }

        this.resetDragState();
        this.selectedItems.forEach((item, index) => {
            item.order = index;
        });
    },

    deleteItem(index) {
        this.selectedItems.splice(index, 1);
    },

    resetDragState() {
        this.draggedItem = null;
        this.draggedFrom = null;
        this.draggedIndex = null;
        this.isDragging = false;
    },
}));

Alpine.start();
