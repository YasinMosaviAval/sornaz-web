<div id="post-editor" class="section hidden">
    <style>
        #post-editor .pe-tool { display:inline-flex; min-width:2rem; height:2rem; align-items:center; justify-content:center; border-radius:.5rem; padding:0 .5rem; font-size:.875rem; color:#374151; }
        #post-editor .pe-tool:hover { background:#fff; color:#4f46e5; box-shadow:0 1px 2px rgb(0 0 0 / .08); }
        #peVisualContent:empty::before { content:attr(data-placeholder); color:#9ca3af; pointer-events:none; }
        #post-editor.pe-editor-fullscreen { position:fixed; inset:0; z-index:60; overflow:auto; background:#f3f4f6; padding:1rem; }
        #post-editor.pe-editor-fullscreen #peVisualContent,
        #post-editor.pe-editor-fullscreen #peContent { min-height:calc(100vh - 13rem); }
        #peVisualContent table { border-collapse:collapse; width:100%; }
        #peVisualContent th, #peVisualContent td { border:1px solid #d1d5db; padding:.5rem; }
        #peVisualContent blockquote { border-right:4px solid #6366f1; margin:1rem 0; padding:.75rem 1rem; color:#4b5563; background:#f9fafb; }
    </style>
    <!-- نوار بالا -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <button onclick="closePostEditor()" class="text-gray-500 hover:text-indigo-600 flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-right"></i> بازگشت به لیست
            </button>
            <div>
                <h1 class="text-2xl font-bold" id="postEditorPageTitle">ویرایش نوشته</h1>
                <p class="text-gray-400 text-sm mt-0.5" id="postEditorSubtitle">پیش‌نویس</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="previewPostEditor()" class="border border-gray-300 hover:bg-gray-50 px-5 py-2.5 rounded-2xl text-sm">
                <i class="fas fa-eye ml-1"></i> پیش‌نمایش
            </button>
            <button onclick="savePostEditor('draft')" class="border border-indigo-300 text-indigo-600 hover:bg-indigo-50 px-5 py-2.5 rounded-2xl text-sm">
                ذخیره پیش‌نویس
            </button>
            <button onclick="savePostEditor('publish')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-2xl text-sm font-medium">
                <i class="fas fa-check ml-1"></i> انتشار
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- ستون اصلی (۲/۳) -->
        <div class="xl:col-span-2 space-y-5">
            <!-- عنوان -->
            <div class="bg-white rounded-3xl shadow-sm p-6">
                <input type="text" id="peTitle"
                       placeholder="عنوان نوشته را اینجا بنویسید"
                       class="w-full text-2xl font-bold border-0 focus:outline-none focus:ring-0 placeholder-gray-300">
                <div class="mt-3 flex items-center gap-2 text-sm text-gray-400">
                    <span>پیوند یکتا:</span>
                    <span class="text-indigo-500" id="pePermalinkBase">/posts/</span>
                    <input type="text" id="peSlug" placeholder="slug"
                           class="flex-1 border border-gray-200 rounded-xl py-1.5 px-3 text-sm focus:outline-none focus:border-indigo-400">
                </div>
            </div>

            <!-- محتوا -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b bg-gray-50 px-4 pt-3">
                    <div class="flex gap-1">
                        <button type="button" id="peVisualTab" onclick="peSetEditorMode('visual')" class="rounded-t-xl border border-b-white bg-white px-5 py-2 text-sm font-medium text-indigo-600">دیداری</button>
                        <button type="button" id="peCodeTab" onclick="peSetEditorMode('code')" class="rounded-t-xl px-5 py-2 text-sm text-gray-500">کد HTML</button>
                    </div>
                    <span id="peWordCount" class="pb-2 text-xs text-gray-400">۰ واژه</span>
                </div>
                <div id="peVisualToolbar" class="flex flex-wrap items-center gap-1 border-b bg-gray-50 px-3 py-2">
                    <select onchange="peExec('formatBlock',this.value);this.selectedIndex=0" class="rounded-lg border px-2 py-1.5 text-sm"><option value="">قالب</option><option value="p">پاراگراف</option><option value="h1">عنوان ۱</option><option value="h2">عنوان ۲</option><option value="h3">عنوان ۳</option><option value="h4">عنوان ۴</option><option value="pre">کد</option><option value="blockquote">نقل‌قول</option></select>
                    <select onchange="peExec('fontName',this.value)" class="rounded-lg border px-2 py-1.5 text-sm"><option value="inherit">قلم</option><option value="Tahoma">Tahoma</option><option value="Arial">Arial</option><option value="Georgia">Georgia</option><option value="monospace">Monospace</option></select>
                    <select onchange="peExec('fontSize',this.value)" class="rounded-lg border px-2 py-1.5 text-sm"><option value="3">اندازه</option><option value="1">خیلی کوچک</option><option value="2">کوچک</option><option value="3">معمولی</option><option value="4">بزرگ</option><option value="5">خیلی بزرگ</option><option value="6">عنوان</option></select>
                    <span class="mx-1 h-7 w-px bg-gray-300"></span>
                    <button type="button" onclick="peExec('bold')" class="pe-tool" title="ضخیم"><b>B</b></button><button type="button" onclick="peExec('italic')" class="pe-tool italic" title="کج">I</button><button type="button" onclick="peExec('underline')" class="pe-tool underline" title="زیرخط">U</button><button type="button" onclick="peExec('strikeThrough')" class="pe-tool line-through" title="خط‌خورده">S</button>
                    <button type="button" onclick="peExec('subscript')" class="pe-tool" title="زیرنویس">X₂</button><button type="button" onclick="peExec('superscript')" class="pe-tool" title="بالانویس">X²</button>
                    <span class="mx-1 h-7 w-px bg-gray-300"></span>
                    <button type="button" onclick="peExec('justifyRight')" class="pe-tool" title="راست‌چین"><i class="fas fa-align-right"></i></button><button type="button" onclick="peExec('justifyCenter')" class="pe-tool" title="وسط‌چین"><i class="fas fa-align-center"></i></button><button type="button" onclick="peExec('justifyLeft')" class="pe-tool" title="چپ‌چین"><i class="fas fa-align-left"></i></button><button type="button" onclick="peExec('justifyFull')" class="pe-tool" title="تراز دو طرف"><i class="fas fa-align-justify"></i></button>
                    <button type="button" onclick="peExec('insertUnorderedList')" class="pe-tool" title="فهرست نشانه‌دار"><i class="fas fa-list-ul"></i></button><button type="button" onclick="peExec('insertOrderedList')" class="pe-tool" title="فهرست شماره‌دار"><i class="fas fa-list-ol"></i></button><button type="button" onclick="peExec('indent')" class="pe-tool" title="افزایش تورفتگی"><i class="fas fa-indent"></i></button><button type="button" onclick="peExec('outdent')" class="pe-tool" title="کاهش تورفتگی"><i class="fas fa-outdent"></i></button>
                    <span class="mx-1 h-7 w-px bg-gray-300"></span>
                    <button type="button" onclick="peInsertLink()" class="pe-tool" title="درج لینک"><i class="fas fa-link"></i></button><button type="button" onclick="peExec('unlink')" class="pe-tool" title="حذف لینک"><i class="fas fa-unlink"></i></button><button type="button" onclick="peInsertImage()" class="pe-tool" title="درج تصویر"><i class="fas fa-image"></i></button><button type="button" onclick="peInsertTable()" class="pe-tool" title="درج جدول"><i class="fas fa-table"></i></button><button type="button" onclick="peExec('insertHorizontalRule')" class="pe-tool" title="خط افقی">―</button>
                    <label class="pe-tool cursor-pointer" title="رنگ متن"><i class="fas fa-font"></i><input type="color" onchange="peExec('foreColor',this.value)" class="sr-only"></label><label class="pe-tool cursor-pointer" title="رنگ پس‌زمینه"><i class="fas fa-fill-drip"></i><input type="color" onchange="peExec('hiliteColor',this.value)" class="sr-only"></label>
                    <span class="mx-1 h-7 w-px bg-gray-300"></span>
                    <button type="button" onclick="peExec('removeFormat')" class="pe-tool" title="پاک‌کردن قالب"><i class="fas fa-eraser"></i></button><button type="button" onclick="peExec('undo')" class="pe-tool" title="واگرد"><i class="fas fa-undo"></i></button><button type="button" onclick="peExec('redo')" class="pe-tool" title="از نو"><i class="fas fa-redo"></i></button><button type="button" onclick="peToggleFullscreen()" class="pe-tool" title="تمام‌صفحه"><i class="fas fa-expand"></i></button>
                </div>
                <div id="peVisualContent" contenteditable="true" dir="rtl" data-placeholder="متن نوشته را اینجا بنویسید..." oninput="peVisualChanged()" class="prose max-w-none min-h-[480px] w-full p-6 text-base leading-8 focus:outline-none"></div>
                <textarea id="peContent" rows="24" dir="ltr" oninput="peCodeChanged()" spellcheck="false" placeholder="کد HTML نوشته را اینجا وارد کنید..." class="hidden min-h-[480px] w-full resize-y border-0 bg-gray-950 p-6 font-mono text-sm leading-7 text-green-300 focus:outline-none"></textarea>
            </div>

            <!-- خلاصه و توضیحات -->
            <div class="bg-white rounded-3xl shadow-sm p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">خلاصه (Excerpt)</label>
                    <textarea id="peSummary" rows="2" class="w-full border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-400" placeholder="خلاصه کوتاه برای لیست و سئو"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">توضیحات</label>
                    <textarea id="peDescription" rows="2" class="w-full border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-400" placeholder="توضیح تکمیلی"></textarea>
                </div>
            </div>
        </div>

        <!-- سایدبار (۱/۳) — شبیه وردپرس -->
        <div class="space-y-5">
            <!-- انتشار -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b font-semibold text-sm bg-gray-50">انتشار</div>
                <div class="p-5 space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">وضعیت</span>
                        <select id="peStatus" class="border border-gray-200 rounded-xl py-1.5 px-3 text-sm">
                            <option value="draft">پیش‌نویس</option>
                            <option value="pending">در انتظار بررسی</option>
                            <option value="published">منتشرشده</option>
                            <option value="private">خصوصی</option>
                            <option value="future">زمان‌بندی‌شده</option>
                            <option value="trash">زباله‌دان</option>
                        </select>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">دسترسی</span>
                        <select id="peVisibility" class="border border-gray-200 rounded-xl py-1.5 px-3 text-sm">
                            <option value="public">عمومی</option>
                            <option value="private">خصوصی</option>
                            <option value="followers">دنبال‌کنندگان</option>
                            <option value="premium">ویژه (Premium)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-500 block mb-1">تاریخ انتشار</label>
                        <input type="datetime-local" id="pePublishedAt"
                               class="w-full border border-gray-200 rounded-xl py-2 px-3 text-sm">
                    </div>
                    <div>
                        <label class="text-gray-500 block mb-1">رمز عبور (اختیاری)</label>
                        <input type="text" id="pePassword" class="w-full border border-gray-200 rounded-xl py-2 px-3 text-sm">
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button onclick="savePostEditor('draft')" class="flex-1 border py-2.5 rounded-xl text-sm hover:bg-gray-50">پیش‌نویس</button>
                        <button onclick="savePostEditor('publish')" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl text-sm hover:bg-indigo-700">انتشار</button>
                    </div>
                    <button onclick="movePostToTrash()" id="peTrashBtn" class="w-full text-red-500 text-sm py-2 hover:underline">انتقال به زباله‌دان</button>
                </div>
            </div>

            <!-- نوع نوشته -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b font-semibold text-sm bg-gray-50">نوع نوشته</div>
                <div class="p-5">
                    <select id="peType" class="w-full border border-gray-200 rounded-xl py-2.5 px-3 text-sm">
                        <option value="post">نوشته</option>
                        <option value="product">محصول</option>
                        <option value="music_theory">تئوری موسیقی</option>
                    </select>
                </div>
            </div>

            <!-- دسته‌ها -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b font-semibold text-sm bg-gray-50">دسته‌ها</div>
                <div class="p-5 space-y-2">
                    <input type="text" id="peCategories" placeholder="آموزش, پیانو, خبر"
                           class="w-full border border-gray-200 rounded-xl py-2.5 px-3 text-sm">
                    <p class="text-xs text-gray-400">دسته‌ها را با کاما جدا کنید</p>
                </div>
            </div>

            <!-- تصویر شاخص -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b font-semibold text-sm bg-gray-50">تصویر شاخص</div>
                <div class="p-5 space-y-3">
                    <div id="peCoverPreview" class="hidden rounded-2xl overflow-hidden bg-gray-100 aspect-video flex items-center justify-center text-gray-400 text-sm">
                        <img id="peCoverImg" src="" alt="" class="w-full h-full object-cover">
                    </div>
                    <input type="text" id="peCover" placeholder="آدرس تصویر یا URL"
                           onchange="updateCoverPreview()"
                           class="w-full border border-gray-200 rounded-xl py-2.5 px-3 text-sm">
                    <input type="number" id="peCoverMediaId" placeholder="cover_media_id"
                           class="w-full border border-gray-200 rounded-xl py-2.5 px-3 text-sm">
                </div>
            </div>

            <!-- پیشرفته -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b font-semibold text-sm bg-gray-50">پیشرفته</div>
                <div class="p-5 space-y-3 text-sm">
                    <div>
                        <label class="text-gray-500 block mb-1">نوشته‌های مرتبط (IDها)</label>
                        <input type="text" id="peRelated" class="w-full border border-gray-200 rounded-xl py-2 px-3">
                    </div>
                    <div>
                        <label class="text-gray-500 block mb-1">GUID / لینک دائمی</label>
                        <input type="text" id="peGuid" class="w-full border border-gray-200 rounded-xl py-2 px-3">
                    </div>
                    <div class="flex justify-between text-gray-400 pt-2 border-t">
                        <span>بازدید</span>
                        <span id="peViewsCount">0</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>نظرات</span>
                        <span id="peCommentCount">0</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>شناسه</span>
                        <span id="pePostId">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
