"use strict";

import $ from "jquery";
import intlTelInput from "intl-tel-input";
import { select2InitByClass } from "./select2";
import { handleFormUpdateSubmit } from "./form-submit";

$(async function () {
    select2InitByClass();
    // Initialize intl-tel-input
    const inputElement = document.querySelector("#phone_number");

    if (inputElement) {
        const iti = intlTelInput(inputElement, {
            initialCountry: "us",
            separateDialCode: true,
            hiddenInput: (telInputName) => ({
                phone: "phone",
                country: "country_code",
            }),
            loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.0/build/js/utils.js"),
        });

        let form = document.querySelector(".user-information-update");

        if (form) {
            form.addEventListener("submit", function (e) {
                // Update phone input with formatted number
                const fullPhoneNumber = iti.getNumber();
                $('input[name="phone"]').val(fullPhoneNumber);
            });
        }
    }

    // Now call your function after setting up the event listener
    handleFormUpdateSubmit("user-information-update");
});
