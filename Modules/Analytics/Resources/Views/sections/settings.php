<div id="settings" class="section hidden" data-site-admin="<?= (int) auth()->id() === 1 ? '1' : '0' ?>">
    <style>
      #settings > .admin-ui-header { justify-content: flex-start; text-align: start; }
      #settings .theme-mode-switch { position: relative; display: inline-block; width: 48px; height: 28px; flex: none; border-radius: 999px; background: #cbd5e1; transition: background-color .2s ease; }
      #settings .theme-mode-switch::after { content: ''; position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px #0003; transition: transform .2s ease; }
      #settings #siteThemeMode:checked + .theme-mode-switch { background: var(--theme-500, #6366f1); }
      #settings #siteThemeMode:checked + .theme-mode-switch::after { transform: translateX(-20px); }
      #settings #siteThemeMode:focus-visible + .theme-mode-switch { outline: 2px solid var(--theme-500, #6366f1); outline-offset: 3px; }
    </style>
    <div class="mb-6"><h1 class="text-3xl font-bold">تنظیمات</h1><p class="mt-1 text-gray-500">تنظیمات عمومی ظاهر و رفتار سایت</p></div>
    <div class="max-w-3xl space-y-5">
        <div class="space-y-1">
            <label class="flex items-center justify-between gap-4 py-2"><span class="font-medium">تم رنگی</span><select id="siteColorTheme" onchange="applyAppearanceSetting('colorTheme',this.value)" class="max-w-40 rounded-xl border px-4 py-2 dark:bg-slate-800"><option value="original">اصلی</option><option value="indigo">نیلی</option><option value="emerald">سبز</option><option value="rose">رز</option><option value="amber">کهربایی</option></select></label>
            <label class="flex items-center justify-between gap-4 py-2"><span class="font-medium">حالت تاریک</span><span><input id="siteThemeMode" type="checkbox" role="switch" onchange="applyAppearanceSetting('themeMode',this.checked?'dark':'light')" class="sr-only"><span class="theme-mode-switch" aria-hidden="true"></span></span></label>
            <?php if ((int) auth()->id() === 1): ?><label class="flex items-center justify-between gap-4 py-2"><span class="font-medium">زبان برنامه</span><select id="siteLanguage" onchange="applyAppearanceSetting('language',this.value)" class="max-w-40 rounded-xl border px-4 py-2 dark:bg-slate-800"><option value="fa">فارسی</option><option value="en">English</option></select></label><?php endif; ?>
            <?php if ((int) auth()->id() === 1): ?><label class="flex items-center justify-between gap-4 py-2"><span class="font-medium">حالت ویرایش</span><input id="siteEditMode" type="checkbox" onchange="applyAppearanceSetting('editMode',this.checked)" class="h-5 w-5 rounded"></label><?php endif; ?>
        </div>
        <div class="space-y-5">
                <label class="block"><span class="mb-2 block text-sm font-medium">خانواده فونت</span><select id="sitePrimaryFont" onchange="previewSiteFont()" class="w-full rounded-xl border px-4 py-3"></select></label>
                <?php if ((int) auth()->id() === 1): ?>
                <label class="block"><span class="mb-3 flex items-center justify-between text-sm font-medium"><span>اندازه عمومی متن‌ها</span><output id="siteFontScaleOutput" dir="ltr" class="inline-flex rounded-lg bg-indigo-50 px-3 py-1 text-indigo-700">۰</output></span><input id="siteFontScale" dir="ltr" type="range" min="-2" max="2" step="1" value="0" oninput="previewSiteFont()" class="w-full accent-indigo-600"><span id="siteFontScaleMarks" class="mt-1 flex justify-between text-xs text-gray-400" dir="ltr"></span></label>
                <label class="block"><span class="mb-3 flex items-center justify-between text-sm font-medium"><span>وزن قلم</span><output id="siteFontWeightOutput" dir="ltr" class="rounded-lg bg-indigo-50 px-3 py-1 text-indigo-700">۰</output></span><input id="siteFontWeight" dir="ltr" type="range" min="0" max="5" step="1" value="0" oninput="previewSiteFont()" class="w-full accent-indigo-600"></label>
                <label class="block"><span class="mb-3 flex items-center justify-between text-sm font-medium"><span>گردی گوشهٔ المان‌ها</span><output id="siteCornerRadiusOutput" dir="ltr" class="rounded-lg bg-indigo-50 px-3 py-1 text-indigo-700">۴ px</output></span><input id="siteCornerRadius" dir="ltr" type="range" min="0" max="16" step="1" value="4" oninput="previewSiteFont()" class="w-full accent-indigo-600"></label>
                <?php endif; ?>
        </div>
        <div class="mt-6 flex items-center gap-4"><button onclick="saveSiteSettings()" class="rounded-xl bg-indigo-600 px-6 py-3 text-white">ذخیره تنظیمات</button><span id="siteSettingsMessage" class="text-sm text-emerald-600"></span></div>
    </div>
</div>
