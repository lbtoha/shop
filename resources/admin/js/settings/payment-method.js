"use strict";
import { closeModal } from "@/shared/js/Modal";
import { fileManagerInit } from "@/shared/js/primary-dashboard/file-manager";
import { select2Init } from "@/shared/js/select2";
import { toastError } from "@/shared/js/toast";
import Alpine from "alpinejs";
import $ from "jquery";

select2Init("supported_currencies");
select2Init("field_type");
fileManagerInit("image", "image");

$(".handle_add_new_extra_field").on("submit", function (e) {
    e.preventDefault();
    const item = {
        label: $(this).find('input[name="label"]').val(),
        field_type: $(this).find('select[name="field_type"]').val(),
        required: $(this).find('input[name="required"]').is(":checked") ? 1 : 0,
    };
    if (!item.label || !item.field_type) {
        toastError("Please fill all the required fields");
    } else {
        window.AddNewField(item);

        closeModal();
    }
});

$(".handle_edit_extra_field").on("submit", function (e) {
    e.preventDefault();
    const item = {
        label: $(this).find('input[name="label"]').val(),
        field_type: $(this).find('select[name="field_type"]').val(),
        required: $(this).find('input[name="required"]').is(":checked") ? 1 : 0,
    };
    window.updateItem($(this).find('input[name="field_id"]').val(), item);
    closeModal();
});

$("#copy_button").on("click", async function () {
    let copyText = $("#webhook_url").val();
    try {
        await navigator.clipboard.writeText(copyText);
        // Show copied message
        $("#copy_message").removeClass("hidden").fadeIn().delay(1500).fadeOut();
    } catch (error) {
        console.error("Failed to copy text: ", error);
    }
});

// its for create method
Alpine.data("metaAdd", () => ({
    page: null,
    selectedItems: [],
    draggedItem: null,
    draggedFrom: null,
    draggedIndex: null,
    isDragging: false, // Flag to track if any item is being dragged

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
    editItem(index) {
        const item = this.selectedItems[index];
        // Set data in the modal
        const modal = document.getElementById("meta_extra_field_edit_modal");
        modal.querySelector('input[name="label"]').value = item.label;
        modal.querySelector('select[name="field_type"]').value = item.field_type;
        modal.querySelector('input[name="required"]').checked = item.required === 1 ? 1 : 0;
        modal.querySelector('input[name="field_id"]').value = index;
    },
    updateItem(index, item) {
        index = parseInt(index);
        this.selectedItems[index] = {
            ...this.selectedItems[index],
            ...item,
        };
        // update order
        this.selectedItems.forEach((item, index) => {
            item.order = index;
        });
    },
    resetDragState() {
        this.draggedItem = null;
        this.draggedFrom = null;
        this.draggedIndex = null;
        this.isDragging = false; // Reset dragging state
    },
    addNewField(item) {
        this.selectedItems.push({
            ...item,
            order: this.selectedItems.length,
        });
    },
    init() {
        window.AddNewField = this.addNewField.bind(this);
        window.updateItem = this.updateItem.bind(this);
    },
}));

Alpine.start();
