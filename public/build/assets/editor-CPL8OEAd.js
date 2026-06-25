import{Q as x,a as L}from"./quill-globals-lbhF1OkO.js";import{a as k}from"./helper-B0cegqFZ.js";import"./_commonjsHelpers-D6-XlEtG.js";import"./moment-CWGZoW8q.js";const T=x.import("formats/image");class M extends T{static create(e){const i=typeof e=="string"?e:(e==null?void 0:e.src)||e,l=super.create(i);return typeof e=="object"&&e!==null&&(e.width&&l.setAttribute("width",e.width),e.height&&l.setAttribute("height",e.height),e.style&&l.setAttribute("style",e.style)),l}static value(e){return{src:e.getAttribute("src"),width:e.getAttribute("width"),height:e.getAttribute("height"),style:e.getAttribute("style")}}static formats(e){const i={};return e.hasAttribute("width")&&(i.width=e.getAttribute("width")),e.hasAttribute("height")&&(i.height=e.getAttribute("height")),e.hasAttribute("style")&&(i.style=e.getAttribute("style")),i}format(e,i){["width","height","style"].includes(e)?i?this.domNode.setAttribute(e,i):this.domNode.removeAttribute(e):super.format(e,i)}}M.blotName="image";M.tagName="img";x.register({"formats/image":M,"modules/table-better":L},!0);const $=()=>{if(document.getElementById("quill-image-resize-overlay"))return document.getElementById("quill-image-resize-overlay");const t=document.createElement("div");return t.id="quill-image-resize-overlay",t.innerHTML=`
        <div class="resize-handle resize-handle-nw" data-direction="nw"></div>
        <div class="resize-handle resize-handle-ne" data-direction="ne"></div>
        <div class="resize-handle resize-handle-sw" data-direction="sw"></div>
        <div class="resize-handle resize-handle-se" data-direction="se"></div>
        <div class="resize-size-display"></div>
    `,document.body.appendChild(t),t},S=(t,e)=>{const i=$();(()=>{t.offsetHeight;const a=t.getBoundingClientRect();i.style.top=`${a.top}px`,i.style.left=`${a.left}px`,i.style.width=`${a.width}px`,i.style.height=`${a.height}px`;const s=i.querySelector(".resize-size-display");s&&(s.textContent=`${Math.round(a.width)} × ${Math.round(a.height)}`)})(),i.classList.add("visible"),i.querySelectorAll(".resize-handle").forEach(a=>{a.onmousedown=s=>{s.preventDefault(),s.stopPropagation();const d=a.dataset.direction,u=s.clientX,n=s.clientY,c=t.offsetWidth,g=t.offsetHeight,f=c/g,y=r=>{let m=r.clientX-u,v=r.clientY-n,h=c,b=g;d.includes("e")?h=c+m:d.includes("w")&&(h=c-m),d.includes("s")?b=g+v:d.includes("n")&&(b=g-v),r.shiftKey,Math.abs(m)>Math.abs(v)?b=h/f:h=b*f,h=Math.max(50,h),b=Math.max(50,b),t.style.width=`${h}px`,t.style.height=`${b}px`,t.setAttribute("width",Math.round(h)),t.setAttribute("height",Math.round(b));const I=document.getElementById("ctx-image-width"),A=document.getElementById("ctx-image-height");I&&(I.value=Math.round(h)),A&&(A.value=Math.round(b)),i.style.width=`${h}px`,i.style.height=`${b}px`;const E=i.querySelector(".resize-size-display");E&&(E.textContent=`${Math.round(h)} × ${Math.round(b)}`)},p=()=>{document.removeEventListener("mousemove",y),document.removeEventListener("mouseup",p),e.update()};document.addEventListener("mousemove",y),document.addEventListener("mouseup",p)}})},w=()=>{const t=document.getElementById("quill-image-resize-overlay");t&&(t.classList.remove("visible"),t.style.top="-9999px",t.style.left="-9999px")},C=()=>{if(document.getElementById("quill-image-context-toolbar"))return document.getElementById("quill-image-context-toolbar");const t=document.createElement("div");return t.id="quill-image-context-toolbar",t.innerHTML=`
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
    `,document.body.appendChild(t),t},R=(t,e)=>{const i=C(),l=i.querySelectorAll("[data-align]"),o=i.querySelector("#ctx-image-width"),a=i.querySelector("#ctx-image-height"),s=i.querySelector("#ctx-apply-size"),d=i.querySelector("#ctx-delete-image");o.value=t.width||t.offsetWidth||"",a.value=t.height||t.offsetHeight||"";const u=t.getAttribute("style")||"";l.forEach(n=>n.classList.remove("active")),u.includes("float: left")?i.querySelector('[data-align="left"]').classList.add("active"):u.includes("float: right")?i.querySelector('[data-align="right"]').classList.add("active"):i.querySelector('[data-align="center"]').classList.add("active"),i.classList.add("visible"),requestAnimationFrame(()=>{const n=t.getBoundingClientRect(),c=i.offsetHeight||40;i.style.top=`${n.top-c-8}px`,i.style.left=`${Math.max(10,n.left+n.width/2-i.offsetWidth/2)}px`}),l.forEach(n=>{n.onclick=c=>{c.preventDefault(),c.stopPropagation(),l.forEach(g=>g.classList.remove("active")),n.classList.add("active"),V(t,n.dataset.align,e)}}),s.onclick=n=>{n.preventDefault(),n.stopPropagation();const c=o.value.trim(),g=a.value.trim();D(t,c,g,e),requestAnimationFrame(()=>{S(t,e)})},d.onclick=n=>{n.preventDefault(),n.stopPropagation();const c=x.find(t);c&&(c.remove(),e.update()),q(),w()}},q=()=>{const t=document.getElementById("quill-image-context-toolbar");t&&t.classList.remove("visible")},V=(t,e,i)=>{let l="";const o=t.getAttribute("width"),a=t.getAttribute("height");switch(o&&(l+=`width: ${o}px; `),a&&(l+=`height: ${a}px; `),e){case"left":l+="float: left; margin-right: 1rem; margin-bottom: 0.5rem;";break;case"right":l+="float: right; margin-left: 1rem; margin-bottom: 0.5rem;";break;case"center":default:l+="display: block; margin-left: auto; margin-right: auto;";break}t.setAttribute("style",l),i.update()},D=(t,e,i,l)=>{let o=t.getAttribute("style")||"";o=o.replace(/width:\s*\d+px;\s*/g,""),o=o.replace(/height:\s*\d+px;\s*/g,""),e?(t.setAttribute("width",e),o=`width: ${e}px; `+o):t.removeAttribute("width"),i?(t.setAttribute("height",i),o=`height: ${i}px; `+o):t.removeAttribute("height"),t.setAttribute("style",o.trim()),l.update()};let H=!1,z=!1;const F=t=>{if(t.root.addEventListener("click",i=>{i.target.tagName==="IMG"?(i.preventDefault(),i.stopPropagation(),z=!0,setTimeout(()=>{z=!1},100),R(i.target,t),S(i.target,t)):(q(),w())}),!H){H=!0,document.addEventListener("click",l=>{if(z)return;const o=document.getElementById("quill-image-context-toolbar"),a=document.getElementById("quill-image-resize-overlay"),s=document.getElementById("quill-image-modal"),d=o&&o.contains(l.target),u=a&&a.contains(l.target),n=s&&s.contains(l.target),c=l.target.tagName==="IMG",g=l.target.closest(".ql-editor");!d&&!u&&!n&&!c&&!g&&(q(),w())});let i;document.addEventListener("scroll",()=>{clearTimeout(i),i=setTimeout(()=>{q(),w()},100)},!0)}},O=()=>{if(document.getElementById("quill-image-modal"))return document.getElementById("quill-image-modal");const t=document.createElement("div");return t.id="quill-image-modal",t.innerHTML=`
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
    `,document.body.appendChild(t),t},W=t=>{const e=O(),i=e.querySelector(".quill-modal-overlay"),l=e.querySelector(".quill-modal-close"),o=e.querySelector(".quill-cancel-btn"),a=e.querySelector(".quill-insert-btn"),s=e.querySelector("#quill-image-url"),d=e.querySelector("#quill-image-width"),u=e.querySelector("#quill-image-height"),n=e.querySelectorAll(".quill-tab-btn"),c=e.querySelectorAll(".quill-tab-content"),g=e.querySelectorAll(".quill-align-buttons button"),f=e.querySelector("#quill-open-lfm");let y="center";s.value="",d.value="",u.value="",e.style.display="block",s.focus(),n.forEach(r=>{r.onclick=()=>{n.forEach(m=>m.classList.remove("active")),c.forEach(m=>m.classList.remove("active")),r.classList.add("active"),document.getElementById(`tab-${r.dataset.tab}`).classList.add("active")}}),g.forEach(r=>{r.onclick=()=>{g.forEach(m=>m.classList.remove("active")),r.classList.add("active"),y=r.dataset.align}}),f.onclick=()=>{const r=k()+"/filemaneger";window.open(r+"?type=image&multiple=0","FileManager","width=900,height=600"),window.SetUrl=m=>{const v=t.getSelection(!0);m.forEach(h=>{const b=new URL(h.url).pathname;B(t,b,"","",y,v)}),p()}};const p=()=>{e.style.display="none"};l.onclick=p,o.onclick=p,i.onclick=r=>{r.target===i&&p()},a.onclick=()=>{const r=s.value.trim();if(!r){s.focus();return}const m=d.value.trim(),v=u.value.trim(),h=t.getSelection(!0);B(t,r,m,v,y,h),p()},s.onkeydown=r=>{r.key==="Enter"&&a.click()}},B=(t,e,i,l,o,a)=>{t.insertEmbed(a.index,"image",e),t.setSelection(a.index+1),setTimeout(()=>{const s=t.root.querySelectorAll("img"),d=s[s.length-1];if(d&&d.src.includes(e.replace(/^\//,""))){let u="";switch(i&&(d.setAttribute("width",i),u+=`width: ${i}px; `),l&&(d.setAttribute("height",l),u+=`height: ${l}px; `),o){case"left":u+="float: left; margin-right: 1rem; margin-bottom: 0.5rem;";break;case"right":u+="float: right; margin-left: 1rem; margin-bottom: 0.5rem;";break;case"center":default:u+="display: block; margin-left: auto; margin-right: auto;";break}u&&d.setAttribute("style",u),t.update()}},50)},P=()=>{const t=document.querySelectorAll(".text-editor");t.length!==0&&t.forEach(e=>{if(e.dataset.initialized==="true")return;const i=e.querySelector("input"),l=e.querySelector(".text-editor-content"),o=[[{header:[1,2,3,4,5,6,!1]}],[{font:[]}],["bold","italic","underline","strike"],[{color:[]},{background:[]}],[{script:"sub"},{script:"super"}],["blockquote","code-block"],[{list:"ordered"},{list:"bullet"}],[{indent:"-1"},{indent:"+1"},{align:[]}],["link","image","video","formula","table-better"],["clean"]],a=new x(l,{theme:"snow",placeholder:"Type your content here...",modules:{toolbar:{container:o,handlers:{image:function(){W(this.quill)}}},table:!1,"table-better":{language:"en_US",menus:["column","row","merge","table","cell","wrap","copy","delete"],toolbarTable:!0},keyboard:{bindings:L.keyboardBindings}}});i.value&&(a.root.innerHTML==="<p><br></p>"||a.root.innerHTML==="")&&(a.root.innerHTML=i.value),a.on("text-change",function(){i.value=a.root.innerHTML,i.dispatchEvent(new Event("input",{bubbles:!0})),i.dispatchEvent(new Event("change",{bubbles:!0}))}),F(a),e.__quill=a,l.__quill=a,e.dataset.initialized="true"})};P();
