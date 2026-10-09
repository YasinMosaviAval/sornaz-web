const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

const root = path.resolve(__dirname, '..');
const renderPhp = (code) => execFileSync('php', ['-r', code], { cwd: root, encoding: 'utf8' });
const panel = renderPhp('function locale(){return "fa";} function e($value){return htmlspecialchars($value, ENT_QUOTES, "UTF-8");} include "Modules/Analytics/Resources/Views/sections/notation.php";');
const child = renderPhp('function base_path($value){return getcwd()."/".$value;} $boot=["locale"=>"fa","userId"=>2,"embedded"=>true,"websitePanel"=>true]; include "Modules/Notation/Resources/Views/index.php";');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage({ viewport: { width: 1000, height: 650 } });
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    await page.addInitScript(() => {
      window.SornazNotation = { postMessage(raw) {
        const request = JSON.parse(raw);
        if (!request.id) return;
        const data = request.action === 'instruments' ? [{ id: 7, fa: 'پیانو', en: 'Piano' }] : request.action === 'list' ? { items: [], has_more: false } : true;
        queueMicrotask(() => window.Notation.receive(request.id, data, null));
      } };
    });
    await page.route('**/*', (route) => {
      const url = new URL(route.request().url());
      if (url.pathname === '/') return route.fulfill({ contentType: 'text/html', body: `<style>.hidden{display:none}#notation{min-height:580px}</style>${panel}<script>document.getElementById('notation').classList.remove('hidden')</script>` });
      if (url.pathname === '/music-sheets') return route.fulfill({ contentType: 'text/html', body: child });
      const asset = path.resolve(root, '.' + url.pathname);
      if (asset.startsWith(root + path.sep) && fs.existsSync(asset) && fs.statSync(asset).isFile()) return route.fulfill({ path: asset });
      return route.fulfill({ status: 404, body: '' });
    });
    await page.goto('http://sornaz.test/');
    const frame = page.frameLocator('#panelNotationFrame');
    assert.equal(await frame.locator('[data-action=new]').count(), 0);
    assert.equal(await page.locator('#panelNotationTabs [data-notation-mode], #panelNotationTabs [data-notation-new]').count(), 4);
    assert.equal(await page.locator('#panelNotationTabs').evaluate((element) => getComputedStyle(element).position), 'static');
    await page.locator('#panelNotationTabs [data-notation-new]').click();
    await page.waitForFunction(() => document.getElementById('panelNotationFrame').offsetHeight > 600);
    const dimensions = await frame.locator('body').evaluate((body) => ({ content: body.scrollHeight, viewport: innerHeight }));
    assert.ok(dimensions.content <= dimensions.viewport + 2, JSON.stringify(dimensions));
    assert.equal(await page.locator('#panelNotationTabs [data-notation-new]').getAttribute('aria-pressed'), 'true');
    await page.locator('#panelNotationTabs [data-notation-mode=saved]').click();
    await frame.locator('[data-action=yes]').click();
    await frame.locator('.shell:not(.form) .sheet, .shell:not(.form) .empty').first().waitFor();
    assert.equal(await page.locator('#panelNotationTabs [data-notation-mode=saved]').getAttribute('aria-pressed'), 'true');
    assert.deepEqual(errors, []);
    console.log('Panel notation: persistent tabs, cross-frame filter and single page scroll passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
