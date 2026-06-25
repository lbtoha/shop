import{$ as s}from"./jquery-CQoUbI_f.js";import"./_commonjsHelpers-D6-XlEtG.js";let i=[];s("#fileInput").on("change",function(e){const t=Array.from(e.target.files);i.push(...t),n()});function n(){const e=s("#fileList");e.html(""),i.forEach((t,r)=>{const c=`
            <div class="flex items-center justify-between gap-4 p-2 border rounded bg-gray-50">
                <div class="flex items-center gap-4">
                    ${(t.type.startsWith("image/")?"image":"file")==="image"?`
                        <img src="${URL.createObjectURL(t)}" alt="Preview" class="size-12 rounded object-cover">
                    `:`
                        <div class="size-12 rounded-md bg-primary/10 f-center text-2xl text-primary">
                            <i class="ph ph-file text-4xl text-blue-500"></i>
                        </div>
                    `}
                    <div class="flex flex-col">
                        <p class="font-medium mb-1 text-sm">${t.name}</p>
                        <span class="text-gray-500 text-xs">${(t.size/1024).toFixed(2)} KB</span>
                    </div>
                </div>
                <button type="button" data-index="${r}" class="removeFile bg-error/10 hover:bg-error/15 rounded-full text-error size-6 f-center">
                   <i class="ph ph-x text-base"></i>
                </button>
            </div>
        `;e.append(c)})}s("#fileList").on("click",".removeFile",function(){const e=s(this).data("index");i.splice(e,1),n()});const l=document.getElementById("open-support"),a=document.getElementById("close-support"),o=document.querySelector(".support-left");l.addEventListener("click",()=>{o.classList.add("open")});a.addEventListener("click",()=>{o.classList.remove("open")});
