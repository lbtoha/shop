import "../../shared/js/primary-dashboard/app.js";
import "@popperjs/core";
import "./auth.js";
import "./logout.js";
import "./page-header.js";
import "../../shared/js/form-submit.js";
import "../../shared/js/table.js";
import "../../shared/js/toast.js";
import { fileManagerInitByClass } from "../../shared/js/primary-dashboard/file-manager";

$(() => {
    fileManagerInitByClass("file-uploader", "image");
});
