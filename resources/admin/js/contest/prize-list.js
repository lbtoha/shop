"use strict";
import Alpine from "alpinejs";

Alpine.data("metaAdd", () => ({
    contest: null,
    selectedItems: [],
    questionlevel: null,
    draggedItem: null,
    draggedFrom: null,
    draggedIndex: null,
    isDragging: false, // Flag to track if any item is being dragged

    startDrag(event, source, index) {
        this.draggedItem = source === "selected" ? this.selectedItems[index] : [];
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
            }
        }

        this.resetDragState();
        // update order
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
        this.isDragging = false; // Reset dragging state
    },
}));

Alpine.start();

