"use strict";
import $ from "jquery";
import { initializeCodeMirror } from "@/shared/js/primary-dashboard/codemirror-initialize";
import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";

initializeCodeMirror("editorContainer");
fileManagerInitByClass("file-uploader", "image");

// Cache selectors
const $adsence = $("#adsense-fields");
const $adsterra = $("#adsterra");
const $html = $("#html-code");
const $image = $("#image");
const $link = $("#link");
const $select = $(".select-2");

function toggleFields(type) {
    $adsence.hide();
    $adsterra.hide();
    $html.hide();
    $image.hide();
    $link.hide();
    switch (type) {
        case "adsense":
            $adsence.show();
            break;
        case "adsterra":
            $adsterra.show();
            break;
        case "custom":
            $html.show();
            break;
        default:
            $image.show();
            $link.show();
    }
}

toggleFields($("#type").val());
$select.on("change", function () {
    if (["adsense", "adsterra", "custom", "image"].includes($(this).val())) {
        toggleFields($(this).val());
    }
});
