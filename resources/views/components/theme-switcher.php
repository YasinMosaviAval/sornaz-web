<div class="theme-switcher flex items-center gap-2 rounded-xl border px-2 py-1.5" data-theme-controls>
    <label class="sr-only"><?= e(trans('theme.palette', 'پالت رنگ')) ?></label>
    <select data-theme-select aria-label="<?= e(trans('theme.palette', 'پالت رنگ')) ?>" onchange="setSiteTheme(this.value)" class="min-w-0 rounded-lg border px-2 py-1 text-xs outline-none">
        <option value="indigo"><?= e(locale() === 'fa' ? 'نیلی' : 'Indigo') ?></option>
        <option value="emerald"><?= e(locale() === 'fa' ? 'زمردی' : 'Emerald') ?></option>
        <option value="rose"><?= e(locale() === 'fa' ? 'رُز' : 'Rose') ?></option>
        <option value="amber"><?= e(locale() === 'fa' ? 'کهربایی' : 'Amber') ?></option>
    </select>
    <button type="button" data-theme-mode onclick="toggleSiteThemeMode()" class="theme-mode-button h-8 w-8 rounded-lg inline-flex items-center justify-center" aria-label="<?= e(trans('theme.toggle_mode', 'تغییر حالت روشن و تیره')) ?>">
        <i data-light-icon class="fas fa-moon"></i><i data-dark-icon class="fas fa-sun hidden"></i>
    </button>
</div>
