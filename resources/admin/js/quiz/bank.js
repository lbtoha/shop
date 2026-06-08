import $ from "jquery";
$(document).on("change", "#category_id, #question_level_id", function () {
    const categoryId = $("#category_id").val();

    const url = new URL(window.location.href);

    if (categoryId) url.searchParams.set("category_id", categoryId);

    location.href = url.toString();
});
