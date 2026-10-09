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
      if (path === '/analytics/site-settings') return route.fulfill({ json: { success: true, data: { primaryFont: 'vazir', fontFamily: 'Vazir, sans-serif', fontScale: 0, rootFontSize: '16px', colorTheme: 'indigo', themeMode: 'light', language: 'fa', fonts: [{ value: 'vazir', label: 'Vazir', fontFamily: 'Vazir, sans-serif' }] } } });
      if (path === '/analytics/admin-settings') {
        writes.push(route.request().postData());
        return route.fulfill({ status: 403, json: { success: false, message: 'Forbidden' } });
      }
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<div id="settings" data-site-admin="0"><select id="sitePrimaryFont"></select><select id="siteColorTheme"><option value="indigo">Indigo</option><option value="rose">Rose</option></select><select id="siteThemeMode"><option value="light">Light</option><option value="dark">Dark</option></select><select id="siteLanguage"><option value="fa">Persian</option></select><input id="siteFontScale" value="0"><input id="siteFontWeight" value="0"><output id="siteFontWeightOutput"></output><input id="siteCornerRadius" value="4"><output id="siteCornerRadiusOutput"></output><output id="siteFontScaleOutput"></output><div id="siteFontScaleMarks"></div><div id="siteFontPreview"></div><div id="siteFontPreviewContent"></div><span id="siteSettingsMessage"></span></div><script>window.applySiteTypography=(value)=>window.personalFont=value;</script><script src="/assets/Analytics/js/settings.js"></script>` });
    });
    await page.goto('http://sornaz.test/');
    await page.waitForFunction(() => document.getElementById('sitePrimaryFont').options.length === 1);
    await page.evaluate(async () => {
      await window.applyAppearanceSetting('colorTheme', 'rose');
      await window.applyAppearanceSetting('themeMode', 'dark');
      document.getElementById('siteFontWeight').value = '3';
      document.getElementById('siteCornerRadius').value = '12';
      await window.saveSiteSettings();
    });
    assert.deepEqual(await page.evaluate(() => [document.documentElement.dataset.theme, document.documentElement.dataset.mode, localStorage.getItem('sornaz.personalFont'), localStorage.getItem('sornaz.personalFontWeight'), localStorage.getItem('sornaz.personalCornerRadius'), document.getElementById('siteSettingsMessage').textContent]), ['rose', 'dark', 'vazir', '3', '12', 'تنظیمات شخصی در این مرورگر ذخیره شد.']);
    assert.equal(writes.length, 0, 'Non-admin settings wrote site-wide data');
    assert.deepEqual(errors, []);
    console.log('Panel settings: personal appearance and font save without site-wide write passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => { console.error(error); process.exitCode = 1; });
