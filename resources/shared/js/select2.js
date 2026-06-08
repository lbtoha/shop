import $ from "jquery";
import select2 from "select2";
import "select2/dist/css/select2.min.css";
select2();

export const select2Init = (selectId, placeholder = "Select an option") => {
    if (!selectId) {
        return;
    }
    const $select = $(`#${selectId}`);
    const optionCount = $select.find("option").length;

    $select.select2({
        selectionCssClass: "custom-select",
        width: "100%",
        allowClear: true,
        placeholder,
        minimumResultsForSearch: optionCount > 5 ? 0 : Infinity,
    });

    return $select;
};

export const select2InitByClassForTags = (className, placeholder = null) => {
    if (!className) {
        return;
    }
    $(`.${className}`).each(function () {
        let optionCount = $(this).find("option").length || 0;
        $(this).select2({
            selectionCssClass: "custom-select",
            width: "100%",
            tags: true,
            tokenSeparators: [",", " "],
            createTag: (params) => {
                let term = params.term.trim();
                if (term === "") {
                    return null;
                }
                return { id: term, text: term };
            },
            allowClear: true,
            placeholder: placeholder || $(this).data("placeholder") || "Create a new tags",
            minimumResultsForSearch: optionCount > 5 ? 0 : Infinity,
        });
    });
};

export const select2InitByClass = (className = "select-2", placeholder = null) => {
    if (!className) {
        return;
    }
    $(`.${className}`).each(function () {
        let optionCount = $(this).find("option").length || 0;

        $(this).select2({
            selectionCssClass: "custom-select",
            width: "100%",
            allowClear: true,
            placeholder: placeholder || $(this).data("placeholder") || "Select an option",
            minimumResultsForSearch: optionCount > 5 ? 0 : Infinity,
        });
    });
};

export const select2dynamic = (className = "select2-dynamic", placeholder = null) => {
    if (!className) {
        return;
    }
    $(`.${className}`).each(function () {
        $(this).select2({
            selectionCssClass: "custom-select",
            placeholder: placeholder || $(this).data("placeholder") || "Select an option",
            width: "100%",
            allowClear: true,
            ajax: {
                url: $(this).data("url"),
                type: "get",
                dataType: "json",
                delay: 1000,
                data: (params) => ({
                    search: params.term,
                    page: params.page,
                }),
                processResults: (response, params) => ({
                    results: response?.data?.length ? response?.data.map((item) => ({ text: item.text, id: item.id })) : [],
                    pagination: { more: response.more },
                }),
                cache: false,
            },
            dropdownParent: $(this).parent(),
        });
    });
};

export const destroySelect2 = (selectId) => {
    if (!selectId) {
        return;
    }
    const $select = $(`#${selectId}`);
    if ($select.hasClass("select2-hidden-accessible")) {
        $select.select2("destroy");
    }
};
