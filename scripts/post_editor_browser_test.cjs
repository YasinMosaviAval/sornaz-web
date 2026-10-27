const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { renderPanelView, tableRuntime } = require('./panel-table-fixture.cjs');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    for (const language of ['fa', 'en']) {
      const page = await browser.newPage(), errors = [], writes = [], coverUploads = [];
      let failOptions = false;
      page.on('pageerror', error => errors.push(error.message));
      await page.route('**/*', async route => {
        const url = new URL(route.request().url());
        if (url.pathname === '/analytics/admin-media/upload-post-cover') {
          coverUploads.push(route.request().postDataBuffer());
          assert.equal(route.request().headers()['x-csrf-token'], 'fixture');
          await new Promise(resolve => setTimeout(resolve, 80));
          return route.fulfill({ json: { status: 200, data: { success: true, data: { id: 56, url: '/cover.svg?uploaded=1' } } } });
        }
        if (route.request().method() === 'POST') {
          const fields = new URLSearchParams(route.request().postData());
          writes.push(JSON.parse(Buffer.from(fields.get('payload_b64'), 'base64url').toString()));
          assert.equal(route.request().headers()['x-csrf-token'], 'fixture');
          await new Promise(resolve => setTimeout(resolve, 30));
          return route.fulfill({ json: { success: true, data: { id: 7 } } });
        }
        if (url.pathname === '/analytics/admin-post-categories') return route.fulfill({ json: { data: [{ id: 3, title_fa: 'Category A', title_en: 'Category A' }, { id: 4, title_fa: '<img onerror=alert(1)>', title_en: '<img onerror=alert(1)>' }] } });
        if (url.searchParams.has('editorOptions')) return route.fulfill(failOptions ? { status: 500, json: { success: false } } : { json: { data: { posts: [{ id: 7, title: 'Current post' }, { id: 8, title: 'Related A' }, { id: 9, title: 'Related B' }] } } });
        if (url.pathname === '/analytics/admin-posts/7') return route.fulfill({ json: { data: { id: 7, title: 'Existing', categories: '3', related_posts_id: '8', cover: '/cover.svg', cover_media_id: 55, status: 'published' } } });
        if (url.pathname === '/analytics/admin-posts') return route.fulfill({ json: { data: { posts: [], total: 0, page: 1, perPage: 20 } } });
        if (url.pathname === '/cover.svg') return route.fulfill({ contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="90"><rect width="160" height="90" fill="green"/></svg>' });
        return route.fulfill({ contentType: 'text/html', body: '<html></html>' });
      });
      await page.goto('http://editor.test/');
      await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style><header><div id="panelPageTitle"></div><div id="panelPageActions"></div><a href="/">Back</a></header><main id="mainContent">${renderPanelView('post-editor', language)}${renderPanelView('posts', language)}</main><div id="modalContainer"></div>${renderPanelView('panel-filters-template')}${tableRuntime()}</html>`);
      await page.evaluate(() => { window.adminCsrfToken = 'fixture'; window.alert = () => {}; window.closeModal = () => document.getElementById('modalContainer').replaceChildren(); });
      await page.addStyleTag({ path: 'assets/theme/theme.css' });
      await page.addScriptTag({ path: 'assets/Analytics/js/admin.js' });
      await page.addScriptTag({ path: 'assets/Analytics/js/posts.js' });
      await page.evaluate(() => editPost(7));
      await page.waitForSelector('#peRelated input[value="9"]');
      await page.evaluate(() => { document.documentElement.dataset.mode='light';peSetEditorMode('code'); });
      assert.deepEqual(await page.evaluate(() => [getComputedStyle(document.getElementById('peContent')).backgroundColor,getComputedStyle(document.getElementById('peVisualContent').parentElement).backgroundColor]),['rgb(255, 255, 255)','rgb(255, 255, 255)'],'HTML and visual editors share the light surface');
      await page.evaluate(() => { document.documentElement.dataset.mode='dark'; });
      await page.waitForFunction(()=>getComputedStyle(document.getElementById('peContent')).backgroundColor==='rgb(23, 32, 51)');
      assert.equal(await page.locator('#peContent').evaluate(element=>getComputedStyle(element).backgroundColor),'rgb(23, 32, 51)');
      await page.evaluate(() => { document.documentElement.dataset.mode='light';peSetEditorMode('visual'); });
      assert.equal(await page.locator('#post-editor h1').count(), 0);
      assert.equal(await page.locator('#panelPageActions [data-panel-header-action="post-editor"]').count(), 0);
      assert.equal(await page.locator('#post-editor [onclick="closePostEditor()"]').isVisible(), true);
      assert.equal(await page.locator('#peCover').getAttribute('type'), 'file');
      assert.equal(await page.locator('#peCover').getAttribute('accept'), '.png,.jpg,.webp');
      assert.equal(await page.locator('#post-editor [onclick^="savePostEditor"]').count(), 2);
      assert.equal(await page.locator('#post-editor [onclick="previewPostEditor()"]').count(), 1);
      assert.equal(await page.locator('#peTrashBtn, #peCoverMediaId').count(), 0);
      assert.equal(await page.locator('#peSummary').getAttribute('rows'), '5');
      assert.equal(await page.locator('#peDescription').getAttribute('rows'), '10');
      assert.equal(await page.locator('#peCategories img').count(), 0);
      assert.equal(await page.locator('#peRelated input[value="7"]').count(), 0);
      assert.equal(await page.locator('#peCategories input[value="3"]').isChecked(), true);
      assert.equal(await page.locator('#peRelated input[value="8"]').isChecked(), true);
      await page.locator('#peCategories input[value="4"]').check();
      await page.locator('#peRelated input[value="9"]').check();
      await page.locator('#peRelatedSearch').fill('Related B');
      assert.equal(await page.locator('#peRelated input').count(), 1);
      await page.locator('#peStatus').selectOption('pending');
      await page.locator('#peTitle').fill('Unsaved title');
      await page.evaluate(() => { document.getElementById('peVisualContent').innerHTML = '<p>Unsaved body</p><script>parent.previewEscaped = true</script>'; previewPostEditor(); });
      assert.equal(await page.locator('#postPreviewFrame').getAttribute('sandbox'), '');
      assert.ok((await page.locator('#postPreviewFrame').getAttribute('srcdoc')).includes('Unsaved title'));
      assert.equal(await page.evaluate(() => window.previewEscaped), undefined);
      await page.locator('#modalContainer button[onclick="closeModal()"]').first().click();
      assert.equal(await page.locator('#peTitle').inputValue(),'Unsaved title','Returning from preview preserves the current unsaved editor');
      await page.waitForFunction(() => document.getElementById('peCoverImg').naturalWidth > 0);
      assert.equal(await page.locator('#peCoverImg').isVisible(), true);
      await page.evaluate(async () => { const save = savePostEditor('status'); savePostEditor('status'); await save; });
      assert.equal(writes.length, 1);
      assert.equal(writes[0].status, 'pending');
      assert.equal(writes[0].categories, '3,4');
      assert.equal(writes[0].related_posts_id, '8,9');
      assert.equal(writes[0].cover_media_id, 55);
      failOptions = true;
      await page.evaluate(() => editPost(7));
      await page.waitForFunction(() => !!document.querySelector('#peRelated button'));
      await page.locator('#peCover').setInputFiles({ name: 'wrong.gif', mimeType: 'image/gif', buffer: Buffer.from('GIF89a') });
      assert.equal(coverUploads.length, 0);
      await page.locator('#peCover').setInputFiles({ name: 'chosen.png', mimeType: 'image/png', buffer: Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aY9sAAAAASUVORK5CYII=', 'base64') });
      await page.locator('#peStatus').selectOption('published');
      await page.evaluate(() => savePostEditor('draft'));
      assert.equal(writes[1].status, 'draft');
      assert.equal(writes[1].related_posts_id, '8', 'Failed options must preserve existing relationships');
      assert.equal(writes[1].cover_media_id, 56);
      assert.equal(writes[1].cover, '/cover.svg?uploaded=1');
      assert.equal(coverUploads.length, 1);
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Post editor: status/draft saves, multiselect labels, image preview, legacy metadata preservation, safe unsaved preview, failed options and header action passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
