import { editorInit } from "@/shared/js/editor";
import { closeModal } from "@/shared/js/Modal";
import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";
import { select2InitByClass } from "@/shared/js/select2";
import Alpine from "alpinejs";
import $ from "jquery";

// Initialize File Manager
fileManagerInitByClass("file-uploader", "image");

// Alpine component for multilingual article/category data
Alpine.data("articleLangData", () => ({
    locals: [],
    languages: [],
    addNewLang() {
        const selectedLocal = $("#lang_select, #lang_select_edit, #lang_select_article, #lang_select_article_edit").val();
        if (!selectedLocal) return;

        const alreadyExists = this.locals.find((local) => local.locale == selectedLocal);
        if (alreadyExists) {
            toastError("Language already exists");
            return;
        }

        const langObj = this.languages.find((lang) => lang.code == selectedLocal);
        
        // Push a new empty translation object
        this.locals.push({
            locale: selectedLocal,
            lang_name: langObj.name,
            title: "",
            content: "",
        });

        closeModal();
        
        // Re-initialize editors for the new language tab
        this.$nextTick(() => {
            editorInit();
        });
    },
    removeLang(index) {
        this.locals.splice(index, 1);
    },
}));

// Initialize dynamic Select2 for Categories and Tags
const initDynamicSelect2 = () => {
    $(".select2-dynamic").each(function () {
        const placeholder = $(this).attr("label") || "Select Option";
        const multiple = $(this).attr("multiple") === "multiple";

        $(this).select2({
            placeholder: placeholder,
            allowClear: true,
            width: "100%",
            multiple: multiple,
            ajax: {
                url: $(this).data("url"),
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.data || [],
                    };
                },
                cache: true,
            },
        });
    });
};

document.addEventListener("alpine:initialized", () => {
    editorInit();
    initDynamicSelect2();
    select2InitByClass("select-2");
});

Alpine.start();
