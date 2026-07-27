<div id="post-editor" class="section hidden">
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
                <div class="border-b px-4 py-2 flex flex-wrap gap-1 bg-gray-50">
                    <button type="button" onclick="peFormat('bold')" class="px-3 py-1.5 rounded-lg hover:bg-white text-sm" title="ضخیم"><b>B</b></button>
                    <button type="button" onclick="peFormat('italic')" class="px-3 py-1.5 rounded-lg hover:bg-white text-sm italic" title="کج">I</button>
                    <button type="button" onclick="peFormat('underline')" class="px-3 py-1.5 rounded-lg hover:bg-white text-sm underline" title="زیرخط">U</button>
                    <span class="w-px bg-gray-200 mx-1"></span>
                    <button type="button" onclick="peInsertHeading()" class="px-3 py-1.5 rounded-lg hover:bg-white text-sm">H2</button>
                    <button type="button" onclick="peInsertList()" class="px-3 py-1.5 rounded-lg hover:bg-white text-sm">• لیست</button>
                    <button type="button" onclick="peInsertLink()" class="px-3 py-1.5 rounded-lg hover:bg-white text-sm">لینک</button>
                </div>
                <textarea id="peContent" rows="18"
                          placeholder="متن نوشته را اینجا بنویسید..."
                          class="w-full border-0 p-6 focus:outline-none resize-y text-base leading-relaxed"></textarea>
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
                        <input type="text" id="pePublishedAt" placeholder="۱۴۰۳/۰۹/۱۵ ۱۰:۳۰"
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

            <!-- شعبه -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b font-semibold text-sm bg-gray-50">شعبه</div>
                <div class="p-5">
                    <select id="peBranch" class="w-full border border-gray-200 rounded-xl py-2.5 px-3 text-sm"></select>
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