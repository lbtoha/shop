import { editorInit } from "@/shared/js/editor";
import { closeModal } from "@/shared/js/Modal";
import { datePickerInit } from "@/shared/js/primary-dashboard/datepicker";
import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";
import { select2InitByClassForTags } from "@/shared/js/select2";
import { toastError } from "@/shared/js/toast";
import Alpine from "alpinejs";
import $ from "jquery";

datePickerInit("date-picker", true);
fileManagerInitByClass("file-uploader", "image");

Alpine.data("langData", () => ({
    locals: [],
    languages: [],
    addNewLang() {
        const selectedLocal = $("#lang").val();
        const alreadyExists = this.locals.find((local) => local.locale == selectedLocal);
        if (alreadyExists) {
            toastError("Language already exists");
            return;
        }
        this.locals.push({
            locale: selectedLocal,
            level: this.languages.find((lang) => lang.code == selectedLocal).name,
            title: "",
            description: "",
        });
        closeModal();
        this.$nextTick(() => {
            select2InitByClassForTags("tags-select-2");
            editorInit();
        });
    },
    removeLang(index) {
        this.locals.splice(index, 1);
    },
}));

document.addEventListener("alpine:initialized", () => {
    select2InitByClassForTags("tags-select-2");
    editorInit();
});

Alpine.start();
