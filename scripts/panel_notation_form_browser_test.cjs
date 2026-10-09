const assert = require('node:assert/strict');
const path = require('node:path');
const { pathToFileURL } = require('node:url');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    await page.addInitScript(() => {
      window.NOTATION_BOOT = { locale: 'fa', userId: 2, embedded: true, websitePanel: true };
      window.SornazNotation = { postMessage(raw) {
        const request = JSON.parse(raw);
        if (!request.id) return;
        const data = request.action === 'instruments' ? [{ id: 7, fa: 'پیانو', en: 'Piano' }] : request.action === 'list' ? { items: [], has_more: false } : true;
        queueMicrotask(() => window.Notation.receive(request.id, data, null));
      } };
    });
    await page.goto(pathToFileURL(path.resolve('assets/notation/index.html')).href);
    await page.evaluate(() => document.body.classList.add('website-panel'));
    await page.addStyleTag({ path: 'assets/notation/panel.css' });
    await page.evaluate(() => window.Notation.command('new'));
    await page.locator('#metadata').waitFor();
    assert.deepEqual(await page.locator('#metadata [name]').evaluateAll((nodes) => nodes.map((node) => node.name)), ['title', 'composer', 'arranger', 'lyricist', 'instrument', 'scale_type', 'key', 'clef', 'time', 'tempo_note', 'tempo_dots', 'bpm', 'tempo_text', 'subtitle']);
    assert.equal(await page.locator('.header').count(), 0);
    assert.equal(await page.locator('#metadata textarea[name=subtitle]').count(), 1);
    assert.equal(await page.locator('[name=key]').isDisabled(), true);
    await page.locator('[name=scale_type]').selectOption('minor');
    assert.equal(await page.locator('[name=key]').isDisabled(), false);
    await page.locator('[name=key]').selectOption('Am');
    await page.locator('.beat-picker summary').click();
    await page.locator('[data-action=beat-unit][data-value="q:2"]').click();
    assert.deepEqual(await page.locator('[name=tempo_note],[name=tempo_dots]').evaluateAll((nodes) => nodes.map((node) => node.value)), ['q', '2']);
    assert.equal(await page.locator('.shell.form').evaluate((node) => getComputedStyle(node).borderTopWidth), '0px');
    assert.deepEqual(errors, []);
    console.log('Panel notation form: Flutter field order, scale, dotted beat and unboxed layout passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
