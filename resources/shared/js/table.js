"use strict";
import $ from "jquery";

$(function () {
    $("#perPageAction").on("change", function () {
        let url = new URL(window.location.href);
        url.searchParams.set("per_page", this.value);
        window.location.href = url.href;
    });
});

