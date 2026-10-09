const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    await page.route('https://panel.test/', (route) => route.fulfill({ status: 200, contentType: 'text/html', body: '<!doctype html><html lang="fa"><body></body></html>' }));
    await page.route('https://panel.test/analytics/personal-offerings/lessons', async (route) => {
      const request = route.request();
      assert.equal(request.method(), 'POST');
      assert.equal(request.headers()['x-csrf-token'], 'fixture-token');
      const body = new URLSearchParams(request.postData());
      const payload = JSON.parse(Buffer.from(body.get('payload_b64').replace(/-/g, '+').replace(/_/g, '/'), 'base64').toString('utf8'));
      assert.deepEqual([payload.lesson_id, payload.level_id, payload.is_primary], [7, 3, 1]);
      await route.fulfill({ status: 422, contentType: 'application/json', body: JSON.stringify({ success: false, message: 'خطای آزمایشی' }) });
    });
    await page.route('https://panel.test/analytics/personal-offerings/schedules', async (route) => {
      const body = new URLSearchParams(route.request().postData());
      const payload = JSON.parse(Buffer.from(body.get('payload_b64').replace(/-/g, '+').replace(/_/g, '/'), 'base64').toString('utf8'));
      assert.equal(payload.repeatPeriod, 'دو هفته');
      assert.equal(payload.repeatDate, '2026-10-10');
      assert.equal(payload.ranges.length, 2);
      assert.deepEqual(payload.ranges.map((item) => [item.start, item.end]), [['09:00', '10:00'], ['11:00', '12:00']]);
      await route.fulfill({ status: 422, contentType: 'application/json', body: JSON.stringify({ success: false, message: 'خطای برنامه' }) });
    });
    await page.goto('https://panel.test/');
    await page.setContent('<button data-personal-open="lesson">افزودن</button><form id="personalLessonForm" class="hidden"><input name="id" value="0"><input name="lesson_id" value="7"><input name="level_id" value="3"><input name="start_date" value="2026-10-08"><input type="checkbox" name="is_primary" value="1"><input name="summary"><textarea name="description"></textarea><button type="submit">ذخیره</button><p class="hidden" data-personal-error></p></form><button data-personal-open="schedule">زمان‌بندی</button><form id="personalScheduleForm" class="hidden"><input name="id" value="0"><select name="repeatPeriod"><option>هفتگی</option><option>دو هفته</option></select><div data-personal-day><select name="day"><option>شنبه</option></select></div><div data-personal-date><input type="date" name="repeatDate"></div><select name="timezone"><option>Asia/Tehran</option></select><div data-personal-ranges></div><button type="button" data-personal-add-range>بازه</button><input name="summary"><textarea name="description"></textarea><button type="submit">ذخیره زمان</button><p class="hidden" data-personal-error></p></form>');
    await page.evaluate(() => { window.adminCsrfToken = 'fixture-token'; });
    await page.addScriptTag({ path: 'assets/Analytics/js/personal-offerings.js' });
    await page.locator('[data-personal-open=lesson]').click();
    assert.equal(await page.locator('#personalLessonForm').evaluate((node) => node.classList.contains('hidden')), false);
    await page.locator('[name=is_primary]').check();
    await page.locator('#personalLessonForm [type=submit]').click();
    await page.getByText('خطای آزمایشی').waitFor();
    assert.equal(await page.locator('#personalLessonForm [type=submit]').isDisabled(), false);
    await page.locator('[data-personal-open=schedule]').click();
    await page.locator('[name=repeatPeriod]').selectOption('دو هفته');
    await page.locator('[name=repeatDate]').fill('2026-10-10');
    await page.locator('.personal-range').first().locator('[data-range-start]').fill('09:00');
    await page.locator('.personal-range').first().locator('[data-range-end]').fill('10:00');
    await page.locator('[data-personal-add-range]').click();
    await page.locator('.personal-range').last().locator('[data-range-start]').fill('11:00');
    await page.locator('.personal-range').last().locator('[data-range-end]').fill('12:00');
    await page.locator('#personalScheduleForm [type=submit]').click();
    await page.getByText('خطای برنامه').waitFor();
    console.log('Personal offerings browser: lesson and multi-range schedule payloads, CSRF and inline errors passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
