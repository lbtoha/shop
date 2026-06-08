"use strict";

import $ from "jquery";
import { select2Init } from "../../../shared/js/select2";
import Alpine from "alpinejs";
import { editorInit } from "../../../shared/js/editor";
import { initializeCodeMirror } from "../../../shared/js/primary-dashboard/codemirror-initialize";
// Editor init
editorInit("email_body_editor");
// code mirror for default template editor
const editor = initializeCodeMirror("editorContainer");

const emailSelect2 = select2Init("email_drivers");

$(function () {
    $("#smtp-config, #mailgun-config, #sendgrid-config, #mailjet-config").hide();

    const selected = emailSelect2.val();

    if (selected === "smtp") {
        $("#smtp-config").show();
    } else if (selected === "mailgun") {
        $("#mailgun-config").show();
    } else if (selected === "sendgrid") {
        $("#sendgrid-config").show();
    } else if (selected === "mailjet") {
        $("#mailjet-config").show();
    }

    // Listen for changes on the mailer dropdown
    emailSelect2.on("change", function () {
        // Get the selected value
        let selectedMailer = $(this).val();

        // Hide all forms first
        $("#smtp-config, #mailgun-config, #sendgrid-config, #mailjet-config").hide();

        // Show the selected form based on the value
        if (selectedMailer === "smtp") {
            $("#smtp-config").show();
        } else if (selectedMailer === "mailgun") {
            $("#mailgun-config").show();
        } else if (selectedMailer === "sendgrid") {
            $("#sendgrid-config").show();
        } else if (selectedMailer === "mailjet") {
            $("#mailjet-config").show();
        }
    });
});

const smsSelect2 = select2Init("sms_drivers");

$(function () {
    $("#twilio-form, #nexmo-form").hide();

    const selected = smsSelect2.val();

    if (selected === "twilio") {
        $("#twilio-form").show();
    } else if (selected === "nexmo") {
        $("#nexmo-form").show();
    }

    // Listen for changes on the mailer dropdown
    smsSelect2.on("change", function () {
        // Get the selected value
        let selectedSmsDriver = $(this).val();

        // Hide all forms first
        $("#twilio-form, #nexmo-form").hide();

        // Show the selected form based on the value
        if (selectedSmsDriver === "twilio") {
            $("#twilio-form").show();
        } else if (selectedSmsDriver === "nexmo") {
            $("#nexmo-form").show();
        }
    });
});

Alpine.start();
