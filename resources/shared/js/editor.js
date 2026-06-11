import "katex/dist/katex.min.css";
import Quill from "quill";
import QuillTableBetter from "quill-table-better";
import "quill-table-better/dist/quill-table-better.css";
import "quill/dist/quill.snow.css";
import { getUrlType } from "./helper";
import "./quill-globals";

// Custom Image Blot with alignment and size support
const BaseImage = Quill.import("formats/image");

class ImageBlot extends BaseImage {
    static create(value) {
        const src = typeof value === "string" ? value : value?.src || value;
        const node = super.create(src);
        if (typeof value === "object" && value !== null) {
            if (value.width) node.setAttribute("width", value.width);
            if (value.height) node.setAttribute("height", value.height);
            if (value.style) node.setAttribute("style", value.style);
        }
        return node;
    }

    static value(node) {
        return {
            src: node.getAttribute("src"),
            width: node.getAttribute("width"),
            height: node.getAttribute("height"),
            style: node.getAttribute("style"),
        };
    }

    static formats(node) {
        const formats = {};
        if (node.hasAttribute("width")) formats.width = node.getAttribute("width");
        if (node.hasAttribute("height")) formats.height = node.getAttribute("height");
        if (node.hasAttribute("style")) formats.style = node.getAttribute("style");
        return formats;
    }

    format(name, value) {
        if (["width", "height", "style"].includes(name)) {
            if (value) {
                this.domNode.setAttribute(name, value);
            } else {
                this.domNode.removeAttribute(name);
            }
        } else {
            super.format(name, value);
        }
    }
}

ImageBlot.blotName = "image";
ImageBlot.tagName = "img";

// Register modules and custom blots (true = suppress overwrite warning for image format)
Quill.register(
    {
        "formats/image": ImageBlot,
        "modules/table-better": QuillTableBetter,
    },
    true,
);

// Create resize overlay with drag handles at all 4 corners
const createImageResizeOverlay = () => {
    if (document.getElementById("quill-image-resize-overlay")) {
        return document.getElementById("quill-image-resize-overlay");
    }

    const overlay = document.createElement("div");
    overlay.id = "quill-image-resize-overlay";
    overlay.innerHTML = `
        <div class="resize-handle resize-handle-nw" data-direction="nw"></div>
        <div class="resize-handle resize-handle-ne" data-direction="ne"></div>
        <div class="resize-handle resize-handle-sw" data-direction="sw"></div>
        <div class="resize-handle resize-handle-se" data-direction="se"></div>
        <div class="resize-size-display"></div>
    `;
    document.body.appendChild(overlay);
    return overlay;
};

// Show resize overlay on image
let currentResizeImage = null;
let currentQuillInstance = null;
let currentContextImage = null;

const showImageResizeOverlay = (img, quill) => {
    const overlay = createImageResizeOverlay();
    currentResizeImage = img;
    currentQuillInstance = quill;

    const updateOverlayPosition = () => {
        // Force reflow to get accurate dimensions after resize
        void img.offsetHeight;

        const rect = img.getBoundingClientRect();
        // Fixed positioning is relative to viewport, no need for scroll offset
        overlay.style.top = `${rect.top}px`;
        overlay.style.left = `${rect.left}px`;
        overlay.style.width = `${rect.width}px`;
        overlay.style.height = `${rect.height}px`;

        // Update size display
        const sizeDisplay = overlay.querySelector(".resize-size-display");
        if (sizeDisplay) sizeDisplay.textContent = `${Math.round(rect.width)} × ${Math.round(rect.height)}`;
    };

    updateOverlayPosition();
    overlay.classList.add("visible");

    // Setup resize handlers
    const handles = overlay.querySelectorAll(".resize-handle");
    handles.forEach((handle) => {
        handle.onmousedown = (e) => {
            e.preventDefault();
            e.stopPropagation();

            const direction = handle.dataset.direction;
            const startX = e.clientX;
            const startY = e.clientY;
            const startWidth = img.offsetWidth;
            const startHeight = img.offsetHeight;
            const aspectRatio = startWidth / startHeight;

            const onMouseMove = (moveEvent) => {
                let deltaX = moveEvent.clientX - startX;
                let deltaY = moveEvent.clientY - startY;

                let newWidth = startWidth;
                let newHeight = startHeight;

                // Calculate new dimensions based on direction
                if (direction.includes("e")) {
                    newWidth = startWidth + deltaX;
                } else if (direction.includes("w")) {
                    newWidth = startWidth - deltaX;
                }

                if (direction.includes("s")) {
                    newHeight = startHeight + deltaY;
                } else if (direction.includes("n")) {
                    newHeight = startHeight - deltaY;
                }

                // Maintain aspect ratio with shift key or by default
                if (moveEvent.shiftKey || true) {
                    if (Math.abs(deltaX) > Math.abs(deltaY)) {
                        newHeight = newWidth / aspectRatio;
                    } else {
                        newWidth = newHeight * aspectRatio;
                    }
                }

                // Minimum size
                newWidth = Math.max(50, newWidth);
                newHeight = Math.max(50, newHeight);

                // Apply size to image
                img.style.width = `${newWidth}px`;
                img.style.height = `${newHeight}px`;
                img.setAttribute("width", Math.round(newWidth));
                img.setAttribute("height", Math.round(newHeight));

                // Sync with toolbar inputs
                const widthInput = document.getElementById("ctx-image-width");
                const heightInput = document.getElementById("ctx-image-height");
                if (widthInput) widthInput.value = Math.round(newWidth);
                if (heightInput) heightInput.value = Math.round(newHeight);

                // Update overlay size directly (more responsive than getBoundingClientRect)
                overlay.style.width = `${newWidth}px`;
                overlay.style.height = `${newHeight}px`;

                // Update size display
                const sizeDisplay = overlay.querySelector(".resize-size-display");
                if (sizeDisplay) sizeDisplay.textContent = `${Math.round(newWidth)} × ${Math.round(newHeight)}`;
            };

            const onMouseUp = () => {
                document.removeEventListener("mousemove", onMouseMove);
                document.removeEventListener("mouseup", onMouseUp);
                quill.update();
            };

            document.addEventListener("mousemove", onMouseMove);
            document.addEventListener("mouseup", onMouseUp);
        };
    });
};

const hideImageResizeOverlay = () => {
    const overlay = document.getElementById("quill-image-resize-overlay");
    if (overlay) {
        overlay.classList.remove("visible");
        // Reset position to prevent stale display
        overlay.style.top = "-9999px";
        overlay.style.left = "-9999px";
    }
    currentResizeImage = null;
};
// Create image context toolbar for editing existing images
const createImageContextToolbar = () => {
    if (document.getElementById("quill-image-context-toolbar")) {
        return document.getElementById("quill-image-context-toolbar");
    }

    const toolbar = document.createElement("div");
    toolbar.id = "quill-image-context-toolbar";
    toolbar.innerHTML = `
        <div class="image-toolbar-group">
            <button type="button" data-align="left" title="Float Left">
                <svg width="16" height="16" viewBox="0 0 16 16"><path d="M2 3h12v2H2zM2 7h8v2H2zM2 11h12v2H2z"/></svg>
            </button>
            <button type="button" data-align="center" title="Center">
                <svg width="16" height="16" viewBox="0 0 16 16"><path d="M2 3h12v2H2zM4 7h8v2H4zM2 11h12v2H2z"/></svg>
            </button>
            <button type="button" data-align="right" title="Float Right">
                <svg width="16" height="16" viewBox="0 0 16 16"><path d="M2 3h12v2H2zM6 7h8v2H6zM2 11h12v2H2z"/></svg>
            </button>
        </div>
        <div class="image-toolbar-divider"></div>
        <div class="image-toolbar-group image-size-group">
            <input type="number" id="ctx-image-width" placeholder="W" title="Width (px)" />
            <span>×</span>
            <input type="number" id="ctx-image-height" placeholder="H" title="Height (px)" />
            <button type="button" id="ctx-apply-size" title="Apply Size">✓</button>
        </div>
        <div class="image-toolbar-divider"></div>
        <div class="image-toolbar-group">
            <button type="button" id="ctx-delete-image" title="Delete Image">
                <svg width="16" height="16" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 016 6v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm2.5 0a.5.5 0 01.5.5v6a.5.5 0 01-1 0V6a.5.5 0 01.5-.5zm3 .5a.5.5 0 00-1 0v6a.5.5 0 001 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 01-1 1H13v9a2 2 0 01-2 2H5a2 2 0 01-2-2V4h-.5a1 1 0 01-1-1V2a1 1 0 011-1H6a1 1 0 011-1h2a1 1 0 011 1h3.5a1 1 0 011 1v1zM4.118 4L4 4.059V13a1 1 0 001 1h6a1 1 0 001-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
            </button>
        </div>
    `;
    document.body.appendChild(toolbar);
    return toolbar;
};

// Show context toolbar for image

const showImageContextToolbar = (img, quill) => {
    const toolbar = createImageContextToolbar();
    currentContextImage = img;

    const alignBtns = toolbar.querySelectorAll("[data-align]");
    const widthInput = toolbar.querySelector("#ctx-image-width");
    const heightInput = toolbar.querySelector("#ctx-image-height");
    const applySizeBtn = toolbar.querySelector("#ctx-apply-size");
    const deleteBtn = toolbar.querySelector("#ctx-delete-image");

    // Set current values
    widthInput.value = img.width || img.offsetWidth || "";
    heightInput.value = img.height || img.offsetHeight || "";

    // Detect current alignment
    const style = img.getAttribute("style") || "";
    alignBtns.forEach((btn) => btn.classList.remove("active"));
    if (style.includes("float: left")) {
        toolbar.querySelector('[data-align="left"]').classList.add("active");
    } else if (style.includes("float: right")) {
        toolbar.querySelector('[data-align="right"]').classList.add("active");
    } else {
        toolbar.querySelector('[data-align="center"]').classList.add("active");
    }

    // Show toolbar first, then position it
    toolbar.classList.add("visible");

    // Position toolbar above image after it's visible
    requestAnimationFrame(() => {
        const rect = img.getBoundingClientRect();
        const toolbarHeight = toolbar.offsetHeight || 40;
        // Fixed positioning is relative to viewport, no need for scroll offset
        toolbar.style.top = `${rect.top - toolbarHeight - 8}px`;
        toolbar.style.left = `${Math.max(10, rect.left + rect.width / 2 - toolbar.offsetWidth / 2)}px`;
    });

    // Alignment handlers
    alignBtns.forEach((btn) => {
        btn.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation();
            alignBtns.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            applyImageAlignment(img, btn.dataset.align, quill);
        };
    });

    // Size handlers
    applySizeBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        const width = widthInput.value.trim();
        const height = heightInput.value.trim();
        applyImageSize(img, width, height, quill);
        // Update resize overlay after image has resized
        requestAnimationFrame(() => {
            showImageResizeOverlay(img, quill);
        });
    };

    // Delete handler
    deleteBtn.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        const blot = Quill.find(img);
        if (blot) {
            blot.remove();
            quill.update();
        }
        hideImageContextToolbar();
        hideImageResizeOverlay();
    };
};

// Hide context toolbar
const hideImageContextToolbar = () => {
    const toolbar = document.getElementById("quill-image-context-toolbar");
    if (toolbar) {
        toolbar.classList.remove("visible");
    }
};

// Apply alignment to image
const applyImageAlignment = (img, align, quill) => {
    let style = "";
    const width = img.getAttribute("width");
    const height = img.getAttribute("height");

    if (width) style += `width: ${width}px; `;
    if (height) style += `height: ${height}px; `;

    switch (align) {
        case "left":
            style += "float: left; margin-right: 1rem; margin-bottom: 0.5rem;";
            break;
        case "right":
            style += "float: right; margin-left: 1rem; margin-bottom: 0.5rem;";
            break;
        case "center":
        default:
            style += "display: block; margin-left: auto; margin-right: auto;";
            break;
    }

    img.setAttribute("style", style);
    quill.update();
};

// Apply size to image
const applyImageSize = (img, width, height, quill) => {
    let style = img.getAttribute("style") || "";

    // Remove existing width/height from style
    style = style.replace(/width:\s*\d+px;\s*/g, "");
    style = style.replace(/height:\s*\d+px;\s*/g, "");

    if (width) {
        img.setAttribute("width", width);
        style = `width: ${width}px; ` + style;
    } else {
        img.removeAttribute("width");
    }

    if (height) {
        img.setAttribute("height", height);
        style = `height: ${height}px; ` + style;
    } else {
        img.removeAttribute("height");
    }

    img.setAttribute("style", style.trim());
    quill.update();
};

// Setup image click handlers for editor
let imageHandlersInitialized = false;
let justShowedToolbar = false;

const setupImageClickHandlers = (quill) => {
    const editorRoot = quill.root;

    // Editor click handler - show toolbar/overlay when clicking images
    editorRoot.addEventListener("click", (e) => {
        if (e.target.tagName === "IMG") {
            e.preventDefault();
            e.stopPropagation();

            // Set flag to prevent immediate hide
            justShowedToolbar = true;
            setTimeout(() => {
                justShowedToolbar = false;
            }, 100);

            showImageContextToolbar(e.target, quill);
            showImageResizeOverlay(e.target, quill);
        } else {
            hideImageContextToolbar();
            hideImageResizeOverlay();
        }
    });

    // Only add document-level handlers once
    if (!imageHandlersInitialized) {
        imageHandlersInitialized = true;

        // Hide toolbar and overlay on outside click
        document.addEventListener("click", (e) => {
            // Skip if we just showed the toolbar
            if (justShowedToolbar) return;

            const toolbar = document.getElementById("quill-image-context-toolbar");
            const overlay = document.getElementById("quill-image-resize-overlay");
            const modal = document.getElementById("quill-image-modal");

            const isInToolbar = toolbar && toolbar.contains(e.target);
            const isInOverlay = overlay && overlay.contains(e.target);
            const isInModal = modal && modal.contains(e.target);
            const isImage = e.target.tagName === "IMG";
            const isInEditor = e.target.closest(".ql-editor");

            if (!isInToolbar && !isInOverlay && !isInModal && !isImage && !isInEditor) {
                hideImageContextToolbar();
                hideImageResizeOverlay();
            }
        });

        // Hide on scroll (debounced)
        let scrollTimeout;
        document.addEventListener(
            "scroll",
            () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    hideImageContextToolbar();
                    hideImageResizeOverlay();
                }, 100);
            },
            true,
        );
    }
};

// Create image modal for URL input and LFM browser
const createImageModal = () => {
    if (document.getElementById("quill-image-modal")) {
        return document.getElementById("quill-image-modal");
    }

    const modal = document.createElement("div");
    modal.id = "quill-image-modal";
    modal.innerHTML = `
        <div class="quill-modal-overlay">
            <div class="quill-modal-content">
                <div class="quill-modal-header">
                    <h3>Insert Image</h3>
                    <button type="button" class="quill-modal-close">&times;</button>
                </div>
                <div class="quill-modal-body">
                    <div class="quill-modal-tabs">
                        <button type="button" class="quill-tab-btn active" data-tab="browse">Browse Files</button>
                        <button type="button" class="quill-tab-btn" data-tab="url">Image URL</button>
                    </div>
                    <div class="quill-tab-content active" id="tab-browse">
                        <p class="quill-browse-text">Click the button below to open the File Manager</p>
                        <button type="button" class="quill-browse-btn" id="quill-open-lfm">
                            <svg width="20" height="20" viewBox="0 0 20 20"><path d="M2 4a2 2 0 012-2h4l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V4z"/></svg>
                            Open File Manager
                        </button>
                    </div>
                    <div class="quill-tab-content" id="tab-url">
                        <label for="quill-image-url">Image URL</label>
                        <input type="text" id="quill-image-url" placeholder="https://example.com/image.jpg" />
                        <div class="quill-size-inputs">
                            <div>
                                <label for="quill-image-width">Width (px)</label>
                                <input type="number" id="quill-image-width" placeholder="Auto" />
                            </div>
                            <div>
                                <label for="quill-image-height">Height (px)</label>
                                <input type="number" id="quill-image-height" placeholder="Auto" />
                            </div>
                        </div>
                        <div class="quill-alignment">
                            <label>Alignment</label>
                            <div class="quill-align-buttons">
                                <button type="button" data-align="left" title="Align Left">
                                    <svg width="16" height="16" viewBox="0 0 16 16"><path d="M2 3h12v2H2zM2 7h8v2H2zM2 11h12v2H2z"/></svg>
                                </button>
                                <button type="button" data-align="center" class="active" title="Center">
                                    <svg width="16" height="16" viewBox="0 0 16 16"><path d="M2 3h12v2H2zM4 7h8v2H4zM2 11h12v2H2z"/></svg>
                                </button>
                                <button type="button" data-align="right" title="Align Right">
                                    <svg width="16" height="16" viewBox="0 0 16 16"><path d="M2 3h12v2H2zM6 7h8v2H6zM2 11h12v2H2z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="quill-modal-footer">
                    <button type="button" class="quill-cancel-btn">Cancel</button>
                    <button type="button" class="quill-insert-btn">Insert Image</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    return modal;
};

// Show image modal
const showImageModal = (quill) => {
    const modal = createImageModal();
    const overlay = modal.querySelector(".quill-modal-overlay");
    const closeBtn = modal.querySelector(".quill-modal-close");
    const cancelBtn = modal.querySelector(".quill-cancel-btn");
    const insertBtn = modal.querySelector(".quill-insert-btn");
    const urlInput = modal.querySelector("#quill-image-url");
    const widthInput = modal.querySelector("#quill-image-width");
    const heightInput = modal.querySelector("#quill-image-height");
    const tabBtns = modal.querySelectorAll(".quill-tab-btn");
    const tabContents = modal.querySelectorAll(".quill-tab-content");
    const alignBtns = modal.querySelectorAll(".quill-align-buttons button");
    const openLfmBtn = modal.querySelector("#quill-open-lfm");

    let selectedAlignment = "center";

    // Reset form
    urlInput.value = "";
    widthInput.value = "";
    heightInput.value = "";

    // Show modal
    modal.style.display = "block";
    urlInput.focus();

    // Tab switching
    tabBtns.forEach((btn) => {
        btn.onclick = () => {
            tabBtns.forEach((b) => b.classList.remove("active"));
            tabContents.forEach((c) => c.classList.remove("active"));
            btn.classList.add("active");
            document.getElementById(`tab-${btn.dataset.tab}`).classList.add("active");
        };
    });

    // Alignment buttons
    alignBtns.forEach((btn) => {
        btn.onclick = () => {
            alignBtns.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            selectedAlignment = btn.dataset.align;
        };
    });

    // Open LFM
    openLfmBtn.onclick = () => {
        const prefix = getUrlType() + "/filemaneger";
        window.open(prefix + "?type=image&multiple=0", "FileManager", "width=900,height=600");
        window.SetUrl = (items) => {
            const range = quill.getSelection(true);
            items.forEach((item) => {
                const url = new URL(item.url).pathname;
                insertImageToQuill(quill, url, "", "", selectedAlignment, range);
            });
            closeModal();
        };
    };

    // Close modal handlers
    const closeModal = () => {
        modal.style.display = "none";
    };

    closeBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;
    overlay.onclick = (e) => {
        if (e.target === overlay) closeModal();
    };

    // Insert image
    insertBtn.onclick = () => {
        const url = urlInput.value.trim();
        if (!url) {
            urlInput.focus();
            return;
        }
        const width = widthInput.value.trim();
        const height = heightInput.value.trim();
        const range = quill.getSelection(true);
        insertImageToQuill(quill, url, width, height, selectedAlignment, range);
        closeModal();
    };

    // Enter key to submit
    urlInput.onkeydown = (e) => {
        if (e.key === "Enter") {
            insertBtn.click();
        }
    };
};

// Insert image to Quill with styles
const insertImageToQuill = (quill, url, width, height, align, range) => {
    quill.insertEmbed(range.index, "image", url);
    quill.setSelection(range.index + 1);

    // Apply styles after insertion
    setTimeout(() => {
        const images = quill.root.querySelectorAll("img");
        const lastImage = images[images.length - 1];
        if (lastImage && lastImage.src.includes(url.replace(/^\//, ""))) {
            let style = "";

            // Set dimensions
            if (width) {
                lastImage.setAttribute("width", width);
                style += `width: ${width}px; `;
            }
            if (height) {
                lastImage.setAttribute("height", height);
                style += `height: ${height}px; `;
            }

            // Set alignment
            switch (align) {
                case "left":
                    style += "float: left; margin-right: 1rem; margin-bottom: 0.5rem;";
                    break;
                case "right":
                    style += "float: right; margin-left: 1rem; margin-bottom: 0.5rem;";
                    break;
                case "center":
                default:
                    style += "display: block; margin-left: auto; margin-right: auto;";
                    break;
            }

            if (style) {
                lastImage.setAttribute("style", style);
            }

            // Trigger change event
            quill.update();
        }
    }, 50);
};

// Initialize Quill editor
export const editorInit = () => {
    const editorElements = document.querySelectorAll(`.text-editor`);
    if (editorElements.length === 0) {
        return;
    }

    editorElements.forEach((element) => {
        if (element.dataset.initialized === "true") return;
        const inputElement = element.querySelector("input");
        const textEditorElement = element.querySelector(".text-editor-content");

        const toolbarOptions = [
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            [{ font: [] }],
            ["bold", "italic", "underline", "strike"],
            [{ color: [] }, { background: [] }],
            [{ script: "sub" }, { script: "super" }],
            ["blockquote", "code-block"],
            [{ list: "ordered" }, { list: "bullet" }],
            [{ indent: "-1" }, { indent: "+1" }, { align: [] }],
            ["link", "image", "video", "formula", "table-better"],
            ["clean"],
        ];

        const quill = new Quill(textEditorElement, {
            theme: "snow",
            placeholder: "Type your content here...",
            modules: {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        image: function () {
                            showImageModal(this.quill);
                        },
                    },
                },
                // Disabled: Using custom resize implementation with context toolbar
                // imageResize: {
                //     displaySize: true,
                // },
                table: false,
                "table-better": {
                    language: "en_US",
                    menus: ["column", "row", "merge", "table", "cell", "wrap", "copy", "delete"],
                    toolbarTable: true,
                },
                keyboard: {
                    bindings: QuillTableBetter.keyboardBindings,
                },
            },
        });

        // Set initial content from input if editor is empty (useful for Alpine/dynamic tabs)
        if (inputElement.value && (quill.root.innerHTML === "<p><br></p>" || quill.root.innerHTML === "")) {
            quill.root.innerHTML = inputElement.value;
        }

        quill.on("text-change", function () {
            inputElement.value = quill.root.innerHTML;
            inputElement.dispatchEvent(new Event("input", { bubbles: true }));
            inputElement.dispatchEvent(new Event("change", { bubbles: true }));
        });

        // Setup image context toolbar for resize/position
        setupImageClickHandlers(quill);

        // Expose the Quill instance on the wrapper element and content element so external scripts
        // (e.g. description template helper buttons) can access it easily.
        element.__quill = quill;
        textEditorElement.__quill = quill;

        element.dataset.initialized = "true";
    });
};

// Call the function to initialize the editor
editorInit();
