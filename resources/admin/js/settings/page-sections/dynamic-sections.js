"use strict";

import { fileManagerGallery, fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";
import $ from "jquery";

fileManagerInitByClass("file-uploader", "image");

$(function () {
    // gallery
    fileManagerGallery("add_gallery_image");

    $("#gallery_container").on("click", ".remove-file", function (e) {
        const images = $("#gallery").val();
        if (images) {
            const imageArray = JSON.parse(images);
            const updatedImages = imageArray.filter((image) => image.id !== parseInt($(this).data("id")));
            $("#gallery").val(JSON.stringify(updatedImages));
        }

        $(this).parent().remove();
    });

    $(".embedded-input").on("input", function (e) {
        let value = $(this).val();
        const iframeMatch = value.match(/<iframe[^>]*src="([^"]*)"[^>]*>/);
        if (iframeMatch) {
            value = iframeMatch[1]; // Extract the src value from the iframe
        }
        $(this).val(value);
    });
});
