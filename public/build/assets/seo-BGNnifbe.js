import{c as o}from"./select2-Rwveio9E.js";import{$ as t}from"./jquery-CQoUbI_f.js";import{f as n}from"./file-manager-6jL7CUa0.js";import{i}from"./codemirror-initialize-q_6jzSl9.js";import"./_commonjsHelpers-D6-XlEtG.js";import"./helper-B0cegqFZ.js";import"./moment-CWGZoW8q.js";n("file-uploader","image");i("editorContainer");o("tags-select-2");t("#extra_field_container").on("click",function(){const e=t("#row_container"),a=Date.now(),r=e.children().length;e.append(`<div class="space-y-4 border border-neutral-30 dark:border-neutral-500 p-3 rounded-lg  relative" id="meta-row-${a}">
            <div class="input-group">
                <label for="name" class="block mb-2 text-sm">Name</label>
                <input type="text" class="text-input" id="name-${a}" value="" name="meta[${r}][name]"
                        placeholder="Enter Text" />
                <span class="input-text-error"></span>
            </div>
            <div class="input-group">
                <label for="textarea" class="block mb-2 text-sm">Content</label>
                <textarea name="meta[${r}][content]" rows="4" class="text-input"
                                        placeholder="Address" id="address"></textarea>
                <span class="input-text-error"></span>
            </div>
            <button type="button" class="text-red-500 cursor-pointer mt-2 delete-meta absolute right-2 -top-1 hover:text-red-900" data-id="${a}">
                <i class="ph ph-trash"></i>
            </button>
        </div>`)});t(document).on("click",".delete-meta",function(){const e=t(this).data("id");t(`#meta-row-${e}`).remove()});
