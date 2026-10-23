const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    const tabs = active => execFileSync('php', ['-r', `function e($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');} function locale(){return 'en';} $active='${active}'; $tabs=['posts'=>['labels'=>['Posts','Posts']], 'post-categories'=>['labels'=>['Categories','Categories']]]; require 'Modules/Analytics/Resources/Views/sections/panel-page-tabs.php';`], { encoding: 'utf8' });
    await page.setContent(`<style>.hidden{display:none}</style><div id="panelPageTitle"></div><div id="dashboard" class="section hidden">Dashboard</div>${['posts', 'post-categories'].map(key => `<div id="${key}" class="section hidden">${tabs(key)}<div><h1>${key}</h1></div></div>`).join('')}`);
    await page.addScriptTag({ path: 'assets/Analytics/js/admin.js' });
    await page.evaluate(() => {
      window.calls = [];
      window.filterPostsByStatus = () => calls.push('posts');
      window.loadPostCategories = () => calls.push('categories');
      showSection('posts');
    });
    await page.locator('#posts nav button').nth(1).click();
    await page.waitForTimeout(150);
    assert.equal(await page.locator('#post-categories').isVisible(), true);
    assert.equal(await page.locator('#posts').isVisible(), false);
    assert.equal(await page.locator('#post-categories [aria-current="page"]').textContent(), 'Categories');
    await page.locator('#post-categories nav button').first().click();
    await page.waitForTimeout(150);
    assert.equal(await page.locator('#posts').isVisible(), true);
    assert.ok((await page.evaluate(() => calls)).includes('categories'));
    await page.evaluate(() => showSection('tests'));
    assert.equal(await page.locator('#dashboard').isVisible(), true);
    assert.deepEqual(errors, []);
    console.log('Posts/category tabs, data loaders and removed test-center fallback passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
