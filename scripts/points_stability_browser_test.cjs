const assert = require('node:assert/strict');
const { tableRuntime } = require('./panel-table-fixture.cjs');
const { execFileSync } = require('node:child_process');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    await page.route('https://panel.test/', (route) => route.fulfill({ status: 200, contentType: 'text/html', body: '<!doctype html><body></body>' }));
    let manager = false, ruleStatus = 'active';
    const updates = [];
    await page.route('https://panel.test/analytics/admin-points/7/update', async route => {
      const params = new URLSearchParams(route.request().postData());
      const data = JSON.parse(Buffer.from(params.get('payload_b64'), 'base64').toString());
      updates.push(data.status); ruleStatus = data.status;
      return route.fulfill({ json: { success: true } });
    });
    await page.route('https://panel.test/analytics/admin-points', (route) => route.fulfill({
      status: 200, contentType: 'application/json',
      body: JSON.stringify({ success: true, data: { rules: [{ id: 7, title: 'ثبت درس', type: 'general', category: 'academic', points: 10, source: 'database', action: 'user_lessons.insert', status: ruleStatus, repeatMode: 'event' }], organizations: [], balance: { general: 10, professional: 0 }, canManage: manager } }),
    }));
    await page.goto('https://panel.test/');
    const views = JSON.parse(execFileSync(process.execPath, ['scripts/panel_activity_text_test.cjs'], { encoding: 'utf8', env: { ...process.env, ACTIVITY_VIEWS: '1' } }));
    await page.setContent('<style>.hidden{display:none}</style>' + views.points + tableRuntime());
    await page.locator('#points').evaluate(x => x.classList.remove('hidden'));
    await page.evaluate(() => { const original = window.setInterval; window.setInterval = (callback, delay) => { if (delay === 12000) { window.__pointsTick = callback; return 1; } return original(callback, delay); }; });
    await page.addScriptTag({ path: 'assets/Analytics/js/points.js' });
    assert.equal(await page.locator('[data-point-panel=personal]').isVisible(), true);
    assert.equal(await page.locator('[data-point-panel=rules]').isVisible(), false);
    await page.locator('[data-point-tab=rules]').click();
    await page.locator('#pointsTable tbody tr').waitFor();
    assert.equal(await page.locator('#pointsTable thead th').count(), 6);
    assert.equal(await page.locator('#pointsTable tbody tr').first().locator('td').count(), 6);
    assert.equal(await page.locator('#myPointsTable thead th').count(), 6);
    assert.equal(await page.locator('#myPointsTable tbody tr').first().locator('td').first().textContent(), '1');
    assert.equal(await page.locator('#myPointsTable').getByText('user_lessons.insert').count(), 0);
    assert.equal(await page.locator('#pointsTable code').count(), 0);
    assert.equal(await page.locator('#filterPointStatus').count(), 0);
    assert.equal(await page.locator('#pointsTable [onclick^=togglePointStatus]').count(), 0);
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
    manager = true;
    await page.setContent('<style>.hidden{display:none}</style>' + views.adminPoints + tableRuntime());
    await page.locator('#points').evaluate(x => x.classList.remove('hidden'));
    await page.addScriptTag({ path: 'assets/Analytics/js/points.js' });
    assert.equal(await page.locator('[data-point-panel=personal]').isVisible(), true);
    await page.locator('[data-point-tab=rules]').click();
    await page.locator('#pointsTable tbody tr').waitFor();
    assert.equal(await page.locator('#pointsTable thead th').count(), 9);
    assert.equal(await page.locator('#pointsTable tbody tr').first().locator('td').count(), 9);
    assert.equal(await page.locator('#pointsTable code').count(), 1);
    const toggle = page.locator('#pointsTable button[onclick^=togglePointStatus]');
    assert.equal(await page.locator('#filterPointStatus').count(), 1);
    assert.match(await toggle.getAttribute('class'), /bg-green-100/);
    await toggle.click();
    await page.waitForFunction(() => document.querySelector('#pointsTable button[onclick^=togglePointStatus]')?.classList.contains('bg-red-100'));
    await toggle.click();
    await page.waitForFunction(() => document.querySelector('#pointsTable button[onclick^=togglePointStatus]')?.classList.contains('bg-green-100'));
    assert.deepEqual(updates, ['inactive', 'active']);
    await page.locator('[data-point-tab=personal]').click();
    assert.equal(await page.locator('[data-point-panel=rules]').isVisible(), false);
    console.log('Points browser: unchanged background refresh preserves rows and pagination.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
