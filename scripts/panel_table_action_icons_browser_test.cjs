const assert = require('node:assert/strict');
const fs = require('node:fs');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {tableRuntime} = require('./panel-table-fixture.cjs');

const css = fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php', 'utf8').match(/<style>([\s\S]*?)<\/style>/)[1];
const labels = lang => lang === 'en'
  ? {details: 'Details', edit: 'Edit', preview: 'Preview', trash: 'Trash', permanent: 'Delete permanently', restricted: 'Delete', restriction: 'Category contains posts'}
  : {details: 'جزئیات', edit: 'ویرایش', preview: 'پیش‌نمایش', trash: 'زباله‌دان', permanent: 'حذف دائمی', restricted: 'حذف', restriction: 'دسته‌بندی دارای نوشته است'};
const tooltip = page => page.locator('#panelActionTooltip:visible');
const actionKind = button => button.getAttribute('data-panel-table-action');

async function expectTooltip(page, button, text) {
  await page.mouse.move(180, 600);
  await button.hover();
  await tooltip(page).waitFor();
  assert.equal(await tooltip(page).getAttribute('role'), 'tooltip');
  assert.ok((await tooltip(page).textContent()).includes(text));
  assert.equal(await tooltip(page).evaluate(element => element.parentElement === document.body), true);
  assert.equal(await tooltip(page).evaluate(element => { const r = element.getBoundingClientRect(); return r.left >= 0 && r.top >= 0 && r.right <= innerWidth && r.bottom <= innerHeight; }), true);
}

(async () => {
  const browser = await chromium.launch({channel: 'msedge', headless: true});
  try {
    for (const lang of ['fa', 'en']) for (const width of [390, 1280]) {
      const page = await browser.newPage({viewport: {width, height: 900}}), errors = [], text = labels(lang);
      page.on('pageerror', error => errors.push(error.message));
      await page.route('**/*', route => route.fulfill({body: '', contentType: 'text/html'}));
      await page.goto('http://panel-actions.test/');
      await page.setContent(`<html lang="${lang}" dir="${lang === 'en' ? 'ltr' : 'rtl'}" data-mode="light" data-theme="emerald"><style>${css}
        html,body{margin:0;padding:0}body{min-height:100vh}.hidden{display:none!important}#fixtureWrap{padding:12px;overflow:hidden}#fixture table{border-collapse:collapse}#fixture td{padding:4px}#realTables{display:none}.help{display:none}#edge{position:fixed;inset-inline-end:0;bottom:0}
        </style><main id="mainContent"><section class="section" id="fixture"><div id="fixtureWrap"><table><tbody><tr><td>
        <button id="details" type="button" onclick="fixtureAction('details')" aria-describedby="existingHelp">${text.details}</button>
        <button id="edit" type="button" data-term-action="inline-edit" data-term-id="12" aria-expanded="false" aria-controls="inlineRow" onclick="fixtureAction('edit')">${text.edit}</button>
        <button id="preview" type="button" onclick="fixtureAction('preview')">${text.preview}</button>
        <button id="trash" type="button" onclick="fixtureAction('trash')">${text.trash}</button>
        <button id="permanent" type="button" onclick="fixtureAction('permanent')">${text.permanent}</button>
        <button id="restricted" type="button" title="${text.restriction}" class="opacity-40">${text.restricted}</button>
        <a id="editLink" href="#fixture" onclick="fixtureAction('link');return false">${text.edit}</a>
        <button id="iconInfo" type="button" aria-label="${text.details}"><i class="fas fa-info-circle"></i></button>
        <button id="edge" type="button">${text.details}</button>
        <button id="restore" type="button">${lang === 'en' ? 'Restore' : 'بازیابی'}</button>
        <button id="status" type="button" onclick="fixtureAction('status')">${lang === 'en' ? 'Active' : 'فعال'}</button>
        <button id="statusTrash" type="button" onclick="cyclePostStatus(12,this)">${text.trash}</button>
        <button id="mediaTitle" type="button" onclick="openMediaDetails(12)"><img alt="" src="/fixture.png"><strong>${lang === 'en' ? 'Delete image' : 'ویرایش تصویر'}</strong><small>photo.png</small></button>
        <a id="postTitle" data-post-title href="#post-editor">Edit this title</a>
        <a id="download" href="/media/9">Download PDF</a><input type="checkbox" id="selection">
        <form><button id="save" type="submit">${text.edit}</button><button id="deleteField" type="button">${text.restricted}</button></form>
        </td></tr><tr id="inlineRow" class="admin-inline-expand" data-admin-inline-expand><td><button id="inlineDelete" type="button">${text.restricted}</button></td></tr></tbody></table></div>
        <span id="existingHelp" class="help">Existing help</span><div id="dynamic"></div>
        <article><table><tr><td><button id="articleEdit" type="button">${text.edit}</button></td></tr></table></article>
        <button id="outsideEdit" type="button">${text.edit}</button></section><section id="other" class="section hidden"></section><div id="realTables"></div></main>
        <div id="modalContainer"><div role="dialog"><button id="confirmDelete" type="button">${text.restricted}</button><table><tr><td><button id="modalDetails" type="button">${text.details}</button></td></tr></table></div></div>${tableRuntime()}</html>`);
      await page.addStyleTag({path: 'assets/theme/theme.css'});
      await page.evaluate(lang => {
        window.adminLocale = lang; window.adminUiMap = {}; window.clicks = [];
        window.fixtureAction = action => clicks.push(action);
        document.getElementById('edit').addEventListener('click', () => clicks.push('listener'));
        document.getElementById('mainContent').addEventListener('click', event => { if (event.target.closest('[data-term-action="inline-edit"]')) clicks.push('delegated'); });
        window.getBranchPhysicalTypeLabel = () => 'In person'; window.branchSiteAdmin = false;
      }, lang);
      for (const script of ['branch-templates', 'term-templates', 'course-templates', 'user-templates']) await page.addScriptTag({path: `assets/Analytics/js/${script}.js`});
      await page.evaluate(() => {
        const branch = {id: 21, name: 'Branch', type: 'Music', status: 'فعال', manager: 'Manager'};
        const term = {id: 22, name: 'Term', branchName: 'Branch', status: 'فعال', status_code: 'ongoing', students: []};
        const course = {id: 23, name: 'Course', branchName: 'Branch', teacher_capacity: 2, student_capacity: 5, status: 'فعال'};
        const user = {id: 24, name: 'User', phone: 'Fixture', userTypeLabel: 'Student', roleTitle: 'Member', branchName: 'Branch', status: 'فعال', permissions: []};
        const rows = [['branch', getBranchTableRowHTML(branch)], ['term', '<tr>' + getTermRowHTML(term, 'bg-green-100') + '</tr>'], ['course', '<tr>' + getCourseRowHTML(course, 'bg-green-100') + '</tr>'], ['user', '<tr>' + getUserRowHTML(user) + '</tr>']];
        document.getElementById('realTables').innerHTML = rows.map(([name, row]) => '<table id="real-' + name + '"><tbody>' + row + '</tbody></table>').join('');
      });
      await page.addScriptTag({path: 'assets/Analytics/js/admin.js'});
      await page.evaluate(() => watchPanelAddActions());
      for (const name of ['branch', 'term', 'course', 'user']) {
        const table = page.locator('#real-' + name);
        for (const kind of ['details', 'edit', 'delete']) assert.equal(await table.locator(`[data-panel-table-action="${kind}"]`).count(), 1, name + ' actual renderer: ' + kind);
      }
      for (const [id, kind, name] of [['details', 'details', text.details], ['edit', 'edit', text.edit], ['preview', 'preview', text.preview], ['trash', 'delete', text.trash], ['permanent', 'delete', text.permanent], ['restricted', 'delete', text.restricted], ['editLink', 'edit', text.edit], ['iconInfo', 'details', text.details], ['modalDetails', 'details', text.details]]) {
        const button = page.locator('#' + id);
        assert.equal(await actionKind(button), kind, id);
        assert.equal(await button.getAttribute('aria-label'), name, id + ' keeps a meaningful accessible name');
        assert.equal(await button.locator(':scope>svg[data-panel-table-action-symbol]').count(), 1);
        assert.deepEqual(await button.evaluate(element => { const r = element.getBoundingClientRect(); return [r.width, r.height]; }), [32, 32]);
        assert.equal(await button.locator(':scope>.panel-table-action-label').evaluate(element => getComputedStyle(element).width), '1px');
      }
      for (const id of ['restore', 'status', 'statusTrash', 'mediaTitle', 'postTitle', 'download', 'save', 'deleteField', 'inlineDelete', 'articleEdit', 'outsideEdit', 'confirmDelete']) assert.equal(await actionKind(page.locator('#' + id)), null, id + ' remains unchanged');
      assert.equal(await page.locator('#edit').getAttribute('aria-expanded'), 'false');
      assert.equal(await page.locator('#edit').getAttribute('aria-controls'), 'inlineRow');
      await page.locator('#edit').click();
      assert.deepEqual(await page.evaluate(() => clicks), ['edit', 'listener', 'delegated']);
      await page.locator('#editLink').click(); assert.equal((await page.evaluate(() => clicks)).at(-1), 'link');

      const extent = await page.evaluate(() => [document.documentElement.scrollWidth, document.documentElement.scrollHeight]);
      for (const mode of ['light', 'dark']) {
        await page.evaluate(mode => document.documentElement.dataset.mode = mode, mode);
        await expectTooltip(page, page.locator('#details'), text.details);
        assert.equal(await page.locator('#details').getAttribute('aria-describedby'), 'existingHelp panelActionTooltip');
        await page.keyboard.press('Escape'); await tooltip(page).waitFor({state: 'hidden'});
        assert.equal(await page.locator('#details').getAttribute('aria-describedby'), 'existingHelp');
      }
      await page.mouse.move(180, 600); await page.locator('#preview').focus(); await tooltip(page).waitFor();
      assert.equal(await tooltip(page).textContent(), text.preview);
      await page.locator('#preview').blur(); await tooltip(page).waitFor({state: 'hidden'});
      await expectTooltip(page, page.locator('#restricted'), text.restriction);
      assert.equal(await page.locator('#restricted').getAttribute('title'), text.restriction);
      await expectTooltip(page, page.locator('#edge'), text.details);
      assert.deepEqual(await page.evaluate(() => [document.documentElement.scrollWidth, document.documentElement.scrollHeight]), extent, 'Popup cannot create document overflow');
      await page.mouse.move(180, 600); await tooltip(page).waitFor({state: 'hidden'});

      await page.evaluate(text => {
        document.getElementById('dynamic').innerHTML = '<table><tbody><tr><td><button type="button" id="dynamicEdit" data-course-action="inline-edit" data-no-inline-edit>' + text.edit + '</button></td></tr></tbody></table>';
        document.getElementById('dynamicEdit').addEventListener('click', () => clicks.push('dynamic'));
      }, text);
      await page.locator('#dynamicEdit[data-panel-table-action="edit"]').waitFor();
      await page.locator('#dynamicEdit').click(); assert.equal((await page.evaluate(() => clicks)).at(-1), 'dynamic');
      await page.evaluate(text => document.querySelector('#trash>.panel-table-action-label').textContent = text.permanent, text);
      await page.waitForFunction(expected => document.getElementById('trash').getAttribute('aria-label') === expected, text.permanent);
      await expectTooltip(page, page.locator('#trash'), text.permanent);
      await page.evaluate(() => showSection('other')); await tooltip(page).waitFor({state: 'hidden'});
      await page.evaluate(() => { document.getElementById('other').classList.add('hidden'); document.getElementById('fixture').classList.remove('hidden'); });
      await expectTooltip(page, page.locator('#dynamicEdit'), text.edit);
      await page.evaluate(() => document.getElementById('dynamic').replaceChildren()); await tooltip(page).waitFor({state: 'hidden'});
      await page.mouse.move(180, 600);
      await page.evaluate(() => {
        window.normalizationChanges = 0;
        new MutationObserver(records => normalizationChanges += records.length).observe(document.getElementById('mainContent'), {subtree: true, childList: true, attributes: true, characterData: true});
        for (let i = 0; i < 4; i++) normalizePanelActions(document.getElementById('mainContent'));
      });
      await page.waitForTimeout(70); assert.equal(await page.evaluate(() => normalizationChanges), 0, 'Repeated normalization must not mutate');
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Panel table action icons: actual branch/term/course/user rows, static/dynamic/modal/delegated actions, preserved state and handlers, exclusions, 32px icons, fa/en mobile/desktop popups, light/dark, no overflow, restriction help and stable normalization passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
