"use strict";

import $ from "jquery";

$("#logout").on("click", function (e) {
    e.preventDefault();
    $("#logout-form").trigger("submit");
});
