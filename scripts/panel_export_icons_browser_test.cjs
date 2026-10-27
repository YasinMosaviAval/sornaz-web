const assert = require('node:assert/strict');
const fs = require('node:fs');
const {execFileSync} = require('node:child_process');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

const sections = ['branches', 'availability-exceptions', 'availabilities', 'messages', 'member-schedules', 'lessons', 'instruments', 'finance', 'terms', 'teachers', 'students', 'courses', 'course-levels', 'scheduling-rules', 'schedules', 'reports', 'classrooms'];
const render = (name, lang) => execFileSync('php', ['scripts/panel-table-view-fixture.php', name, lang, '', '1'], {encoding: 'utf8'});
const layoutCss = () => fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php', 'utf8').match(/<style>([\s\S]*?)<\/style>/)[1];
const label = (lang, kind) => lang === 'en' ? `Export ${kind === 'excel' ? 'Excel' : 'PDF'}` : `خروجی ${kind === 'excel' ? 'Excell' : 'PDF'}`;
const dimensions = element => { const r = element.getBoundingClientRect(); return [r.width, r.height]; };
const visibleTooltip = page => page.locator('#panelActionTooltip:visible');

async function assertTooltip(page, button, expected) {
  await page.mouse.move(page.viewportSize().width / 2, 500);
  await button.hover();
  await visibleTooltip(page).waitFor();
  assert.equal(await visibleTooltip(page).getAttribute('role'), 'tooltip');
  assert.match(await visibleTooltip(page).textContent(), new RegExp(expected));
  assert.equal(await visibleTooltip(page).evaluate(element => element.parentElement === document.body), true, 'Popup lives outside clipped toolbars');
  assert.ok((await button.getAttribute('aria-describedby') || '').split(/\s+/).includes('panelActionTooltip'));
  assert.equal(await visibleTooltip(page).evaluate(element => {
    const r = element.getBoundingClientRect();
    return r.left >= 0 && r.top >= 0 && r.right <= innerWidth && r.bottom <= innerHeight;
  }), true, 'Popup is clamped inside the viewport');
}

(async () => {
  const browser = await chromium.launch({channel: 'msedge', headless: true});
  try {
    for (const lang of ['fa', 'en']) {
      const staticHtml = sections.map(name => render(name, lang)).join('');
      const personalHtml = render('personal-panel-sections', lang);
      for (const width of [390, 1280]) {
        const page = await browser.newPage({viewport: {width, height: 900}}), errors = [];
        page.on('pageerror', error => errors.push(error.message));
        let allowed = true;
        await page.route('**/*', route => new URL(route.request().url()).pathname === '/academy/exports/access'
          ? route.fulfill({json: {success: true, data: {allowed}}})
          : route.fulfill({body: '', contentType: 'text/html'}));
        await page.goto('http://panel-export.test/');
        await page.setContent(`<html lang="${lang}" dir="${lang === 'fa' ? 'rtl' : 'ltr'}" data-mode="light" data-theme="emerald"><style>
          ${layoutCss()}html,body{margin:0;padding:0}body{min-height:100vh}.hidden{display:none!important}#fixtureActions{display:flex;gap:12px;padding:12px;overflow:hidden;max-width:100%;box-sizing:border-box}#staticSections,#personalSections{display:none}#fixture{min-height:130px}#edge{position:fixed;inset-inline-end:0;bottom:0}.existing-help{display:none}
          </style><main id="mainContent"><div id="staticSections">${staticHtml}</div><div id="personalSections">${personalHtml}</div><section id="fixture" class="section"><div id="fixtureActions">
          <button id="plus" type="button" onclick="openAddFixture()">افزودن</button>
          <button id="excel" type="button" onclick="exportFixtureToExcel()" aria-describedby="existingHelp"><i class="fas fa-file-excel"></i>خروجی اکسل</button>
          <button id="pdf" type="button" onclick="openFixturePDFOptionsModal()"><i class="fas fa-file-pdf"></i>خروجی PDF</button>
          <button id="edge" type="button" data-personal-print="fixture">خروجی PDF</button></div><span id="existingHelp" class="existing-help">Existing description</span>
          <div id="dynamic"></div><article><a id="articleExport" href="/download/report.pdf">Export PDF</a><button id="articleButton" onclick="exportStoryToExcel()">Export Excel</button></article>
          <a id="download" href="/media/download/9">Download PDF</a><form><button id="submit" type="submit">Export PDF</button></form>
          </section><section id="other" class="section hidden"></section></main><div id="modalContainer"><div role="dialog"><button id="generate" onclick="generateFixturePDF()">ایجاد PDF</button><button id="modalExport" type="button" onclick="exportModalToExcel()">خروجی اکسل</button></div></div></html>`);
        await page.addStyleTag({path: 'assets/theme/theme.css'});
        await page.evaluate(lang => {
          window.adminLocale = lang; window.adminUiMap = {'افزودن': 'Add'}; window.clicks = [];
          window.openAddFixture = () => clicks.push('add');
          window.exportFixtureToExcel = () => clicks.push('excel');
          window.openFixturePDFOptionsModal = () => clicks.push('pdf');
          document.getElementById('excel').addEventListener('click', () => clicks.push('listener'));
          document.getElementById('mainContent').addEventListener('click', event => { if (event.target.closest('[data-personal-print]')) clicks.push('personal'); });
        }, lang);
        await page.addScriptTag({path: 'assets/Analytics/js/admin.js'});
        await page.evaluate(() => watchPanelAddActions());

        for (const name of sections) {
          const host = page.locator('#staticSections #' + name);
          assert.ok(await host.locator('[data-panel-export-icon="excel"]').count() >= 1, name + ': actual Excel control');
          assert.ok(await host.locator('[data-panel-export-icon="pdf"]').count() >= 1, name + ': actual PDF control');
        }
        assert.equal(await page.locator('#personalSections [data-personal-export][data-panel-export-icon="excel"]').count(), 3);
        assert.equal(await page.locator('#personalSections [data-personal-print][data-panel-export-icon="pdf"]').count(), 3);
        for (const [id, kind] of [['excel', 'excel'], ['pdf', 'pdf'], ['edge', 'pdf'], ['modalExport', 'excel']]) {
          const button = page.locator('#' + id);
          assert.equal(await button.getAttribute('data-panel-export-icon'), kind);
          assert.equal(await button.getAttribute('aria-label'), label(lang, kind));
          assert.equal(await button.locator(':scope>svg[data-panel-export-symbol]').count(), 1);
          assert.equal(await button.locator(':scope>.panel-export-label').evaluate(element => getComputedStyle(element).width), '1px');
          assert.deepEqual(await button.evaluate(dimensions), [44, 44]);
          assert.equal(await button.getAttribute('title'), null, 'Normalization does not create competing browser titles');
        }
        assert.deepEqual(await page.locator('#plus').evaluate(dimensions), [44, 44]);
        for (const id of ['generate', 'submit', 'articleExport', 'articleButton', 'download']) assert.equal(await page.locator('#' + id).getAttribute('data-panel-export-icon'), null, id + ' keeps its existing meaning');
        // The mock dialog is open only for the checks above. Leaving it in normal flow
        // would move its export button under the pointer when the section is hidden.
        await page.locator('#modalContainer').evaluate(element => { element.hidden = true; });

        for (const [mode, excelColor, pdfColor] of [['light', 'rgb(21, 128, 61)', 'rgb(220, 38, 38)'], ['dark', 'rgb(74, 222, 128)', 'rgb(248, 113, 113)']]) {
          await page.evaluate(mode => document.documentElement.dataset.mode = mode, mode);
          await page.waitForFunction(({excelColor, pdfColor}) => getComputedStyle(document.querySelector('#excel>svg')).color === excelColor && getComputedStyle(document.querySelector('#pdf>svg')).color === pdfColor, {excelColor, pdfColor});
          await assertTooltip(page, page.locator('#excel'), label(lang, 'excel'));
          await page.keyboard.press('Escape');
          await visibleTooltip(page).waitFor({state: 'hidden'});
          assert.equal(await page.locator('#excel').getAttribute('aria-describedby'), 'existingHelp');
        }
        await page.mouse.move(width - 60, 500);
        await page.locator('#pdf').focus(); await visibleTooltip(page).waitFor();
        assert.equal(await visibleTooltip(page).textContent(), label(lang, 'pdf'));
        await page.locator('#pdf').blur(); await visibleTooltip(page).waitFor({state: 'hidden'});
        for (const event of ['scroll', 'resize']) {
          await assertTooltip(page, page.locator('#pdf'), label(lang, 'pdf'));
          await page.evaluate(event => window.dispatchEvent(new Event(event)), event);
          await visibleTooltip(page).waitFor({state: 'hidden'});
          await page.mouse.move(width - 60, 500);
        }
        await assertTooltip(page, page.locator('#excel'), label(lang, 'excel'));
        await page.mouse.move(width - 60, 500); await visibleTooltip(page).waitFor({state: 'hidden'});
        const extent = await page.evaluate(() => [document.documentElement.scrollWidth, document.documentElement.scrollHeight]);
        await assertTooltip(page, page.locator('#edge'), label(lang, 'pdf'));
        assert.deepEqual(await page.evaluate(() => [document.documentElement.scrollWidth, document.documentElement.scrollHeight]), extent, 'Popup must not add document scrolling');
        await page.locator('#edge').click(); await visibleTooltip(page).waitFor({state: 'hidden'});
        await page.locator('#excel').click(); await page.locator('#pdf').click();
        assert.deepEqual(await page.evaluate(() => clicks), ['personal', 'excel', 'listener', 'pdf'], 'Original handlers and delegated listeners survive');

        await page.evaluate(() => {
          const button = document.createElement('button'); button.type = 'button'; button.id = 'dynamicExport';
          button.dataset.personalExport = 'fixture'; button.textContent = 'Export Excel';
          button.addEventListener('click', () => clicks.push('dynamic')); document.getElementById('dynamic').append(button);
        });
        await page.locator('#dynamicExport[data-panel-export-icon="excel"]').waitFor();
        await page.locator('#dynamicExport').click(); assert.equal((await page.evaluate(() => clicks)).at(-1), 'dynamic');
        await page.addScriptTag({path: 'assets/Analytics/js/academy-export-access.js'});
        await page.evaluate(() => AcademyExports.refresh());
        assert.equal(await page.locator('#excel').getAttribute('aria-disabled'), 'false');
        allowed = false; await page.evaluate(() => AcademyExports.refresh());
        assert.equal(await page.locator('#excel').getAttribute('aria-disabled'), 'true');
        assert.equal(await page.locator('#excel').getAttribute('data-export-subscription-required'), '1');
        const lockedTitle = await page.locator('#excel').getAttribute('title');
        assert.ok(lockedTitle && lockedTitle.length > 30);
        await page.evaluate(() => normalizePanelActions(document.getElementById('mainContent')));
        assert.equal(await page.locator('#excel').getAttribute('title'), lockedTitle, 'Normalization preserves subscription status explanation');
        await assertTooltip(page, page.locator('#excel'), label(lang, 'excel'));
        assert.ok((await visibleTooltip(page).textContent()).includes(lockedTitle), 'Locked popup explains the existing export subscription rule');
        await page.evaluate(() => showSection('other')); await visibleTooltip(page).waitFor({state: 'hidden'});
        await page.evaluate(() => { document.getElementById('other').classList.add('hidden'); document.getElementById('fixture').classList.remove('hidden'); });
        allowed = true; await page.evaluate(() => AcademyExports.refresh());
        assert.equal(await page.locator('#excel').getAttribute('aria-disabled'), 'false');
        await page.mouse.move(width - 60, 500);
        await page.evaluate(() => {
          window.normalizationChanges = 0;
          new MutationObserver(records => normalizationChanges += records.length).observe(document.getElementById('mainContent'), {subtree: true, childList: true, attributes: true, characterData: true});
          for (let i = 0; i < 4; i++) normalizePanelActions(document.getElementById('mainContent'));
        });
        await page.waitForTimeout(60);
        assert.equal(await page.evaluate(() => normalizationChanges), 0, 'Repeated normalization is mutation-free');
        assert.equal(await page.locator('#excel>svg[data-panel-export-symbol]').count(), 1);
        assert.deepEqual(errors, []);
        await page.close();
      }
    }
    console.log('Panel export icons: 17 actual sections, personal/dynamic/modal controls, preserved handlers, 44px size, fa/en popup accessibility, light/dark colors, viewport clamping, no extra scrolling and subscription states passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
