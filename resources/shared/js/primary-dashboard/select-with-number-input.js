"use strict";
import $ from "jquery";
export const initSelectWithNumberInput = () => {
    const select = $(".number-select");
    select.on("change", function () {
        const value = $(this).val();
        if (value == "percentage") {
            $(this).parent().parent().find("span").html("%");
        } else {
            $(this).parent().parent().find("span").html("$");
        }
    })
}
