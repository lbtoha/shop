import $ from "jquery";
import { getUrlType } from "../helper";

export const fileManagerInit = (id, type = "image", isMultiple = false) => {
    (function ($) {
        $.fn.filemanager = function (type, options) {
            type = type || "file";

            this.on("click", function (e) {
                const route_prefix = options?.prefix ? options.prefix : `${getUrlType()}/filemaneger`;

                const target_input = $("#" + $(this).data("input"));
                const target_preview = $("#" + $(this).data("preview"));
                window.open(route_prefix + "?type=" + type + "&multiple=0", "FileManager", "width=900,height=600");
                window.SetUrl = function (items) {
                    const file_path = items
                        .map(function (item) {
                            return new URL(item.url).pathname;
                        })
                        .join(",");
                    // set the value of the desired input to image url
                    target_input.val("").val(file_path).trigger("change");
                    // clear previous preview
                    target_preview.html("");
                    // set or change the preview image src
                    items.forEach(function (item) {
                        target_preview.append($("<img>").css("height", "5rem").attr("src", item.url));
                        target_preview.addClass("mt-4");
                    });

                    // trigger change event
                    target_preview.trigger("change");
                };
                return false;
            });
        };
    })($);

    $(`#${id}`).filemanager(type, {
        prefix: `${getUrlType()}/filemaneger`,
    });
};

export const fileManagerInitByClass = (className, type = "image") => {
    (function ($) {
        $.fn.filemanager = function (type, options) {
            type = type || "file";

            this.on("click", function (e) {
                var route_prefix = options && options.prefix ? options.prefix : `${getUrlType()}/filemaneger`;

                const parentElement = $(this).closest(".file-manager-container");
                var target_input = parentElement.find("input");
                var target_preview = parentElement.find(".preview");
                window.open(route_prefix + "?type=" + type + "&multiple=0", "FileManager", "width=1900,height=800");
                window.SetUrl = function (items) {
                    var file_path = items
                        .map(function (item) {
                            return new URL(item.url).pathname;
                        })
                        .join(",");
                    // set the value of the desired input to image url
                    target_input.val("").val(file_path).trigger("change");
                    // clear previous preview
                    target_preview.html("");
                    // set or change the preview image src
                    items.forEach(function (item) {
                        target_preview.append($("<img>").css("height", "5rem").attr("src", item.url));
                        target_preview.addClass("mt-4");
                    });

                    // trigger change event
                    target_preview.trigger("change");
                };
                return false;
            });
        };
    })($);

    $(`.${className}`).each(function (index, element) {
        $(this).filemanager(type, {
            prefix: `${getUrlType()}/filemaneger`,
        });
    });
};

export const fileManagerPreviewOnImg = (id, type = "image") => {
    (function ($) {
        $.fn.filemanager = function (type, options) {
            type = type || "file";

            this.on("click", function (e) {
                var route_prefix = options && options.prefix ? options.prefix : `${getUrlType()}/filemaneger`;

                var target_input = $("#" + $(this).data("input"));
                var target_preview = $("#" + $(this).data("preview"));
                window.open(route_prefix + "?type=" + type + "&multiple=0", "FileManager", "width=900,height=600");
                window.SetUrl = function (items) {
                    var file_path = items
                        .map(function (item) {
                            return new URL(item.url).pathname;
                        })
                        .join(",");
                    // set the value of the desired input to image url
                    target_input.val("").val(file_path).trigger("change");
                    // clear previous preview
                    target_preview.html("");
                    // set or change the preview image src
                    items.forEach(function (item) {
                        target_preview.attr("src", item.url);
                        target_preview.addClass("mt-4");
                    });

                    // trigger change event
                    target_preview.trigger("change");
                };
                return false;
            });
        };
    })($);

    $(`#${id}`).filemanager(type, {
        prefix: `${getUrlType()}/filemaneger`,
    });
};

export const fileManagerGallery = (id, type = "image") => {
    (function ($) {
        $.fn.filemanager = function (type, options) {
            type = type || "file";

            this.on("click", function (e) {
                var route_prefix = options && options.prefix ? options.prefix : `${getUrlType()}/filemaneger`;

                var target_input = $("#" + $(this).data("input"));
                var target_preview = $("#" + $(this).data("preview"));
                window.open(route_prefix + "?type=" + type + "&multiple=1", "FileManager", "width=900,height=600");
                window.SetUrl = function (items) {
                    var file_paths = items.map(function (item, index) {
                        return {
                            url: new URL(item.url).pathname,
                            id: index,
                        };
                    });
                    let total_files = file_paths;
                    // set the value of the desired input to image url
                    let previousValue = target_input.val();

                    if (previousValue) {
                        total_files = [...JSON.parse(previousValue), ...file_paths];
                    }

                    const updatedFiles = total_files.map((item, index) => ({ url: item.url, id: index }));

                    target_input.val(JSON.stringify(updatedFiles)).trigger("change");

                    target_preview.html("");
                    // set or change the preview image src
                    updatedFiles.map(function (item, index) {
                        target_preview.append(
                            `<div class="w-full cursor-pointer relative flex items-center justify-center border border-neutral-30 dark:border-neutral-500 p-4 h-[200px] rounded-md">
                                <img src="${item.url}" alt="" class="w-full h-full" />
                                <i data-id="${item.id}" class="remove-file ph ph-trash text-24 text-red-500 absolute top-2 right-2 cursor-pointer"></i>
                            </div>`
                        );
                    });

                    target_preview.append(`
                    <button id="${id}" data-input="gallery" data-preview="gallery_container" type="button"
                        class="w-full cursor-pointer flex items-center justify-center border border-neutral-30 dark:border-neutral-500 p-4 h-[200px] rounded-md">
                        <i class="ph ph-plus"></i>
                        <span>Add Image</span>
                    </button>
                    `);

                    // trigger change event
                    target_preview.trigger("change");
                    fileManagerGallery(id);
                };
                return false;
            });
        };
    })($);

    $(`#${id}`).filemanager(type, {
        prefix: `${getUrlType()}/filemaneger`,
        multiple: true,
    });
};
