"use strict";

import $, { ajax } from "jquery";
import { destroySelect2, select2Init } from "./select2";
import { toastAlert, toastConfirm, toastError, toastSuccess } from "./toast";

// set modal form data when data is available, it's for update
$(document).on("click", ".set-create-modal-form-data", function (e) {
    const modalId = $(this).data("modal-target");
    const data = $(this).data("row");
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector("form");
        if (form) {
            form.action = $(this).data("action");
            // Set form data when data is available, it's for update
            if (data) {
                for (const [key, value] of Object.entries(data)) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input && ["text", "number", "email"].includes(input.type)) {
                        input.value = value;
                    }
                    if (input && ["checkbox", "radio"].includes(input.type)) {
                        input.checked = value;
                    }
                    if (input && ["hidden"].includes(input.type)) {
                        const checkbox = form.querySelectorAll(`[name="${key}"]`);
                        checkbox.forEach((item) => {
                            if (item.type === "checkbox" && item.value == value) {
                                item.checked = true;
                            } else {
                                item.checked = false;
                            }
                        });
                    }
                }
            } else {
                form.reset();
            }
        }
    }
});
// set modal form data when data is available, it's for update
$(document).on("click", ".set-edit-modal-form-data", function (e) {
    const modalId = $(this).data("modal-target");
    const data = $(this).data("row");
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector("form");
        if (form) {
            form.action = $(this).data("action");
            // Set form data when data is available, it's for update
            if (data) {
                for (const [key, value] of Object.entries(data)) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (value instanceof Array) {
                        value.forEach((item) => {
                            const inputFromValue = form.querySelector(`[value="${item}"]`);
                            if (inputFromValue && ["checkbox", "radio"].includes(inputFromValue.type)) {
                                inputFromValue.checked = inputFromValue.value == item;
                            }
                        });
                    }

                    if (input?.localName === "select") {
                        const selected = input.querySelector(`option[value="${value}"]`);
                        if (selected) {
                            input.querySelectorAll("option[selected]")?.forEach((option) => {
                                option.removeAttribute("selected");
                            });
                            selected.setAttribute("selected", "selected");
                        }

                        destroySelect2(input.getAttribute("id"));
                        select2Init(input.getAttribute("id"));
                    }

                    if (input && ["textarea"].includes(input.tagName.toLowerCase())) {
                        input.textContent = value;
                    }

                    if (input && ["text", "number", "email"].includes(input.type)) {
                        input.value = value;
                    }

                    if (input && ["checkbox", "radio"].includes(input.type)) {
                        input.checked = input?.value == value;
                    }
                    if (input && ["hidden"].includes(input.type)) {
                        const checkbox = form.querySelectorAll(`[name="${key}"]`);
                        checkbox.forEach((item) => {
                            if (item.type === "checkbox" && item.value == value) {
                                item.checked = true;
                            } else {
                                item.checked = false;
                            }
                        });
                    }
                }
                // Set method to PUT
                const methodInput = document.createElement("input");
                methodInput.type = "hidden";
                methodInput.name = "_method";
                methodInput.value = "PUT";
                form.appendChild(methodInput);
            } else {
                form.reset();
            }
        }
    }
});

export function handleAddFormSubmit(className = "form-submit-add") {
    $(`.${className}`).on("submit", function (e) {
        e.preventDefault();
        const self = this;
        const button = $(this).find("button[type=submit]");
        button.attr("disabled", true);
        button.find(".btn-spinner").removeClass("hidden");
        button.find(".btn-text").addClass("hidden");

        ajax({
            url: $(this).attr("action"),
            method: $(this).attr("method"),
            data: removeDuplicateKey(new FormData(this), this),
            processData: false,
            contentType: false,
            success: function (data) {
                button.find(".input-error").removeClass("input-error");
                button.find(".input-text-error").remove();
                button.trigger("reset");
                toastSuccess(data?.message);

                if (data?.redirect) {
                    location.href = data?.redirect;
                } else if (data?.reload) {
                    reloadPage();
                }
                // closeModal();
                button.find(".btn-spinner").addClass("hidden");
                button.find(".btn-text").removeClass("hidden");
                button.attr("disabled", false);
            },
            error: function (data) {
                const errors = data.responseJSON.errors;
                if (errors) {
                    for (const [key, value] of Object.entries(errors)) {
                        validationErrorMessageShow(self, key, value);
                    }
                }
                const error = data.responseJSON.message;
                if (error) {
                    toastError(error);
                }
                $(self).find("button[type=submit]").attr("disabled", false);
                $(self).find(".btn-spinner").addClass("hidden");
                $(self).find(".btn-text").removeClass("hidden");
            },
        });
    });
}
handleAddFormSubmit();

export function handleFormUpdateSubmit(className = "form-submit-edit") {
    $(`.${className}`).on("submit", function (e) {
        e.preventDefault();
        const self = this;
        const button = $(this).find("button[type=submit]");
        button.attr("disabled", true);
        button.find(".btn-spinner").removeClass("hidden");
        button.find(".btn-text").addClass("hidden");
        ajax({
            url: $(this).attr("action"),
            method: $(this).attr("method"),
            data: removeDuplicateKey(new FormData(this), this),
            processData: false,
            contentType: false,
            success: function (data) {
                button.find(".input-error").removeClass("input-error");
                button.find(".input-text-error").remove();
                toastSuccess(data?.message);
                const redirect = data?.redirect;
                const isReload = data?.reload;
                if (redirect) {
                    location.href = redirect;
                } else if (isReload) {
                    reloadPage();
                }
                button.find(".btn-spinner").addClass("hidden");
                button.find(".btn-text").removeClass("hidden");
                button.attr("disabled", false);
            },
            error: function (data) {
                const errors = data.responseJSON.errors;
                if (errors) {
                    for (const [key, value] of Object.entries(errors)) {
                        validationErrorMessageShow(self, key, value);
                    }
                }
                const error = data.responseJSON.message;
                if (error) {
                    toastError(error);
                }

                const issues = data.responseJSON.issues;
                if (issues) {
                    $("#validation-error-container").empty().parent().removeClass("hidden");
                    for (const [key, value] of Object.entries(issues)) {
                        $("#validation-error-container").append(
                            `<span class="block text-danger pb-1">
                                ${value}
                            </span>`
                        );
                    }
                }

                $(self).find("button[type=submit]").attr("disabled", false);
                $(self).find(".btn-spinner").addClass("hidden");
                $(self).find(".btn-text").removeClass("hidden");
            },
        });
    });
}
handleFormUpdateSubmit();

$(document).on("click", ".delete-item-action", function (e) {
    const action = $(this).data("action");
    toastAlert(action);
});
$(document).on("click", ".action-confirm-btn", function (e) {
    e.preventDefault();
    const config = {
        title: $(this).attr("title") || "Are you sure?",
        text: $(this).attr("text") || "To perform this action please confirm",
    };

    const action = $(this).attr("action");

    if (action) {
        config.action = action;
        config.method = $(this).attr("method") || "REDIRECT";
        const data = {};
        $.each(this.attributes, function() {
            if (this.name.startsWith('data-')) {
                const key = this.name.substring(5);
                data[key] = this.value;
            }
        });
        if (Object.keys(data).length > 0) {
            config.data = data;
        }
    } else {
        config.form = $(this).closest("form");
    }

    toastConfirm(config);
});

function validationErrorMessageShow(self, key, value) {
    let input = $(self)
        .find(`[name="${arrayKeyModifier(key)}"]`)
        .first();
    input = input.length > 0 ? input : $(self).find(`[name="${arrayKeyModifier(key)}[]"]`);

    if (!input.length) {
        return;
    }

    if (["radio", "files", "images"].includes(input.attr("type"))) {
        input = input.parent().parent();
    }

    if (input.siblings("#phone_number").length > 0) {
        input.siblings("#phone_number").addClass("input-error");
        input = input.parent();
    }

    if (input.length > 0 && input.find("#phone_number").length <= 0) {
        input.addClass("input-error");
    }
    // file manager
    if (input.attr("id") == "thumbnail-image") {
        input.removeClass("input-error");
        input.closest(".file-manager-container").addClass("input-error");
        if (input.closest(".input-group").find(".input-text-error").length === 0) {
            input.closest(".input-group").append(`<span class="input-text-error">${value}</span>`);
        }
        return;
    }

    if (input.closest(".input-group")) {
        input = input.closest(".input-group");
        input.find(".input-text-error").remove();
        input.append(`<span class="input-text-error">${value}</span>`);
        return;
    }

    if (input.parent().find(".input-text-error").length === 0) {
        input.parent().append(`<span class="input-text-error"></span>`);
    }

    let errorMessage = input.parent().find(".input-text-error");

    errorMessage.text(value);
}

/**
 * Remove duplicate keys from a FormData object.
 *
 * @param {FormData} data
 * @returns {FormData}
 */
export function removeDuplicateKey(data, form) {
    const formData = new FormData();

    // Group entries by key to handle multiple values
    const entriesByKey = {};

    // Collect all form values
    data.forEach((value, key) => {
        if (!entriesByKey[key]) {
            entriesByKey[key] = [];
        }
        // Only add non-null/undefined values
        if (value !== null && value !== undefined) {
            entriesByKey[key].push(value);
        }
    });

    // Process each group of entries
    Object.entries(entriesByKey).forEach(([key, values]) => {
        const input = form.querySelector(`[name="${key}"]`);

        // Handle checkbox inputs specifically
        if (input?.type === "checkbox") {
            // If the checkbox is checked, use the explicit value (usually '1')
            // If unchecked, use the hidden input value (usually '0')
            const value = input.checked ? values[0] : values[values.length - 1];
            formData.append(key, value);
        }
        // Handle multiple select inputs
        else if (input?.multiple) {
            values.forEach((value) => formData.append(key, value));
        }
        // Handle array inputs (inputs with name ending in [])
        else if (key.endsWith("[]")) {
            values.forEach((value) => formData.append(key, value));
        }
        // For all other inputs, just use the first value
        else {
            formData.append(key, values[0] || "");
        }
    });

    return formData;
}

// Helper function to convert FormData to object (useful for debugging)
export function formDataToObject(formData) {
    const object = {};
    formData.forEach((value, key) => {
        // Handle array inputs
        if (key.endsWith("[]")) {
            const k = key.slice(0, -2);
            if (!object[k]) {
                object[k] = [];
            }
            object[k].push(value);
        }
        // Handle multiple values for same key
        else if (object[key]) {
            if (!Array.isArray(object[key])) {
                object[key] = [object[key]];
            }
            object[key].push(value);
        }
        // Handle single values
        else {
            object[key] = value;
        }
    });
    return object;
}

const reloadPage = (after = 1500) => {
    setTimeout(() => {
        location.reload();
    }, after);
};

const arrayKeyModifier = (key) => {
    if (key.includes(".")) {
        return key.replace(/\.(\w+)/g, "[$1]");
    }
    return key;
};

$("input, textarea, select").on("input change keyup blur", function () {
    removeErrorMessage($(this));
});

export function removeErrorMessage(input) {
    input.closest(".input-group").find(".input-error").removeClass("input-error");
    input.closest(".input-group").find(".input-text-error").remove();
}
