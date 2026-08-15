<div id="settings" class="section hidden">
    <div class="mb-6"><h1 class="text-3xl font-bold">تنظیمات</h1><p class="mt-1 text-gray-500">تنظیمات عمومی ظاهر و رفتار سایت</p></div>
    <div class="max-w-3xl rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-bold">فونت اصلی پروژه</h2>
        <p class="mt-2 text-sm text-gray-500">خانواده فونت و اندازه عمومی متن‌ها روی پنل مدیریت و صفحات عمومی اعمال می‌شود. نوع اعداد به‌صورت خودکار از زبان صفحه پیروی می‌کند.</p>
        <div class="mt-6 grid gap-5 md:grid-cols-2">
            <div class="space-y-5">
                <label class="block"><span class="mb-2 block text-sm font-medium">خانواده فونت</span><select id="sitePrimaryFont" onchange="previewSiteFont()" class="w-full rounded-xl border px-4 py-3"></select></label>
                <label class="block"><span class="mb-3 flex items-center justify-between text-sm font-medium"><span>اندازه عمومی متن‌ها</span><output id="siteFontScaleOutput" dir="ltr" class="inline-flex rounded-lg bg-indigo-50 px-3 py-1 text-indigo-700">۰</output></span><input id="siteFontScale" dir="ltr" type="range" min="-2" max="2" step="1" value="0" oninput="previewSiteFont()" class="w-full accent-indigo-600"><span id="siteFontScaleMarks" class="mt-1 flex justify-between text-xs text-gray-400" dir="ltr"></span></label>
            </div>
            <div id="siteFontPreview" class="rounded-2xl border bg-gray-50 p-5"><strong class="block text-lg">پیش‌نمایش فونت</strong><div id="siteFontPreviewContent" class="mt-2"></div></div>
        </div>
        <div class="mt-6 flex items-center gap-4"><button onclick="saveSiteSettings()" class="rounded-xl bg-indigo-600 px-6 py-3 text-white">ذخیره تنظیمات</button><span id="siteSettingsMessage" class="text-sm text-emerald-600"></span></div>
    </div>
</div>
