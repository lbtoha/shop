"use strict";
import $ from "jquery";
$(function () {
    $(".toggle-password-eye").each(function () {
        $(this).hide();
    });
    $(".toggle-password-eye-close").each(function () {
        $(this).show();
    });
});

$(".toggle-password").each(function () {
    $(this).on("click", function () {
        $(this).toggleClass("active");
        if ($(this).hasClass("active")) {
            $(this).find(".toggle-password-eye").show();
            $(this).find(".toggle-password-eye-close").hide();
            $(this).parent().find("input").attr("type", "text");
        } else {
            $(this).find(".toggle-password-eye").hide();
            $(this).find(".toggle-password-eye-close").show();
            $(this).parent().find("input").attr("type", "password");
        }
    });
});

$("#logout-btn").on("click", function () {
    $("#logout-form").on("submit", function (e) {
        e.preventDefault();
        $(this).trigger("submit");
    });
});
