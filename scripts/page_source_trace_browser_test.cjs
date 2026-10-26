const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    for (const actor of [1, 2, 7]) for (const lang of ['fa', 'en']) {
      const page = await browser.newPage({ viewport: { width: 390, height: 844 } });
      const errors = [], headers = [];
      page.on('pageerror', (error) => errors.push(error.message));
      const manifest = { files: ['core/view/View.php', 'Modules/Analytics/Resources/Views/sections/sidebar.php'], sections: { lessons: ['Modules/Analytics/Resources/Views/sections/lessons.php', 'Modules/Analytics/Resources/Views/sections/panel-table-template.php'] } };
      await page.route('**/*', (route) => {
        const url = new URL(route.request().url());
        if (url.pathname.startsWith('/assets/')) return route.fulfill({ path: '.' + url.pathname });
        if (url.pathname === '/data') {
          headers.push({ origin: url.origin, trace: route.request().headers()['x-sornaz-source-trace'] });
          return route.fulfill({ json: { data: { ok: true }, _pageSources: { files: ['Modules/Academy/Services/AcademyClassroomService.php', 'Modules/<img src=x onerror=alert(1)>.php'] } }, headers: { 'X-Sornaz-Source-Trace': '1', 'Access-Control-Allow-Origin': '*' } });
        }
        return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<html lang="${lang}" data-help-enabled="${actor === 1 ? 1 : 0}"><head>${actor === 1 ? `<script id="pageSourceManifest" type="application/json">${JSON.stringify(manifest)}</script>` : ''}<script src="/assets/theme/page-source-trace.js"></script></head><body><main><section id="lessons" class="section"><h1>Lessons</h1></section></main><script src="/assets/theme/help-center.js"></script></body></html>` });
      });
      await page.goto('http://sornaz.test/');
      if (actor !== 1) {
        assert.equal(await page.locator('.context-help-button').count(), 0);
        assert.equal(await page.evaluate(() => typeof PageSourceTrace), 'undefined');
        await page.evaluate(() => fetch('/data'));
        assert.equal(headers[0].trace, undefined);
      } else {
        assert.equal(await page.locator('.context-help-button').evaluate(button=>Boolean(button.closest('header'))),true);
        assert.equal(await page.locator('.context-help-button').evaluate(button=>getComputedStyle(button).position),'static');
        await page.locator('.context-help-button').click();
        const sources = page.locator('.context-help-sources');
        assert.match(await sources.textContent(), /lessons\.php/);
        assert.match(await sources.textContent(), /sidebar\.php/);
        assert.match(await sources.textContent(), /panel-table-template\.php/);
        assert.match(await sources.textContent(), lang === 'en' ? /Files involved/ : /فایل‌های سازنده/);
        await page.evaluate(() => fetch('/data'));
        await page.waitForFunction(() => document.querySelector('.context-help-sources').textContent.includes('AcademyClassroomService.php'));
        assert.equal(headers[0].trace, '1');
        assert.equal(await sources.locator('img').count(), 0);
        assert(!(await sources.textContent()).includes('onerror'));
        const requestGroup = sources.locator('[data-source-group="requests"]');
        await requestGroup.locator('summary').click();
        await page.evaluate(() => fetch('/data'));
        await page.waitForTimeout(100);
        assert.equal(await requestGroup.getAttribute('open'), '');
        await page.evaluate(() => fetch('https://external.test/data'));
        assert.equal(headers.find((item) => item.origin === 'https://external.test').trace, undefined);
        await page.keyboard.press('Escape');
        assert.equal(await page.locator('#contextHelpDialog').count(), 0);
        await page.evaluate(()=>{document.getElementById('lessons').hidden=true;const section=document.createElement('section');section.id='courses';section.className='section';section.textContent='Courses';document.querySelector('main').append(section);});
        await page.locator('header .context-help-button').click();
        assert.equal(await page.locator('#contextHelpDialog').getAttribute('data-source-root'),'courses','The header help follows the active page');
        assert.equal(await page.locator('.context-help-button').count(),1);
      }
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Page source help: founder gating, fa/en, relative file roles, live request updates, escaping and same-origin opt-in passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
