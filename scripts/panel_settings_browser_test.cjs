const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const writes = [];
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    await page.route('**/*', async (route) => {
      const path = new URL(route.request().url()).pathname;
      if (path === '/assets/Analytics/js/settings.js') return route.fulfill({ path: './assets/Analytics/js/settings.js' });
      if (path === '/assets/theme/theme.js') return route.fulfill({ path: './assets/theme/theme.js' });
      if (path === '/analytics/site-settings') return route.fulfill({ json: { success: true, data: { primaryFont: 'vazir', fontFamily: 'Vazir, sans-serif', fontScale: 2, rootFontSize: '18px', fontWeight: 3, cornerRadius: 12, colorTheme: 'indigo', themeMode: 'light', language: 'fa', fonts: [{ value: 'vazir', label: 'Vazir', fontFamily: 'Vazir, sans-serif' }] } } });
      if (path === '/analytics/admin-settings') {
        writes.push(route.request().postData());
        return route.fulfill({ status: 403, json: { success: false, message: 'Forbidden' } });
      }
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<div id="settings" data-site-admin="0"><select id="sitePrimaryFont"></select><select id="siteColorTheme"><option value="indigo">Indigo</option><option value="rose">Rose</option></select><input id="siteThemeMode" type="checkbox"><span id="siteSettingsMessage"></span></div><script src="/assets/theme/theme.js"></script><script src="/assets/Analytics/js/settings.js"></script>` });
    });
    await page.goto('http://sornaz.test/');
    await page.waitForFunction(() => document.getElementById('sitePrimaryFont').options.length === 1);
    await page.evaluate(async () => {
      await window.applyAppearanceSetting('colorTheme', 'rose');
      await window.applyAppearanceSetting('themeMode', 'dark');
      localStorage.setItem('sornaz.personalFontScale', '-2');
      localStorage.setItem('sornaz.personalFontWeight', '5');
      localStorage.setItem('sornaz.personalCornerRadius', '0');
      await window.saveSiteSettings();
    });
    assert.deepEqual(await page.evaluate(() => [document.documentElement.dataset.theme, document.documentElement.dataset.mode, localStorage.getItem('sornaz.personalFont'), document.documentElement.style.getPropertyValue('--site-root-font-size'), document.documentElement.style.getPropertyValue('--site-font-weight'), document.documentElement.style.getPropertyValue('--site-corner-radius'), document.getElementById('siteSettingsMessage').textContent]), ['rose', 'dark', 'vazir', '18px', '700', '12px', 'تنظیمات شخصی در این مرورگر ذخیره شد.']);
    assert.equal(await page.locator('#siteFontScale, #siteFontWeight, #siteCornerRadius').count(), 0);
    assert.equal(await page.locator('#siteLanguage').count(), 0);
    assert.equal(writes.length, 0, 'Non-admin settings wrote site-wide data');
    assert.deepEqual(errors, []);
    console.log('Panel settings: non-admin controls hidden, site typography inherited, personal appearance saved without site-wide write.');
  } finally {
    await browser.close();
  }
})().catch((error) => { console.error(error); process.exitCode = 1; });
