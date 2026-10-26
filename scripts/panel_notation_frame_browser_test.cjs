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
    assert.equal(await frame.locator('[data-action=new]:visible').count(), 0);
    assert.equal(await page.locator('#panelNotationTabs [data-notation-mode], #panelNotationToolbar [data-notation-new]').count(), 5);
    assert.equal(await page.locator('#panelNotationTabs').evaluate((element) => getComputedStyle(element).position), 'static');
    await page.locator('[data-notation-settings]').click();
    await frame.locator('.notation-settings-form').waitFor();
    assert.equal(await frame.locator('dialog').count(),0,'Settings is a page, not a dialog');
    await frame.locator('[name=durationMode]').selectOption('3');
    await frame.locator('.notation-settings-form button').click();
    assert.equal(await page.evaluate(()=>JSON.parse(localStorage.getItem('notation_web_preferences')).durationMode),3);
    assert.equal(await page.locator('[data-notation-settings]').getAttribute('aria-pressed'),'true');
    await page.locator('#panelNotationToolbar [data-notation-new]').click();
    await frame.locator('#metadata').waitFor();
    await page.locator('#panelNotationToolbar').waitFor({state:'hidden'});
    await page.waitForFunction(() => {const frame=document.getElementById('panelNotationFrame');return frame.contentDocument.body.scrollHeight<=frame.offsetHeight+2;});
    const dimensions = await frame.locator('body').evaluate((body) => ({ content: body.scrollHeight, viewport: innerHeight }));
    assert.ok(dimensions.content <= dimensions.viewport + 2, JSON.stringify(dimensions));
    assert.equal(await page.locator('#panelNotationToolbar [data-notation-new]').getAttribute('aria-pressed'), 'true');
    await frame.locator('.web-form-actions [data-action=back]').click();
    await frame.locator('[data-action=yes]').click();
    await page.locator('#panelNotationToolbar').waitFor({state:'visible'});
    await page.locator('#panelNotationTabs [data-notation-mode=saved]').click();
    await page.waitForFunction(()=>document.querySelector('#panelNotationTabs [data-notation-mode=saved]').getAttribute('aria-pressed')==='true');
    await frame.locator('.shell:not(.form) .sheet, .shell:not(.form) .empty').first().waitFor();
    assert.equal(await page.locator('#panelNotationTabs [data-notation-mode=saved]').getAttribute('aria-pressed'), 'true');
    assert.deepEqual(errors, []);
    console.log('Panel notation: persistent tabs, cross-frame filter and single page scroll passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
