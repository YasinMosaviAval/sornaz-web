<label class="language-switcher inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium cursor-pointer transition" dir="ltr">
    <i class="fas fa-globe"></i>
    <span data-language-label><?= e(locale() === 'fa' ? trans('auth.language_persian', 'فارسی') : trans('auth.language_english', 'English')) ?></span>
    <input type="checkbox" class="sr-only" <?= locale() === 'en' ? 'checked' : '' ?>
           aria-label="<?= e(trans('auth.language_switch_aria', 'تغییر زبان')) ?>"
           onchange="changeSiteLanguage(this.checked ? 'en' : 'fa')">
    <span class="language-switch-track relative block h-6 w-11 rounded-full transition-colors after:absolute after:top-1 after:left-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow after:transition-transform"></span>
</label>
