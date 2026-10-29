const assert = require('node:assert/strict');
const fs = require('node:fs');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {tableRuntime, renderPanelView} = require('./panel-table-fixture.cjs');

const layoutCss = fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php', 'utf8').match(/<style>([\s\S]*?)<\/style>/)[1];
const actionSelector = {
  addTerm: '[onclick="openAddTermModal()"]',
  batch: '[onclick="openTeacherTermBatchModal()"]',
  discount: '[onclick="openAddTermDiscountModal()"]',
  excel: '[onclick="exportTermsToExcel()"]',
  pdf: '[onclick="exportTermsToPDF()"]',
};

(async () => {
  const browser = await chromium.launch({channel: 'msedge', headless: true});
  try {
    for (const lang of ['fa', 'en']) for (const width of [390, 1280]) {
      const page = await browser.newPage({viewport: {width, height: 900}}), errors = [];
      page.on('pageerror', error => errors.push(error.message));
      await page.route('**/*', route => {
        const pathname = new URL(route.request().url()).pathname;
        if (pathname === '/academy/admin/terms') return route.fulfill({json: {success: true, data: {terms: [], currencies: [], discounts: [], classrooms: [], members: []}}});
        if (pathname === '/academy/admin/term-discounts') return route.fulfill({json: {success: true, data: {discounts: [], organizations: [], ready: true}}});
        return route.fulfill({json: {success: true, data: {version: 'fixture', organizations: [], courses: []}}});
      });
      await page.goto('http://term-toolbar.test/');
      await page.setContent(`<html lang="${lang}" dir="${lang === 'en' ? 'ltr' : 'rtl'}" data-mode="light" data-theme="emerald"><style>${layoutCss}.hidden{display:none}body{margin:0;padding:16px}</style><header><h1 id="panelPageTitle"></h1></header><main id="mainContent">${renderPanelView('terms', lang)}</main><div id="modalContainer"></div>${renderPanelView('panel-page-header-template')}${renderPanelView('panel-organization-tabs-template')}${renderPanelView('panel-filters-template')}${tableRuntime()}</html>`);
      await page.addScriptTag({path: 'assets/vendor/tailwind/tailwindcss.js'});
      await page.addStyleTag({path: 'assets/theme/theme.css'});
      await page.evaluate(lang => { window.adminLocale = lang; window.adminUiMap = {}; window.alert = message => { throw Error(message); }; }, lang);
      for (const script of ['panel-organization-filters', 'term-templates', 'terms', 'admin']) await page.addScriptTag({path: `assets/Analytics/js/${script}.js`});
      await page.evaluate(selectors => {
        window.termToolbarOriginals = {};
        Object.entries(selectors).forEach(([name, selector]) => { const button = document.querySelector('#terms ' + selector); button.id = 'toolbar-' + name; termToolbarOriginals[name] = button; });
        window.termToolbarClicks = [];
        window.openAddTermModal = () => termToolbarClicks.push('term');
        window.openTeacherTermBatchModal = () => termToolbarClicks.push('batch');
        window.openAddTermDiscountModal = () => termToolbarClicks.push('discount');
        document.querySelector('#toolbar-batch').addEventListener('click', () => termToolbarClicks.push('listener'));
        watchPanelAddActions(); showSection('terms');
      }, actionSelector);
      const toolbar = page.locator('#terms > [data-panel-page-toolbar]');
      await toolbar.waitFor();
      const assertTab = async selected => {
        const management = selected === 'terms';
        for (const name of ['addTerm', 'batch', 'excel', 'pdf']) assert.equal(await page.locator('#toolbar-' + name).isVisible(), management, `${lang}/${width}: ${name} belongs to term management`);
        assert.equal(await page.locator('#toolbar-discount').isVisible(), !management, `${lang}/${width}: discount add belongs to discounts`);
        assert.equal(await page.locator('[data-term-panel="terms"]').isVisible(), management);
        assert.equal(await page.locator('[data-term-panel="discounts"]').isVisible(), !management);
        assert.equal(await toolbar.locator('[data-panel-page-action-group]').count(), 0);
        assert.equal(await page.locator('#terms [data-panel-filter-actions] [data-panel-page-action-group]').count(), 2);
        assert.equal(await page.locator('#terms > [data-panel-page-toolbar]').count(), 1);
      };
      await assertTab('terms');
      const filter = page.locator('#termsBranchTabs');
      assert.equal(await filter.evaluate(element => !!element.closest('[data-panel-filter-actions]')), true);
      if (width === 1280) {
        const f = await filter.boundingBox(), a = await page.locator('#toolbar-addTerm').boundingBox();
        assert.ok(Math.abs((f.y + f.height / 2) - (a.y + a.height / 2)) < 20, 'Organization filters and actions share a row');
        assert.ok(lang === 'fa' ? a.x < f.x : a.x > f.x, 'Actions are opposite organization filters');
      }
      for (const name of ['addTerm', 'batch']) assert.deepEqual(await page.locator('#toolbar-' + name).evaluate(element => { const rect = element.getBoundingClientRect(); return [rect.width, rect.height]; }), [44, 44]);
      const batch = page.locator('#toolbar-batch');
      assert.equal(await batch.getAttribute('data-panel-add-kind'), 'list');
      assert.equal(await batch.locator(':scope > svg[data-panel-add-symbol]').count(), 1);
      assert.notEqual(await batch.locator(':scope > svg').innerHTML(), await page.locator('#toolbar-addTerm > svg').innerHTML(), 'Batch addition has a list-plus symbol, distinct from one-item addition');
      assert.ok((await batch.getAttribute('aria-label')).includes(lang === 'en' ? 'teacher terms' : 'ترم'));
      assert.equal(await batch.getAttribute('aria-label'), await batch.getAttribute('title'));
      await batch.click(); await page.locator('#toolbar-addTerm').click();
      assert.deepEqual(await page.evaluate(() => termToolbarClicks), ['batch', 'listener', 'term']);
      if (lang === 'fa' && width === 1280) await toolbar.screenshot({path: 'storage/notation-browser-check/term-actions-terms.png'});
      for (let repeat = 0; repeat < 3; repeat++) {
        await page.locator('[data-term-tab="discounts"]').click();
        await page.waitForFunction(() => termDiscountCatalog?.ready === true);
        await page.evaluate(() => { alignPanelPageActions(document.getElementById('terms')); showSection('terms'); });
        await assertTab('discounts');
        assert.deepEqual(await page.locator('#toolbar-discount').evaluate(element => { const rect = element.getBoundingClientRect(); return [rect.width, rect.height]; }), [44, 44]);
        if (repeat === 0) {
          await page.locator('#toolbar-discount').click();
          if (lang === 'fa' && width === 1280) await toolbar.screenshot({path: 'storage/notation-browser-check/term-actions-discounts.png'});
        }
        await page.locator('[data-term-tab="terms"]').click();
        await page.evaluate(() => alignPanelPageActions(document.getElementById('terms')));
        await assertTab('terms');
      }
      assert.equal(await page.evaluate(() => Object.entries(termToolbarOriginals).every(([name, element]) => document.getElementById('toolbar-' + name) === element)), true, 'Actions retain their original nodes, IDs and listeners');
      await page.evaluate(() => { window.academyHasNoBranches = true; applyAcademyOrganizationTabs(); alignPanelPageActions(document.getElementById('terms')); });
      assert.equal(await filter.isVisible(), false, 'Branchless accounts do not regain hidden organization filters');
      assert.equal(await page.locator('#toolbar-addTerm').isVisible(), true, 'Hiding organization filters must not hide the actions');
      const emptyRow = await page.locator('#terms > [data-panel-content=""][data-term-panel="terms"] > [data-panel-filter-actions]').boundingBox();
      const remainingActions = await page.locator('#terms [data-term-panel="terms"] [data-panel-page-actions]').boundingBox();
      assert.ok(Math.abs(lang === 'fa' ? remainingActions.x - emptyRow.x : remainingActions.x + remainingActions.width - emptyRow.x - emptyRow.width) < 2, 'Actions retain their edge without visible organization filters');
      assert.deepEqual(await page.evaluate(() => termToolbarClicks), ['batch', 'listener', 'term', 'discount']);
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Term toolbar: real nested tabs, scoped add/export actions, list-plus icon, preserved buttons/listeners, repeated alignment, Persian/English and mobile/desktop passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
