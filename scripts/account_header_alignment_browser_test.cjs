const assert = require('node:assert/strict');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const account = fs.readFileSync('Modules/Analytics/Resources/Views/sections/account.php', 'utf8').split('    <!-- کاور')[0] + '</div>';
    const template = fs.readFileSync('Modules/Analytics/Resources/Views/sections/panel-page-header-template.php', 'utf8');
    for (const dir of ['rtl', 'ltr']) {
      for (const width of [390, 1280]) {
        const page = await browser.newPage({ viewport: { width, height: 800 } });
        await page.setContent(`<body dir="${dir}"><h1 id="panelPageTitle"></h1><main id="mainContent">${account}</main>${template}</body>`);
        await page.addScriptTag({ path: 'assets/vendor/tailwind/tailwindcss.js' });
        await page.addScriptTag({ path: 'assets/Analytics/js/admin-ui-templates.js' });
        await page.addScriptTag({ path: 'assets/Analytics/js/admin.js' });
        await page.evaluate(() => showSection('account'));
        const header = page.locator('#account > .admin-ui-header');
        await header.waitFor();
        const description = page.locator('#account [data-panel-description]');
        assert.equal(await description.isVisible(), true);
        assert.equal(await description.locator('xpath=ancestor::*[@data-slot="heading"]').count(), 1);
        await page.waitForFunction(() => getComputedStyle(document.querySelector('#account > .admin-ui-header')).justifyContent === 'flex-start');
        const box = await header.boundingBox();
        const text = await description.boundingBox();
        assert.ok(dir === 'rtl' ? Math.abs(box.x + box.width - text.x - text.width) < 2 : Math.abs(box.x - text.x) < 2);
        await page.close();
      }
    }
    console.log('Account description: heading side after real header normalization, RTL/LTR and mobile/desktop passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
