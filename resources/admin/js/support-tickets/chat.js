"use strict";
import $ from "jquery";
// Store selected files in an array
let selectedFiles = [];
$("#fileInput").on("change", function (event) {
    const files = Array.from(event.target.files);
    // Add files to the selectedFiles array
    selectedFiles.push(...files);
    // Refresh the file list
    updateFileList();
});

function updateFileList() {
    const fileListContainer = $("#fileList");
    fileListContainer.html(""); // Clear the list using jQuery

    selectedFiles.forEach((file, index) => {
        const fileType = file.type.startsWith("image/") ? "image" : "file";
        const fileElement = `
            <div class="flex items-center justify-between gap-4 p-2 border rounded bg-gray-50">
                <div class="flex items-center gap-4">
                    ${fileType === "image" ? `
                        <img src="${URL.createObjectURL(file)}" alt="Preview" class="size-12 rounded object-cover">
                    ` : `
                        <div class="size-12 rounded-md bg-primary/10 f-center text-2xl text-primary">
                            <i class="ph ph-file text-4xl text-blue-500"></i>
                        </div>
                    `}
                    <div class="flex flex-col">
                        <p class="font-medium mb-1 text-sm">${file.name}</p>
                        <span class="text-gray-500 text-xs">${(file.size / 1024).toFixed(2)} KB</span>
                    </div>
                </div>
                <button type="button" data-index="${index}" class="removeFile bg-error/10 hover:bg-error/15 rounded-full text-error size-6 f-center">
                   <i class="ph ph-x text-base"></i>
                </button>
            </div>
        `;
        fileListContainer.append(fileElement);
    });
}

// Use event delegation for dynamically added elements
$("#fileList").on("click", ".removeFile", function () {
    const index = $(this).data("index");

    // Remove file from selectedFiles array
    selectedFiles.splice(index, 1);

    // Refresh the file list
    updateFileList();
});

const openSupportBtn = document.getElementById("open-support");
const closeSupportBtn = document.getElementById("close-support");
const chatBar = document.querySelector(".support-left");
openSupportBtn.addEventListener("click", () => {
    chatBar.classList.add("open");
});
closeSupportBtn.addEventListener("click", () => {
    chatBar.classList.remove("open");
});
