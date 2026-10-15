const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { tableRuntime, renderPanelView } = require('./panel-table-fixture.cjs');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    const config = {
      attributes: 'id="sample" data-no-identity-column class="w-full"',
      columns: [{ value: 'نام <آزمون>' }, { value: 'عملیات' }],
      rows: [
        { cells: [{ value: '<img src=x onerror=alert(1)>' }, { html: '<button onclick="window.clicked=(window.clicked||0)+1">ویرایش</button>' }] },
        { attributes: 'class="expanded"', cells: [{ value: 'جزئیات', attributes: 'colspan="2"' }] },
      ],
    };
    const phpTable = execFileSync('php', ['scripts/panel-table-view-fixture.php', 'data', 'fa', JSON.stringify(config)], { encoding: 'utf8' });
    await page.setContent(`<main id="mainContent"><section id="fixture" class="section"><div class="overflow-hidden bg-white shadow"><div class="overflow-x-auto">${phpTable}</div></div></section></main>${renderPanelView('panel-table-template')}${tableRuntime()}`);
    assert.equal(await page.locator('#sample th').count(), 2);
    assert.equal(await page.locator('#sample tbody tr').count(), 2);
    assert.equal(await page.locator('#sample img').count(), 0);
    assert.equal(await page.locator('#sample td').first().textContent(), '<img src=x onerror=alert(1)>');
    assert.equal(await page.locator('#sample .expanded td').getAttribute('colspan'), '2');
    await page.getByRole('button', { name: 'ویرایش' }).click();
    assert.equal(await page.evaluate(() => window.clicked), 1);
    await page.evaluate((data) => AdminPanelTable.render('#sample', data), config);
    assert.equal(await page.locator('#sample th').count(), 2);
    assert.equal(await page.locator('#sample td').first().textContent(), '<img src=x onerror=alert(1)>');
    await page.getByRole('button', { name: 'ویرایش' }).click();
    assert.equal(await page.evaluate(() => window.clicked), 2);
    await page.evaluate(() => {
      const row = AdminPanelTable.createRow('data-id="7"');
      row.innerHTML = AdminPanelTable.cellsHTML([{ value: 'هفت' }, { fragment: AdminPanelTable.cellsHTML([{ value: 'اختیاری' }]) }]);
      document.querySelector('#sample tbody').appendChild(row);
    });
    assert.equal(await page.locator('#sample [data-id="7"] td').count(), 2);
    for (const locale of ['fa', 'en']) {
      for (const view of ['students', 'courses', 'terms', 'classrooms', 'teachers', 'branches', 'users', 'lessons', 'classroom-types', 'roles', 'permissions', 'personal-panel-sections']) {
        const html = renderPanelView(view, locale);
        await page.setContent(html);
        assert(await page.locator('table[data-panel-table="1"]').count(), `${view}/${locale} renders shared tables`);
        assert(!(await page.locator('body').textContent()).includes('panelTable0'), `${view}/${locale} contains no unrendered PHP`);
        if (view === 'personal-panel-sections') {
          assert.equal(await page.locator('table').count(), 5);
          assert.equal(await page.locator('table tbody tr').count(), 5);
          assert((await page.locator('table').allTextContents()).join(' ').includes('پیانو'));
        }
      }
    }
    assert.deepEqual(errors, []);
    console.log('Shared panel table: PHP/JS rendering, fa/en views, escaped values, actions, optional cells, expansion and repeated renders passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
