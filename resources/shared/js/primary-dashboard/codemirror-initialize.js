import $ from "jquery";
import { css } from "@codemirror/lang-css";
import { oneDark } from "@codemirror/theme-one-dark";
import { EditorView, basicSetup } from "codemirror";

export const initializeCodeMirror = (className) => {
    const editorContainer = $(`.${className}`);

    const input = editorContainer.data("input");

    const initialValue = $(`#${input}`).val();

    const preview = editorContainer.data("preview");

    if (preview) {
        $(`#${preview}`).html(initialValue);
    }
    const editor = new EditorView({
        doc: initialValue,
        extensions: [
            basicSetup,
            EditorView.theme({
                "&": {
                    height: "600px",
                    fontSize: "14px",
                },
            }),
            css(),
            oneDark,
            EditorView.updateListener.of((update) => {
                if (update.docChanged) {
                    const newCode = update.state.doc.toString();
                    $(`#${input}`).val(newCode);
                    if (preview) {
                        $(`#${preview}`).html(newCode);
                    }
                }
            }),
        ],
        parent: document.querySelector(`.${className}`),
    });

    return editor;
};
