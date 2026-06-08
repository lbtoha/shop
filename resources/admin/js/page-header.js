"use strict";

import $ from "jquery";
import moment from "moment";
import { datePicker } from "../../shared/js/primary-dashboard/datepicker";

const picker = datePicker("date-range");

if (picker) {
    picker.on("selected", (date1, date2) => {
        let url = new URL(window.location.href);
        url.searchParams.set("start_date", moment(date1.dateInstance).format("YYYY-MM-DD"));
        url.searchParams.set("end_date", moment(date2.dateInstance).format("YYYY-MM-DD"));
        window.location.href = url.href;
    });
}

const customPicker = datePicker("custom-date-range");

if (customPicker) {
    customPicker.on("selected", (date1, date2) => {
        let url = new URL(window.location.href);
        url.searchParams.set("custom_start_date", moment(date1.dateInstance).format("YYYY-MM-DD"));
        url.searchParams.set("custom_end_date", moment(date2.dateInstance).format("YYYY-MM-DD"));
        window.location.href = url.href;
    });
}

$("#table-header-search-btn").on("click", function () {
    let url = new URL(window.location.href);
    url.searchParams.set("search", $("#search").val());
    window.location.href = url.href;
});
