"use strict";
// user details page will need
import { select2Init } from "@/shared/js/select2";
import $ from "jquery";
$(function () {
    const actionFormIdList = {
        add_money: "add-money-form",
        subtract_money: "subtract-money-form",
    };

    Object.values(actionFormIdList).forEach((value) => {
        $(`#${value}`).addClass("hidden");
    });

    const userActionSelect = select2Init("user-action");

    userActionSelect.on("select2:select", function (e) {
        const selectedValue = e.target.value;
        if (actionFormIdList[selectedValue]) {
            $(`#${actionFormIdList[selectedValue]}`).removeClass("hidden");
            Object.entries(actionFormIdList).forEach(([key, value]) => {
                if (selectedValue !== key) {
                    $(`#${value}`).addClass("hidden");
                }
            });
        }
    });

    const selectedValue = userActionSelect.val();
    if (actionFormIdList[selectedValue]) {
        $(`#${actionFormIdList[selectedValue]}`).removeClass("hidden");
    } else {
        Object.entries(actionFormIdList).forEach(([key, value]) => {
            if (selectedValue !== key) {
                $(`#${value}`).addClass("hidden");
            }
        });
    }
});
