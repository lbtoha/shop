"use strict";
import { select2InitByClassForTags } from "@/shared/js/select2";
import $ from "jquery";

import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";
fileManagerInitByClass("file-uploader", "image");

import { initializeCodeMirror } from "@/shared/js/primary-dashboard/codemirror-initialize";
initializeCodeMirror("editorContainer", ``);

select2InitByClassForTags("tags-select-2");

$("#extra_field_container").on("click", function () {
    const row_container = $("#row_container");
    const uniqueId = Date.now(); // Generate a unique ID for each new row
    const rowCount = row_container.children().length;
    row_container.append(
        `<div class="space-y-4 border border-neutral-30 dark:border-neutral-500 p-3 rounded-lg  relative" id="meta-row-${uniqueId}">
            <div class="input-group">
                <label for="name" class="block mb-2 text-sm">Name</label>
                <input type="text" class="text-input" id="name-${uniqueId}" value="" name="meta[${rowCount}][name]"
                        placeholder="Enter Text" />
                <span class="input-text-error"></span>
            </div>
            <div class="input-group">
                <label for="textarea" class="block mb-2 text-sm">Content</label>
                <textarea name="meta[${rowCount}][content]" rows="4" class="text-input"
                                        placeholder="Address" id="address"></textarea>
                <span class="input-text-error"></span>
            </div>
            <button type="button" class="text-red-500 cursor-pointer mt-2 delete-meta absolute right-2 -top-1 hover:text-red-900" data-id="${uniqueId}">
                <i class="ph ph-trash"></i>
            </button>
        </div>`
    );
});

// Event delegation for dynamically added delete buttons
$(document).on("click", ".delete-meta", function () {
    const id = $(this).data("id");
    $(`#meta-row-${id}`).remove();
});
