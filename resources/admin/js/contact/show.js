import $ from "jquery";
import { editorInit } from "../../custom/js/libs/editor";
import { select2Init } from "../../custom/js/libs/select2";

// Editor init
editorInit("email_body_editor");
const templateSelect = select2Init("template_id2");

templateSelect.on("change", function () {
    let selectedTemplate = $(this).val();

    if (selectedTemplate) {
        $("#email_body_editor").hide();
        $("#subject").hide();
    } else {
        $("#email_body_editor").show();
        $("#subject").show();
    }
});
