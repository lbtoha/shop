import katex from "katex";
import Quill from "quill";
window.Quill = Quill;
window.katex = katex;

// Shim for Quill 2.0 compatibility with older modules
if (Quill && !Quill.imports) {
    Quill.imports = {
        parchment: Quill.import("parchment"),
        delta: Quill.import("delta"),
    };
}
