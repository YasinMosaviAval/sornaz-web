<div id="settings" class="section hidden" data-site-admin="<?= (int) auth()->id() === 1 ? '1' : '0' ?>">
    <div class="mb-6"><h1 class="text-3xl font-bold">تنظیمات</h1><p class="mt-1 text-gray-500">تنظیمات عمومی ظاهر و رفتار سایت</p></div>
    <div class="max-w-3xl space-y-5">
      <details open class="rounded-2xl border bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <summary class="cursor-pointer text-xl font-bold"><i class="fas fa-palette ml-2 text-indigo-600"></i>ظاهر</summary>
        <p class="mt-2 text-sm text-gray-500">تم رنگی، حالت روشن یا تیره و زبان را انتخاب کنید.</p>
        <div class="mt-6 space-y-3">
            <label class="flex items-center justify-between gap-4 rounded-xl border p-4 dark:border-slate-700"><span class="font-medium">تم رنگی</span><select id="siteColorTheme" onchange="applyAppearanceSetting('colorTheme',this.value)" class="max-w-40 rounded-xl border px-4 py-2 dark:bg-slate-800"><option value="indigo">نیلی</option><option value="emerald">سبز</option><option value="rose">رز</option><option value="amber">کهربایی</option></select></label>
            <label class="flex items-center justify-between gap-4 rounded-xl border p-4 dark:border-slate-700"><span><strong class="block">حالت تاریک</strong><small class="text-gray-500">نمایش پنل با زمینهٔ تیره</small></span><input id="siteThemeMode" type="checkbox" onchange="applyAppearanceSetting('themeMode',this.checked?'dark':'light')" class="h-5 w-5 accent-indigo-600"></label>
            <label class="flex items-center justify-between gap-4 rounded-xl border p-4 dark:border-slate-700"><span class="font-medium">زبان برنامه</span><select id="siteLanguage" onchange="applyAppearanceSetting('language',this.value)" class="max-w-40 rounded-xl border px-4 py-2 dark:bg-slate-800"><option value="fa">فارسی</option><option value="en">English</option></select></label>
            <?php if ((int) auth()->id() === 1): ?><label class="flex items-center gap-3 rounded-xl border p-4"><input id="siteEditMode" type="checkbox" onchange="applyAppearanceSetting('editMode',this.checked)" class="h-5 w-5 rounded"><span><strong class="block">حالت ویرایش</strong><small class="text-gray-500">امکان ویرایش متن‌های ثابت با کلیک روی آن‌ها</small></span></label><?php endif; ?>
        </div>
      </details>
      <details open class="rounded-2xl border bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <summary class="cursor-pointer text-xl font-bold"><i class="fas fa-sliders-h ml-2 text-indigo-600"></i>المان‌ها</summary>
        <p class="mt-2 text-sm text-gray-500"><?= (int) auth()->id() === 1 ? 'خانواده فونت و اندازه عمومی متن‌ها روی پنل مدیریت و صفحات عمومی اعمال می‌شود.' : 'فونت و اندازه متن دلخواه شما در همین مرورگر ذخیره می‌شود.' ?> نوع اعداد به‌صورت خودکار از زبان صفحه پیروی می‌کند.</p>
        <div class="mt-6 space-y-5">
            <div class="space-y-5">
                <label class="block"><span class="mb-2 block text-sm font-medium">خانواده فونت</span><select id="sitePrimaryFont" onchange="previewSiteFont()" class="w-full rounded-xl border px-4 py-3"></select></label>
                <label class="block"><span class="mb-3 flex items-center justify-between text-sm font-medium"><span>اندازه عمومی متن‌ها</span><output id="siteFontScaleOutput" dir="ltr" class="inline-flex rounded-lg bg-indigo-50 px-3 py-1 text-indigo-700">۰</output></span><input id="siteFontScale" dir="ltr" type="range" min="-2" max="2" step="1" value="0" oninput="previewSiteFont()" class="w-full accent-indigo-600"><span id="siteFontScaleMarks" class="mt-1 flex justify-between text-xs text-gray-400" dir="ltr"></span></label>
                <label class="block"><span class="mb-3 flex items-center justify-between text-sm font-medium"><span>وزن قلم</span><output id="siteFontWeightOutput" dir="ltr" class="rounded-lg bg-indigo-50 px-3 py-1 text-indigo-700">۰</output></span><input id="siteFontWeight" dir="ltr" type="range" min="0" max="5" step="1" value="0" oninput="previewSiteFont()" class="w-full accent-indigo-600"></label>
                <label class="block"><span class="mb-3 flex items-center justify-between text-sm font-medium"><span>گردی گوشهٔ المان‌ها</span><output id="siteCornerRadiusOutput" dir="ltr" class="rounded-lg bg-indigo-50 px-3 py-1 text-indigo-700">۴ px</output></span><input id="siteCornerRadius" dir="ltr" type="range" min="0" max="16" step="1" value="4" oninput="previewSiteFont()" class="w-full accent-indigo-600"></label>
            </div>
            <div id="siteFontPreview" class="rounded-2xl border bg-gray-50 p-5"><strong class="block text-lg">پیش‌نمایش فونت</strong><div id="siteFontPreviewContent" class="mt-2"></div></div>
        </div>
      </details>
        <div class="mt-6 flex items-center gap-4"><button onclick="saveSiteSettings()" class="rounded-xl bg-indigo-600 px-6 py-3 text-white">ذخیره تنظیمات</button><span id="siteSettingsMessage" class="text-sm text-emerald-600"></span></div>
    </div>
</div>
