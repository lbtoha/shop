import { select2Init } from "@/shared/js/select2";
import Alpine from "alpinejs";
import $ from "jquery";

Alpine.data("metaAdd", () => ({
    page: null,
    selectedItems: [],
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

const kycTypeSelect = select2Init("kyc_type_select");

kycTypeSelect.on("change", function (e) {
    const kycType = e.target.value;
    if (kycType == "select") {
        $("#option_container").show();
    } else {
        $("#option_container").hide();
    }
    if (kycType == "file") {
        $("#file_container").show();
    } else {
        $("#file_container").hide();
    }
});

const kycTypeSelect2 = select2Init("kyc_type_select2");

kycTypeSelect2.on("change", function (e) {
    const kycType = $(this).val();
    if (kycType == "select") {
        $("#option_container2").show();
    } else {
        $("#option_container2").hide();
    }

    if (kycType == "file") {
        $("#file_container2").show();
    } else {
        $("#file_container2").hide();
    }
});

$("#add_option2").on("click", function () {
    $("#option_list2").append('<input type="text" name="options[]" class="text-input" placeholder="Enter Option" required>');
    kycTypeSelect2.trigger("change");
});

$("#add_option").on("click", function () {
    $("#option_list").append('<input type="text" name="options[]" class="text-input" placeholder="Enter Option" required>');
    kycTypeSelect2.trigger("change");
});

$(".set-edit-modal-form-data").on("click", function (e) {
    const data = $(this).data("row");
    // set selected data select2
    $("#kyc_type_select2").val(data.field_type).trigger("change");
    if (data.field_type == "select") {
        $("#option_container2").show();
        $("#option_list2").html("");
        data.options.forEach((option) => {
            $("#option_list2").append('<input type="text" name="options[]" class="text-input" placeholder="Enter Option" required value="' + option + '">');
        });
    } else {
        $("#option_container").hide();
    }

    if (data.field_type == "file") {
        $("#file_container2").show();
        // set selected options for file types which is multi select select 2
        $("#file_container2").find(".select-2").val(data.options).trigger("change");
    } else {
        $("#file_container2").hide();
    }
});
