"use strict";
import $, { ajax } from "jquery";
import Swal from "sweetalert2/dist/sweetalert2.js";
import "sweetalert2/src/sweetalert2.scss";

function isDarkMode() {
    return document.documentElement.classList.contains("dark");
}

const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
});

export default Toast;

export function toastAlert(action, buttonText = "Yes, Confirm it!") {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
    });

    swalWithBootstrapButtons
        .fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: buttonText,
            cancelButtonText: "No, cancel!",
            cancelButtonColor: "#FF0000.",
            reverseButtons: true,
            buttonsStyling: false,
            didOpen: (modal) => {
                if (isDarkMode()) {
                    modal.style.backgroundColor = "var(--color-neutral-903)";
                    modal.style.color = "var(--color-neutral-20)";
                } else {
                    modal.style.backgroundColor = "var(--color-neutral-20)";
                    modal.style.color = "var(--color-neutral-500)";
                }
            },
        })
        .then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append("_method", "DELETE");
                formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
                ajax({
                    url: action,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastSuccess(data?.message);
                        location.reload();
                    },
                    error: function (data) {
                        const error = data.responseJSON.message;
                        if (error) {
                            toastError(error);
                        }
                    },
                });
            }
        });
}

export function toastSuccess(title = "Success") {
    Toast.fire({
        icon: "success",
        title,
        padding: "10px 20px",
    });
}

export function toastError(title = "Error") {
    Toast.fire({
        icon: "error",
        title,
        padding: "10px 20px",
    });
}

export function toastWarning(title = "Warning") {
    Toast.fire({
        icon: "warning",
        title,
        padding: "10px 20px",
    });
}

export function toastConfirm(config) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
    });

    swalWithBootstrapButtons
        .fire({
            title: config.title || "Are you sure?",
            text: config.text || "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: config.confirmButtonText || "Confirm",
            cancelButtonText: "Cancel",
            reverseButtons: true,
            didOpen: (modal) => {
                if (isDarkMode()) {
                    modal.style.backgroundColor = "var(--color-neutral-903)";
                    modal.style.color = "var(--color-neutral-20)";
                } else {
                    modal.style.backgroundColor = "var(--color-neutral-20)";
                    modal.style.color = "var(--color-neutral-500)";
                }
            },
        })
        .then((result) => {
            if (result.isConfirmed) {
                if (config.action) {
                    if (config.method === "REDIRECT") {
                        location.href = config.action;
                    } else {
                        const formData = new FormData();
                        formData.append("_method", config.method || "POST");
                        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
                        if (config.data) {
                            Object.entries(config.data).forEach(([key, value]) => {
                                if (Array.isArray(value)) {
                                    value.forEach((v) => formData.append(`${key}[]`, v));
                                } else {
                                    formData.append(key, value);
                                }
                            });
                        }
                        ajax({
                            url: config.action,
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (data) {
                                toastSuccess(data?.message);
                                location.reload();
                            },
                            error: function (data) {
                                const error = data.responseJSON?.message;
                                if (error) {
                                    toastError(error);
                                }
                            },
                        });
                    }
                } else if (config.form) {
                    config.form.trigger("submit");
                }
            }
        });
}

function showSessionToast() {
    const toastElement = document.getElementById("session-toast");
    if (toastElement) {
        const type = toastElement.getAttribute("data-type");
        const message = toastElement.getAttribute("data-message");
        Toast.fire({
            icon: type,
            title: message,
            padding: "10px 20px",
        });
    }
}
showSessionToast();
