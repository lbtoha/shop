"use strict";
import $ from "jquery";
const query = document.querySelector.bind(document);
const removeComma = (string) => string.trim();
function modifyTags(e, id) {
    if (e.key === "Enter") {
        addTag(e.target.value, id);
        e.target.value = "";
    }
    if (e.key === "Backspace" && !e.target.value.length) {
        deleteTag(null, id, query(`.${id}`).children.length - 1);
    }
    // the all tags as comma separated on input
    if (query(`.${id}`).children?.length) {
        query(`#${id}-input`).value = Array.from(query(`.${id}`).children)
            .map((tag) => tag.children[0].textContent)
            .join(", ");
    }
}
function addTag(textValue, id) {
    if(!textValue.trim()) return
    const cleanedValue = removeComma(textValue);
    // Check for duplicates
    const existingTags = Array.from(query(`.${id}`).children).map(
        (tag) => tag.children[0].textContent
    );
    if (existingTags.includes(cleanedValue)) {
        return; // Prevent duplicate entry
    }
    const tag = document.createElement("div"),
        tagName = document.createElement("level"),
        remove = document.createElement("i");
    tagName.setAttribute("class", "tag-name");
    tagName.textContent = removeComma(textValue);
    remove.setAttribute("class", "remove");
    remove.setAttribute("class", "ph ph-x cursor-pointer")
    remove.addEventListener("click", (e) => deleteTag(e, id));
    tag.setAttribute("class", "tag");
    tag.appendChild(tagName);
    tag.appendChild(remove);
    query(`.${id}`).appendChild(tag);
}
function deleteTag(e, id, i = Array.from(query(`.${id}`).children).indexOf(e.target.parentElement)) {
    const index = query(`.${id}`).getElementsByClassName("tag")[i];
    query(`.${id}`).removeChild(index);
}
export const initializeCommaSeparated = (id) => {
    const value = $(`#${id}-input`).val();
    if (value) {
        value.split(",").forEach((tag) => addTag(tag, id));
    }
    query(`#${id}`).addEventListener("click", () => query(`#${id}`).focus());
    query(`#${id}`).addEventListener("keydown", (e) => modifyTags(e, id));
};
