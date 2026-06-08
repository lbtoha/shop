"use strict";
import { toastError, toastSuccess } from "@/shared/js//toast";
import Alpine from "alpinejs";
import axios from "axios";

Alpine.data("editPage", () => ({
    page: null,
    selectedItems: [],
    availableItems: [],
    draggedItem: null,
    draggedFrom: null,
    draggedIndex: null,
    isDragging: false, // Flag to track if any item is being dragged
    isSaving: false, // Flag to track if saving is in progress

    startDrag(event, source, index) {
        this.draggedItem = source === "selected" ? this.selectedItems[index] : this.availableItems[index];
        this.draggedFrom = source;
        this.draggedIndex = index;
        this.isDragging = true; // Set dragging state
        event.dataTransfer.effectAllowed = "move";
    },

    endDrag() {
        this.isDragging = false; // Reset dragging state
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
                this.availableItems.splice(this.draggedIndex, 1);
            }
        } else if (this.draggedFrom === "available") {
            if (!this.selectedItems.includes(this.draggedItem)) {
                if (targetIndex !== null) {
                    this.selectedItems.splice(targetIndex, 0, this.draggedItem);
                } else {
                    this.selectedItems.push(this.draggedItem);
                    this.availableItems.splice(this.draggedIndex, 1);
                }
            }
        }

        this.resetDragState();
    },
    deleteItem(index) {
        this.selectedItems.splice(index, 1);
    },

    resetDragState() {
        this.draggedItem = null;
        this.draggedFrom = null;
        this.draggedIndex = null;
        this.isDragging = false; // Reset dragging state
    },

    savePage() {
        this.isSaving = true;
        // Handle saving logic here
        const items = this.selectedItems.map((item, index) => ({
            id: item.id,
            order: index,
        }));
        axios
            .post(`/admin/settings/theme/pages/${this.page?.id}/update-section`, {
                sections: items,
            })
            .then((response) => {
                toastSuccess(response?.data?.message);
                this.isSaving = false;
            })
            .catch((error) => {
                let message = error?.response?.data?.message;
                toastError(message);
                this.isSaving = false;
            });
    },
    init() {
        if (this.selectedItems.length) {
            // Create a Set of slugs from selectedItems for faster lookup
            const selectedSlugs = new Set(this.selectedItems.map((item) => item.slug));

            // Filter availableItems to exclude items that are in selectedItems
            this.availableItems = this.availableItems.filter((item) => !selectedSlugs.has(item.slug));
        }
    },
}));

Alpine.start();
