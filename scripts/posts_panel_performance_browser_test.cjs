const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { renderPanelView, tableRuntime } = require('./panel-table-fixture.cjs');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    for (const language of ['fa', 'en']) {
      const page = await browser.newPage(), errors = [], requests = [];
      page.on('pageerror', e => errors.push(e.message));
      await page.route('**/*', route => route.fulfill({ contentType: 'text/html', body: '<html></html>' }));
      await page.route('**/analytics/admin-post*', async route => {
        const url = new URL(route.request().url());
        requests.push(url.href);
        if (url.pathname.includes('categories')) return route.fulfill({ json: { data: [{ id: 8, title_fa: 'Category', slug: 'category', posts_count: 1 }] } });
        const status = url.searchParams.get('status') || 'all';
        await new Promise(resolve => setTimeout(resolve, status === 'draft' ? 180 : 20));
        return route.fulfill({ json: { data: { posts: [{ id: 7, title: status, slug: status, status: 'published', type: 'post' }], total: 40, page: Number(url.searchParams.get('page')), perPage: 20, statusCounts: { all: 40 } } } });
      });
      await page.goto('http://panel.test/');
      await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style><div id="panelPageTitle"></div><main id="mainContent">${renderPanelView('posts', language)}${renderPanelView('post-categories', language)}</main>${renderPanelView('panel-filters-template', language)}${tableRuntime()}</html>`);
      await page.addStyleTag({ path: 'assets/theme/theme.css' });
      await page.addScriptTag({ path: 'assets/Analytics/js/posts.js' });
      await page.addScriptTag({ path: 'assets/Analytics/js/post-categories.js' });
      await page.addScriptTag({ path: 'assets/Analytics/js/admin.js' });
      await page.evaluate(() => { showSection('posts'); loadPosts(); loadPosts(); });
      await page.waitForSelector('#postsTable [data-admin-identity-cell]');
      await page.waitForSelector('#postCategoryFilter option[value="8"]', {state:'attached'});
      assert.equal(requests.filter(value => new URL(value).pathname === '/analytics/admin-posts').length, 1, 'Concurrent post loads must share one request');
      assert.equal(requests.filter(value => new URL(value).pathname === '/analytics/admin-post-categories').length, 1, 'Category filter options load once independently');
      assert.equal(await page.locator('#posts h1').count(), 0);
      assert.equal(await page.locator('#panelPageTitle').textContent(), language === 'en' ? 'Posts' : 'نوشته ها');
      await page.evaluate(() => { window.originalRow = document.querySelector('#postsTable tbody tr'); });
      await page.evaluate(() => loadPosts());
      assert.equal(await page.evaluate(() => originalRow === document.querySelector('#postsTable tbody tr')), true, 'Unchanged results must preserve row nodes');
      await page.evaluate(() => { filterPostsByStatus('draft'); filterPostsByStatus('published'); });
      await page.waitForTimeout(250);
      assert.ok((await page.locator('#postsTable tbody').textContent()).includes('published'));
      assert.equal(await page.locator('[data-status="published"]').evaluate(el => el.classList.contains('bg-indigo-600') && !el.classList.contains('bg-gray-900')), true);
      const count = requests.length;
      await page.evaluate(() => filterPostsByStatus('all'));
      assert.equal(requests.length, count, 'Revisiting a recent filter must use memory');
      await page.evaluate(() => document.documentElement.style.setProperty('--theme-600', '#008866'));
      assert.equal(await page.locator('[data-status="all"]').evaluate(el => getComputedStyle(el).backgroundColor), 'rgb(0, 136, 102)');
      await page.evaluate(() => changePostsPage(2));
      await page.waitForTimeout(60);
      const paged = requests.length;
      await page.evaluate(() => changePostsPage(1));
      assert.equal(requests.length, paged, 'Recent pages must be cached');
      await page.evaluate(() => window.dispatchEvent(new CustomEvent('sornaz:data-mutated', { detail: { url: '/analytics/admin-posts/7/update' } })));
      await page.evaluate(() => loadPosts());
      assert.equal(requests.length, paged + 1, 'Writes must invalidate cached lists');
      await page.evaluate(() => showSection('post-categories'));
      await page.waitForSelector('#postCategoriesTable [data-admin-identity-cell]');
      assert.equal(await page.locator('#post-categories h1').count(), 0);
      assert.equal(await page.locator('#panelPageTitle').textContent(), language === 'en' ? 'Posts' : 'نوشته ها');
      const categoriesCount = requests.length;
      await page.evaluate(() => loadPostCategories());
      assert.equal(requests.length, categoriesCount);
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Posts panel: localized headers, stable identity columns, deduplicated loads, cached filters and stale-response isolation passed.');
  } finally { await browser.close(); }
})().catch(e => { console.error(e); process.exitCode = 1; });
