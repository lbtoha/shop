"use strict";

import { toastError, toastSuccess } from "@/shared/js/toast.js";
import $, { ajax } from "jquery";

$(function () {
    $(".handle-action").on("click", function (e) {
        $(this).find(".btn-spinner").removeClass("hidden");
        $(this).find(".btn-text").addClass("hidden");
        let self = this;
        ajax({
            url: $(this).data("action"),
            method: "POST",
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                toastSuccess(data?.message);
                $(self).find(".btn-spinner").addClass("hidden");
                $(self).find(".btn-text").removeClass("hidden");
                location.reload();
            },
            error: function (data) {
                $(self).find(".btn-spinner").addClass("hidden");
                $(self).find(".btn-text").removeClass("hidden");
                const error = data.responseJSON.message;
                if (error) {
                    toastError(error);
                }
            },
        });
    });
});
