import Alpine from "alpinejs";
import { datePickerInit } from "@/shared/js/primary-dashboard/datepicker";
import { select2InitByClassForTags } from "@/shared/js/select2";
import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";


fileManagerInitByClass("file-uploader", "image");

document.addEventListener("alpine:initialized", () => {
    select2InitByClassForTags("tags-select-2");
});

Alpine.start();

datePickerInit("date-picker", true);



