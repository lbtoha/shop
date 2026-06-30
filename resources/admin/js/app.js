import "../../shared/js/primary-dashboard/app.js";
import "@popperjs/core";
import "./auth.js";
import "./logout.js";
import "./page-header.js";
import "../../shared/js/form-submit.js";
import "../../shared/js/table.js";
import "../../shared/js/toast.js";
import { fileManagerInitByClass } from "../../shared/js/primary-dashboard/file-manager";

$(() => {
    fileManagerInitByClass("file-uploader", "image");

    // Handle clearing the file uploader field
    $(document).on("click", ".clear-file-btn", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = $(this);
        const inputId = btn.data("input");
        const previewId = btn.data("preview");

        const targetInput = $("#" + inputId);
        const targetPreview = $("#" + previewId);

        targetInput.val("").trigger("change");
        targetPreview.html("").trigger("change");
        btn.addClass("hidden");
    });

    // Handle showing/hiding clear button on value changes
    $(document).on("change", ".file-manager-container input", function () {
        const input = $(this);
        const parent = input.closest(".file-manager-container");
        const clearBtn = parent.find(".clear-file-btn");

        if (input.val()) {
            clearBtn.removeClass("hidden");
        } else {
            clearBtn.addClass("hidden");
        }
    });
});
