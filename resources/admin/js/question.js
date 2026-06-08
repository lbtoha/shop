import { editorInit } from "@/shared/js/editor";
import { fileManagerInitByClass } from "@/shared/js/primary-dashboard/file-manager";
import { toastError } from "@/shared/js/toast";
import $ from "jquery";
import select2 from "select2";
import "select2/dist/css/select2.min.css";
select2();

// Initialize components
fileManagerInitByClass("file-uploader", "image");

// Initialize all forms on the page
$(".question-form-container").each(function () {
    const $form = $(this);
    const locale = $form.data("locale");

    // Scoped selectors
    const getById = (id) => $form.find(`#${id}_${locale}`);
    const getByClass = (cls) => $form.find(`.${cls}`);

    // Initialize Select2 for this form
    $form.find(".select-2").each(function () {
        let optionCount = $(this).find("option").length || 0;
        $(this).select2({
            selectionCssClass: "custom-select",
            width: "100%",
            allowClear: true,
            placeholder: $(this).data("placeholder") || "Select an option",
            minimumResultsForSearch: optionCount > 5 ? 0 : Infinity,
        });
    });

    // Initialize all editors in this form
    const editorNames = ["question_text", "hints", "answer_explanation"];
    editorNames.forEach((name) => {
        const id = `${name}_${locale}`;
        if ($form.find(`#${id}`).length) {
            editorInit(id);
        }
    });

    // Gap Filler Logic
    getById("add_gap_filler").on("click", function () {
        const $questionText = getById("question_text");
        const questionText = $questionText.val();
        const matches = questionText.match(/\{([A-Z])\}/g) || [];
        const usedLetters = matches.map((m) => m.replace(/\{|\}/g, ""));
        let nextLetter = null;

        for (let i = 0; i < 26; i++) {
            const letter = String.fromCharCode(65 + i); // A-Z
            if (!usedLetters.includes(letter)) {
                nextLetter = letter;
                break;
            }
        }

        if (nextLetter) {
            const newBlank = `{${nextLetter}}`;
            const $input = $form.find("input[name='question_text'], textarea[name='question_text']");
            $input.val(questionText + " " + newBlank);
        } else {
            alert("Maximum 26 gap fillers allowed (A-Z).");
        }
    });

    // Question Input Type Logic
    function setQuestionText(value) {
        const container = getById("question-text-container");
        container.children().addClass("hidden");
        container.find("input, textarea").removeAttr("name");

        const selected = getById(`question_${value}_id`);
        selected.removeClass("hidden");

        if (value === "audio" || value === "video") {
            const fileWrapper = selected.find(".media-file-input");
            const urlWrapper = selected.find(".media-url-input");

            if (urlWrapper.hasClass("hidden")) {
                fileWrapper.find("input").attr("name", "question_text");
            } else {
                urlWrapper.find("input").attr("name", "question_text");
            }
        } else {
            selected.find("input, textarea").attr("name", "question_text");
        }
    }

    const $inputSelector = getById("question_input_type");
    if ($inputSelector.val()) {
        setQuestionText($inputSelector.val());
    }

    $inputSelector.on("change", function () {
        setQuestionText($(this).val());
    });

    // Media source toggle inside this form
    $form.on("click", ".media-source-btn", function () {
        const source = $(this).data("source");
        const container = $(this).closest(".input-group");
        const fileWrapper = container.find(".media-file-input");
        const urlWrapper = container.find(".media-url-input");

        container.find(".media-source-btn").removeClass("btn-primary active").addClass("btn-outline");
        $(this).removeClass("btn-outline").addClass("btn-primary active");

        fileWrapper.find("input").removeAttr("name");
        urlWrapper.find("input").removeAttr("name");

        if (source === "file") {
            fileWrapper.removeClass("hidden");
            urlWrapper.addClass("hidden");
            fileWrapper.find("input").attr("name", "question_text");
        } else {
            fileWrapper.addClass("hidden");
            urlWrapper.removeClass("hidden");
            urlWrapper.find("input").attr("name", "question_text");
        }
    });

    // Explanation Type Logic
    function setExplanationType(value) {
        const container = getById("question-explanation");
        container.children().addClass("hidden");
        container.find("input, textarea").removeAttr("name");

        const selected = $form.find(`#${value}_${locale}`);
        if (selected.length) {
            selected.removeClass("hidden");
            selected.find("input, textarea").attr("name", "question_explanation");
        }
    }

    const $explSelector = getById("explanation_type");
    if ($explSelector.val()) {
        setExplanationType($explSelector.val());
    }

    $explSelector.on("change", function () {
        setExplanationType($(this).val());
    });

    // Question Type Logic (Options & Blanks visibility)
    getById("question_type").on("change", function () {
        const optionsContainer = getById("options_container");
        const correctAnswersContainer = getById("correct_answers_container");
        const questionType = $(this).val();

        optionsContainer.empty();
        correctAnswersContainer.empty();
        getById("gap_fillers").addClass("hidden");
        getById("question_text_example").addClass("hidden");

        if (questionType === "fill_in_the_blank") {
            getById("gap_fillers").removeClass("hidden");
            getById("question_text_example").removeClass("hidden");
        } else if (questionType === "true_false") {
            const tfHtml = `
                <div class="space-y-4 border border-neutral-30 dark:border-neutral-500 p-3 rounded-lg relative meta-row" id="meta-row-1_${locale}">
                    <div class="input-group">
                        <div class="flex justify-between items-center py-2">
                            <label class="block mb-2 text-sm">Option - A</label>
                            <button type="button" class="btn btn-success correct_answer" data-id="1_${locale}"><i class="ph ph-check"></i></button>
                        </div>
                        <input type="text" class="text-input" readonly id="options-label-1_${locale}" value="True" name="options[0][label]" />
                        <input type="hidden" name="options[0][value]" id="options-value-1_${locale}" value="True">
                    </div>
                </div>
                <div class="space-y-4 border border-neutral-30 dark:border-neutral-500 p-3 rounded-lg relative meta-row" id="meta-row-2_${locale}">
                    <div class="input-group">
                        <div class="flex justify-between items-center py-2">
                            <label class="block mb-2 text-sm">Option - B</label>
                            <button type="button" class="btn btn-success correct_answer" data-id="2_${locale}"><i class="ph ph-check"></i></button>
                        </div>
                        <input type="text" class="text-input" readonly id="options-label-2_${locale}" value="False" name="options[1][label]" />
                        <input type="hidden" name="options[1][value]" id="options-value-2_${locale}" value="False">
                    </div>
                </div>`;
            optionsContainer.append(tfHtml);
        }
    });

    // Options Management
    getById("extra_field_container").on("click", function () {
        const row_container = getById("options_container");
        const questionType = getById("question_type").val();
        const rowCount = row_container.children().length;

        if (questionType === "true_false" && rowCount >= 2) {
            toastError("Only True and False options are allowed");
            return;
        }

        const uniqueId = Date.now();
        const character = String.fromCharCode(65 + rowCount); // A, B, C...

        const optionHtml = `
            <div class="space-y-4 border border-neutral-30 dark:border-neutral-500 p-3 rounded-lg relative meta-row" id="meta-row-${uniqueId}_${locale}">
                <div class="input-group">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-3 border-b border-gray-200 dark:border-gray-700">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2 sm:mb-0">Option - ${character}</label>
                        <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <span>Mark as correct answer</span>
                            <button type="button" class="correct_answer p-2 rounded-full hover:bg-green-100 dark:hover:bg-green-900 transition" data-id="${uniqueId}_${locale}">
                                <i class="ph ph-check text-lg text-green-600 dark:text-green-400"></i>
                            </button>
                        </div>
                    </div>
                    <input type="text" class="text-input" id="options-label-${uniqueId}_${locale}" name="options[${rowCount}][label]" placeholder="Enter option" />
                    <input type="hidden" name="options[${rowCount}][value]" id="options-value-${uniqueId}_${locale}" value="${character}">
                </div>
                <button type="button" class="text-red-500 mt-2 delete-meta absolute right-0 -top-7" data-id="${uniqueId}_${locale}"><i class="ph ph-trash"></i></button>
            </div>`;
        row_container.append(optionHtml);
    });

    // Scoped Delete Meta
    $form.on("click", ".delete-meta", function () {
        const id = $(this).data("id");
        $form.find(`#meta-row-${id}`).remove();
    });

    // Scoped Correct Answer Logic
    $form.on("click", ".correct_answer", function () {
        const id = $(this).data("id");
        const value = $form.find(`#options-value-${id}`).val();
        const label = $form.find(`#options-label-${id}`).val();

        if (!label) {
            toastError("Please enter option label first");
            return;
        }

        const $ansContainer = getById("correct_answers_container");
        if ($ansContainer.find(`input[value="${value}"]`).length) {
            toastError("Already added as correct answer");
            return;
        }

        const questionType = getById("question_type").val();
        const currentCorrectCount = $ansContainer.children().length;

        if ((questionType === "true_false" || questionType === "single_choice") && currentCorrectCount > 0) {
            toastError("Only one correct answer is allowed for this question type");
            return;
        }

        const uniqueId = Date.now();
        const ansHtml = `
            <div class="flex items-center gap-2" id="meta-row-ans-${uniqueId}_${locale}">
                <span class="text-sm">${value}</span>
                <input type="hidden" name="correct_answers[${currentCorrectCount}]" value="${value}">
                <button type="button" class="text-red-500 mt-2 delete-meta" data-id="ans-${uniqueId}_${locale}"><i class="ph ph-trash"></i></button>
            </div>`;
        $ansContainer.append(ansHtml);
    });
});
