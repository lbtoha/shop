import { editorInit } from "@/shared/js/editor";
import { closeModal } from "@/shared/js/Modal";
import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";
import { select2InitByClassForTags } from "@/shared/js/select2";
import Alpine from "alpinejs";
import $ from "jquery";

fileManagerInitByClass("file-uploader", "image");

$('input[name="is_free"]').on("change", function () {
    if (this.checked) {
        $("#point_to_pass").addClass("hidden");
    } else {
        $("#point_to_pass").removeClass("hidden");
    }
});

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
            tags: [],
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
