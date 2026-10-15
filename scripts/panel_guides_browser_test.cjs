const assert = require('node:assert/strict');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { renderPanelView } = require('./panel-table-fixture.cjs');
(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  let saved = {};
  try {
    for (const founder of [true, false]) {
      const page = await browser.newPage();
      await page.setContent(`<html lang="${founder ? 'fa' : 'en'}"><body><main id="mainContent"><section id="classrooms" class="section"><h1>Classes</h1></section><section id="courses" class="section"><h1>Courses</h1></section></main><template id="panelPageGuideTemplate">${renderPanelView('panel-page-guide')}</template></body></html>`);
      await page.evaluate(({ founder, saved }) => {
        window.canEditPanelGuides = founder;
        window.siteCsrfToken = 'fixture-token';
        window.guideStore = saved;
        window.fetch = async (url, options) => {
          if (options.method === 'POST') {
            const payload = JSON.parse(decodeURIComponent(escape(atob(options.body.get('payload_b64')))));
            window.lastGuidePost = { payload, token: options.headers['X-CSRF-TOKEN'] };
            guideStore[payload.page.slice(6)] = { title: { fa: payload.fa.title, en: payload.en.title }, body: { fa: payload.fa.body, en: payload.en.body } };
            return { ok: true, json: async () => ({ success: true }) };
          }
          return { ok: true, json: async () => ({ success: true, data: guideStore }) };
        };
      }, { founder, saved });
      await page.addScriptTag({ content: fs.readFileSync('assets/Analytics/js/panel-page-guides.js', 'utf8') });
      if (founder) {
        await page.waitForSelector('[data-edit-page-guide="classrooms"]');
        assert.equal(await page.locator('[data-edit-page-guide]').count(), 2, 'Founder can add guidance to every section');
        await page.locator('[data-edit-page-guide="classrooms"]').click();
        await page.locator('[name="fa-title"]').fill('<img src=x onerror=alert(1)>');
        await page.locator('[name="en-title"]').fill('English guidance');
        await page.locator('[name="fa-body"]').fill('توضیحات فارسی');
        await page.locator('[name="en-body"]').fill('English description');
        await page.locator('[data-guide-save]').click();
        await page.waitForSelector('#classrooms .panel-page-guide');
        assert.equal(await page.locator('#classrooms .panel-page-guide img').count(), 0);
        assert.equal(await page.locator('#classrooms [data-guide-title]').textContent(), '<img src=x onerror=alert(1)>');
        assert.equal(await page.evaluate(() => lastGuidePost.token), 'fixture-token');
        await page.locator('[data-edit-page-guide="classrooms"]').click();
        assert.equal(await page.locator('[name="en-body"]').inputValue(), 'English description');
        saved = await page.evaluate(() => guideStore);
      } else {
        await page.waitForSelector('#classrooms .panel-page-guide');
        assert.equal(await page.locator('[data-edit-page-guide]').count(), 0);
        assert.equal(await page.locator('#classrooms [data-guide-title]').textContent(), 'English guidance');
        assert.equal(await page.locator('#classrooms .panel-page-guide p').isVisible(), false);
        await page.locator('#classrooms summary').click();
        assert.equal(await page.locator('#classrooms .panel-page-guide p').textContent(), 'English description');
      }
      await page.close();
    }
    console.log('Panel guidance: founder editing, bilingual persistence payload, shared template, escaped output and member display passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
