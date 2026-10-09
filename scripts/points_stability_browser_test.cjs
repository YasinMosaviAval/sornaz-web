const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    await page.route('https://panel.test/', (route) => route.fulfill({ status: 200, contentType: 'text/html', body: '<!doctype html><body></body>' }));
    await page.route('https://panel.test/analytics/admin-points', (route) => route.fulfill({
      status: 200, contentType: 'application/json',
      body: JSON.stringify({ success: true, data: { rules: [{ id: 7, title: 'ثبت درس', type: 'general', category: 'academic', points: 10, source: 'database', action: 'user_lessons.insert', status: 'active', repeatMode: 'event' }], organizations: [], balance: { general: 10, professional: 0 }, canManage: false } }),
    }));
    await page.goto('https://panel.test/');
    await page.setContent('<div id="points"><strong id="pointGeneralBalance"></strong><strong id="pointProfessionalBalance"></strong><strong id="pointActiveRules"></strong><table id="pointsTable"><tbody></tbody></table><span id="pointsPaginationInfo"></span><div id="pointsPaginationButtons"></div></div>');
    await page.evaluate(() => { const original = window.setInterval; window.setInterval = (callback, delay) => { if (delay === 12000) { window.__pointsTick = callback; return 1; } return original(callback, delay); }; });
    await page.addScriptTag({ path: 'assets/Analytics/js/points.js' });
    await page.locator('#pointsTable tbody tr').waitFor();
    await page.evaluate(() => {
      window.__pointMutations = 0;
      const observer = new MutationObserver((records) => { window.__pointMutations += records.length; });
      observer.observe(document.querySelector('#pointsTable tbody'), { childList: true, subtree: true, characterData: true });
      observer.observe(document.getElementById('pointsPaginationButtons'), { childList: true, subtree: true, characterData: true });
    });
    const response = page.waitForResponse((item) => item.url().endsWith('/analytics/admin-points'));
    await page.evaluate(() => window.__pointsTick());
    await response;
    await page.waitForTimeout(100);
    assert.equal(await page.evaluate(() => window.__pointMutations), 0);
    console.log('Points browser: unchanged background refresh preserves rows and pagination.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
