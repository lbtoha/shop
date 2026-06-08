import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";
import $ from "jquery";
fileManagerInitByClass("file-uploader", "image");

$("#criteria-description").text($("#criteria_type").find("option:selected").data("description"));

$("#criteria_type").on("change", function () {
    const description = $(this).find("option:selected").data("description");
    $("#criteria-description").text(description);
});
