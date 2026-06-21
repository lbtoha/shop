"use strict";

import $ from "jquery";
import Sortable from "sortablejs";
import { toastError, toastSuccess } from "@/shared/js/toast";

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

function postJson(url, data) {
    return $.ajax({
        url,
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf },
        data,
    });
}

$(function () {
    const container = document.getElementById("home-sections-sortable");
    if (!container) {
        return;
    }

    const tbody = container.querySelector("table tbody");
    const reorderUrl = container.dataset.reorderUrl;

    // ── Drag & drop ordering ───────────────────────────────────────────
    if (tbody && reorderUrl) {
        Sortable.create(tbody, {
            handle: ".home-section-drag-handle",
            animation: 150,
            ghostClass: "opacity-40",
            onEnd: function () {
                const ids = Array.from(tbody.querySelectorAll(".home-section-drag-handle")).map(
                    (el) => el.dataset.id
                );

                postJson(reorderUrl, { ids })
                    .done((res) => toastSuccess(res?.message))
                    .fail((res) => toastError(res?.responseJSON?.message || "Could not save order"));
            },
        });
    }

    // ── Instant visibility toggle ──────────────────────────────────────
    $(document).on("change", ".home-section-toggle", function () {
        const url = this.dataset.url;

        postJson(url, {})
            .done((res) => toastSuccess(res?.message))
            .fail((res) => {
                this.checked = !this.checked; // revert on failure
                toastError(res?.responseJSON?.message || "Could not update visibility");
            });
    });
});
