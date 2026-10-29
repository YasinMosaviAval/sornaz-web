const assert = require('node:assert/strict');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { renderPanelView, tableRuntime } = require('./panel-table-fixture.cjs');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    for (const language of ['fa', 'en']) {
      const page = await browser.newPage(), errors = [];
      page.on('pageerror', e => errors.push(e.message));
      await page.route('**/*', route => {
        const path = new URL(route.request().url()).pathname;
        if (path === '/analytics/admin-reports') return route.fulfill({ json: { success: true, data: { branches: [], reports: Array.from({ length: 12 }, (_, i) => ({ id: i + 1, title: 'Report ' + (i + 1), branchName: 'Branch', type: 'Finance', status: 'Ready', dateISO: '2026-10-01' })) } } });
        return route.fulfill({ contentType: 'text/html', body: '<html></html>' });
      });
      await page.goto('http://panel.test/');
      const sections = ['posts', 'post-categories', 'media', 'comments', 'chart-gallery', 'reports'];
      const extraTemplates = ['panel-filters-template', 'panel-page-header-template', 'panel-organization-tabs-template'].map(key => renderPanelView(key, language)).join('');
      const shell = fs.readFileSync('Modules/Analytics/Resources/Views/sections/panel-table-template.php', 'utf8').match(/<template id="adminTableTemplate">[\s\S]*?<\/template>/)[0];
      await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style><header><div id="panelPageTitle"></div><div id="panelPageActions"></div><a href="/">Back</a></header><div id="modalContainer"></div><main id="mainContent">${sections.map(key => renderPanelView(key, language)).join('')}</main>${extraTemplates}${shell}${tableRuntime()}</html>`);
      await page.addScriptTag({ path: 'assets/Analytics/js/admin.js' });
      await page.evaluate(() => {
        window.openPostEditor = () => { window.action = 'posts'; };
        window.openPostCategoryForm = () => { window.action = 'post-categories'; };
        window.openMediaUpload = () => { window.action = 'media'; };
      });
      const layout = fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php', 'utf8');
      await page.addStyleTag({content: layout.split('\n').filter(line => line.includes('.panel-page-')).join('\n')});
      await page.evaluate(language => document.documentElement.dir = language === 'fa' ? 'rtl' : 'ltr', language);
      for (const key of ['posts', 'post-categories', 'media']) {
        await page.evaluate(key => showSection(key), key);
        const active = page.locator(`#${key} [data-panel-page-action="${key}"]`);
        assert.equal(await active.isVisible(), true);
        assert.equal(await page.locator('#panelPageActions').isVisible(), false);
        assert.equal(await page.locator('#panelPageActions button').count(), 0);
        assert.equal(await active.evaluate(button => !!button.closest('[data-panel-filter-actions] [data-panel-page-actions]')), true);
        const tab = await page.locator(`#${key} .panel-page-tabs button`).first().boundingBox();
        const action = await active.boundingBox();
        assert.ok(action.y >= tab.y + tab.height, 'Actions belong below the internal navigation');
        const row = await page.locator(`#${key} [data-panel-filter-actions]`).boundingBox();
        assert.ok(language === 'fa' ? action.x < row.x + row.width / 2 : action.x > row.x + row.width / 2, 'Actions stay at the opposite end when organization filters are absent');
        await active.click();
        assert.equal(await page.evaluate(() => window.action), key);
      }
      for (const key of ['posts', 'post-categories', 'media', 'comments']) {
        assert.deepEqual(await page.locator('#' + key + ' .panel-page-tabs button').evaluateAll(nodes => nodes.map(node => node.getAttribute('onclick'))), ['post-categories', 'posts', 'media', 'comments'].map(key => 'showSection("' + key + '")'));
      }
      await page.route('**/analytics/admin-media?*', route => route.fulfill({ json: { data: { items: [{id:2,title:'Used image',url:'/used.png',type:'image',filename:'used.png',mime:'image/png',size:12}], total:1,page:1,perPage:20,months:[] } } }));
      await page.route('**/analytics/admin-media/upload', route => route.fulfill({ json: { success:true,data:{id:3,url:'/new.png'} } }));
      await page.addScriptTag({ path: 'assets/Analytics/js/media.js' });
      await page.evaluate(() => loadAdminMedia(1));
      assert.equal(await page.locator('#mediaGrid img').count(), 1);
      await page.evaluate(() => uploadMediaFiles([new File(['image'], 'new.png', {type:'image/png'})]));
      assert.equal(await page.locator('#modalContainer input[readonly]').getAttribute('value'), '/new.png');
      await page.evaluate(() => document.getElementById('modalContainer').replaceChildren());
      for (const key of sections) assert.equal(await page.locator(`#${key} h1`).count(), 0);
      await page.evaluate(() => showSection('comments'));
      assert.equal(await page.locator('#panelPageActions').isVisible(), false);
      await page.addStyleTag({ path: 'assets/theme/theme.css' });
      await page.evaluate(() => document.documentElement.style.setProperty('--theme-600', '#008866'));
      await page.route('**/analytics/admin-comments?*', route => route.fulfill({ json: { data: { comments: [{id:17,author:'Author',content:'Comment body',status:'pending',postTitle:'Post',createdAt:'2026-10-23'}], total: 1, page: 1, perPage: 20, statusCounts: {}, posts: [] } } }));
      await page.addScriptTag({ path: 'assets/Analytics/js/comments.js' });
      assert.equal(await page.locator('#commentPostFilter').count(), 0);
      await page.evaluate(() => filterCommentsByStatus('pending'));
      await page.waitForSelector('#commentsCards article', { state: 'attached' });
      await page.evaluate(() => setCommentsView('cards'));
      assert.equal(await page.locator('#commentsCards').isVisible(), true);
      assert.equal(await page.locator('#commentsCards [onclick="editComment(17)"]').count(), 1);
      assert.equal(await page.locator('#commentsTableWrap').isVisible(), false);
      assert.equal(await page.locator('#commentsCardViewButton').getAttribute('aria-pressed'), 'true');
      await page.evaluate(() => setCommentsView('table'));
      for (const status of ['pending', 'approved', 'rejected', 'all']) {
        await page.evaluate(status => filterCommentsByStatus(status), status);
        assert.equal(await page.locator('[data-comment-status="' + status + '"]').evaluate(el => getComputedStyle(el).backgroundColor), 'rgb(0, 136, 102)');
        assert.equal(await page.locator('.comment-status-tab.bg-indigo-600').count(), 1);
      }
      await page.addScriptTag({ path: 'assets/Analytics/js/report-templates.js' });
      await page.addScriptTag({ path: 'assets/Analytics/js/reports.js' });
      await page.waitForFunction(() => document.querySelector('#reportsTable tbody')?.textContent.includes('Report 1'));
      assert.equal(await page.locator('#reportsTable tbody tr').count(), 10);
      await page.evaluate(() => changeReportsPage(2));
      assert.equal(await page.locator('#reportsTable tbody tr').count(), 2);
      assert.equal(await page.locator('#reportsTable').evaluate(table => !!table.closest('.admin-ui-table')), true);
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Content panel: shared tables, empty states, report pagination, in-page actions and removed internal headings passed.');
  } finally { await browser.close(); }
})().catch(e => { console.error(e); process.exitCode = 1; });
