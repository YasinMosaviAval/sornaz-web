const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { renderPanelView, tableRuntime } = require('./panel-table-fixture.cjs');
const copy = value => JSON.parse(JSON.stringify(value));
const originalBody = '<p class="keep" data-x="original">Original <em>article</em> &amp; text</p>\n<p>Second paragraph.</p>';
const png = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aY9sAAAAASUVORK5CYII=', 'base64');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    for (const language of ['fa', 'en']) for (const width of [1280, 390]) {
      const page = await browser.newPage({ viewport: { width, height: 900 } });
      page.setDefaultTimeout(15000);
      const errors = [], writes = [], uploads = [], listRequests = [];
      let failSave = false, failOptions = false, failGet = false, holdGet = false, holdUpload = false;
      let failCategories = false, categoryLoads = 0;
      const categories = [{ id: 3, title_fa: 'Category A', title_en: 'Category A' }, { id: 4, title_fa: '<svg onload="window.badCategory=1">', title_en: '<svg onload="window.badCategory=1">' }];
      let releaseGet, releaseUpload, notifyGet, notifyUpload;
      const records = Array.from({ length: 12 }, (_, index) => ({
        id: 7 + index, title: index ? 'Article ' + (7 + index) : 'Add rhythm <img src=x onerror="window.badTitle=1">',
        slug: 'article-' + (7 + index), content: originalBody, summary: 'Original summary', description: 'Original description',
        status: index === 1 ? 'request-confirmed' : 'published', visibility: 'followers', visibility_user_id: 72,
        type: 'post', author_id: 1, author_name: 'Author', categories: index === 1 ? '4,99' : '3,99', category_names: ['Category A'],
        related_posts_id: '8,199', cover: '/cover.svg?id=' + (7 + index), cover_media_id: 70 + index,
        password: 'original-password', guid: 'https://example.test/original/' + (7 + index),
        published_at: '2026-10-01 12:34:56', created_at: '2026-09-01 10:00:00', views_count: 42, comment_count: 3,
      }));
      const reply = data => ({ json: { status: 200, data: { success: true, data } } });
      page.on('pageerror', error => errors.push(error.message));
      await page.route('**/*', async route => {
        const request = route.request(), url = new URL(request.url());
        if (url.pathname === '/analytics/admin-media/upload-post-cover') {
          uploads.push(request.postDataBuffer()); assert.equal(request.headers()['x-csrf-token'], 'fixture');
          if (holdUpload) await new Promise(resolve => { releaseUpload = resolve; notifyUpload?.(); });
          return route.fulfill(reply({ id: 777, url: '/cover.svg?uploaded=1' }));
        }
        if (request.method() === 'POST') {
          const fields = new URLSearchParams(request.postData()), data = JSON.parse(Buffer.from(fields.get('payload_b64'), 'base64url').toString());
          assert.equal(request.headers()['x-csrf-token'], 'fixture'); assert.equal(fields.get('_token'), 'fixture');
          assert.match(url.pathname, /^\/analytics\/admin-posts\/\d+\/metadata$/, 'Inline writes must use the metadata-only endpoint');
          for (const key of ['content', 'summary', 'description']) assert.equal(Object.hasOwn(data, key), false, key + ' must not enter metadata writes');
          const id = Number(url.pathname.split('/')[3]); writes.push({ id, data: copy(data), failed: failSave });
          await new Promise(resolve => setTimeout(resolve, 35));
          if (failSave) return route.fulfill({ status: 422, json: { success: false, message: 'Fixture save failure' } });
          const { visibility_user_id, ...metadata } = data; Object.assign(records.find(item => item.id === id), metadata);
          return route.fulfill(reply({}));
        }
        if (url.pathname === '/analytics/admin-post-categories') { categoryLoads++; return route.fulfill(failCategories ? {status:500,json:{success:false,message:'Category failure'}} : reply(copy(categories))); }
        if (url.searchParams.has('editorOptions')) return route.fulfill(failOptions ? { status: 500, json: { success: false, message: 'Fixture choices failure' } } : reply({ posts: records.map(item => ({ id: item.id, title: item.title })) }));
        const match = /^\/analytics\/admin-posts\/(\d+)$/.exec(url.pathname);
        if (match) {
          if (failGet) return route.fulfill({ status: 500, json: { success: false, message: 'Fixture detail failure' } });
          const id = Number(match[1]); if (holdGet && id === 7) await new Promise(resolve => { releaseGet = resolve; notifyGet?.(); });
          return route.fulfill(reply(copy(records.find(item => item.id === id))));
        }
        if (url.pathname === '/analytics/admin-posts') {
          listRequests.push(url);
          const current = Math.max(1, Number(url.searchParams.get('page')) || 1);
          const selected = url.searchParams.get('category');
          const filtered = selected ? records.filter(item => item.categories.split(',').includes(selected)) : records;
          return route.fulfill(reply({ posts: filtered.slice((current - 1) * 10, current * 10).map(copy), total: filtered.length, page: current, perPage: 10, statusCounts: { published: 11, 'request-confirmed': 1 } }));
        }
        if (url.pathname === '/cover.svg') return route.fulfill({ contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="90"><rect width="160" height="90" fill="green"/></svg>' });
        return route.fulfill({ contentType: 'text/html', body: '<html></html>' });
      });
      try {
        await page.goto('http://posts-inline.test/');
        await page.setContent(`<html lang="${language}" dir="${language === 'fa' ? 'rtl' : 'ltr'}" data-mode="light" data-theme="emerald"><header><div id="panelPageTitle"></div><div id="panelPageActions"></div></header><main id="mainContent" class="p-4">${renderPanelView('posts', language)}${renderPanelView('post-editor', language)}</main><div id="modalContainer"></div>${renderPanelView('panel-filters-template')}${tableRuntime()}</html>`);
        await page.addScriptTag({ path: 'assets/vendor/tailwind/tailwindcss.js' }); await page.addStyleTag({ path: 'assets/theme/theme.css' });
        await page.evaluate(() => { window.adminCsrfToken = 'fixture'; window.fixtureAlerts = []; window.alert = message => fixtureAlerts.push(String(message)); window.closeModal = () => document.getElementById('modalContainer').replaceChildren(); });
        await page.addScriptTag({ path: 'assets/Analytics/js/admin.js' }); await page.addScriptTag({ path: 'assets/Analytics/js/admin-runtime.js' }); await page.addScriptTag({ path: 'assets/Analytics/js/posts.js' });
        await page.evaluate(() => { showSection('posts'); loadPosts(); }); await page.locator('[data-post-inline-edit="7"]').waitFor();
        const inline = page.locator('.post-inline-editor');
        const ready = id => page.waitForFunction(value => document.querySelector('.post-inline-editor #pePostId')?.textContent.trim() === String(value), id);
        const openInline = async id => { await page.locator(`[data-post-inline-edit="${id}"]`).click(); await ready(id); };
        await page.waitForFunction(() => document.querySelectorAll('#postCategoryFilter option').length === 3);
        const categoryFilter = page.locator('#postCategoryFilter');
        assert.equal(await categoryFilter.inputValue(), ''); assert.equal(await categoryFilter.locator('option').first().getAttribute('value'), '');
        assert.equal(await categoryFilter.locator('option').first().textContent(), language === 'en' ? 'All categories' : 'همه دسته‌ها');
        assert.equal(await categoryFilter.locator('svg').count(), 0, 'Category title markup is plain option text');
        await categoryFilter.selectOption('3'); await page.waitForTimeout(300);
        await page.locator('#postsPagination button').last().click(); await page.locator('[data-post-title="18"]').waitFor();
        assert.equal(listRequests.at(-1).searchParams.get('category'), '3'); assert.equal(listRequests.at(-1).searchParams.get('page'), '2');
        await categoryFilter.selectOption('4'); await page.locator('[data-post-title="8"]').waitFor();
        assert.equal(listRequests.at(-1).searchParams.get('page'), '1'); assert.equal(await page.locator('[data-post-title]').count(), 1);
        categories[1].title_fa = 'Updated category'; categories[1].title_en = 'Updated category';
        await page.evaluate(() => window.dispatchEvent(new CustomEvent('sornaz:data-mutated', {detail:{url:'/analytics/admin-post-categories/4/update'}})));
        await page.waitForFunction(() => document.querySelector('#postCategoryFilter option[value="4"]')?.textContent === 'Updated category');
        assert.equal(await categoryFilter.inputValue(), '4', 'Category refresh preserves the selected filter');
        failCategories = true; await page.evaluate(() => window.dispatchEvent(new CustomEvent('sornaz:data-mutated', {detail:{url:'/analytics/admin-post-categories/4/update'}})));
        await page.waitForFunction(() => document.getElementById('postCategoryFilter').hasAttribute('title'));
        failCategories = false; await categoryFilter.blur(); await categoryFilter.focus();
        await page.waitForFunction(() => !document.getElementById('postCategoryFilter').hasAttribute('title'));
        assert.equal(await categoryFilter.inputValue(), '4'); assert.equal(categoryLoads, 4, 'Category choices are cached, invalidated and retried separately');
        await categoryFilter.selectOption(''); await page.locator('[data-post-title="7"]').waitFor();
        assert.equal(await page.locator('[data-post-title="7"] img').count(), 0);
        assert.ok((await page.locator('[data-post-title="7"]').textContent()).startsWith('Add rhythm'), 'Article titles are not Add actions');
        await page.locator('[data-post-inline-edit="7"]').scrollIntoViewIfNeeded();
        await page.evaluate(() => { window.inlineScrollCalls=0;const original=Element.prototype.scrollIntoView;Element.prototype.scrollIntoView=function(...args){inlineScrollCalls++;return original.apply(this,args);};window.inlineScrollStart=[scrollX,scrollY,document.getElementById('postsTable').parentElement.scrollLeft];document.querySelector('[data-post-inline-edit="7"]').click(); });
        await ready(7); await inline.locator('#peRelated input[value="9"]').waitFor();
        assert.equal(await page.locator('.post-inline-reveal').evaluate(element => element.getAnimations().some(animation => animation.playState === 'running')), true, 'Opening interpolates the expansion height');
        await page.waitForTimeout(450);
        assert.deepEqual(await page.evaluate(() => [scrollX,scrollY,document.getElementById('postsTable').parentElement.scrollLeft]), await page.evaluate(() => inlineScrollStart), 'Opening and loading must keep viewport and table scroll positions');
        assert.equal(await page.evaluate(() => inlineScrollCalls), 0, 'Inline expansion never scrolls the page programmatically');
        assert.equal(await page.evaluate(() => document.documentElement.style.overflowAnchor), '', 'Scroll anchoring override is restored after animation');
        assert.equal(await inline.evaluate(element => { const box=element.getBoundingClientRect(),viewport=document.getElementById('postsTable').parentElement.getBoundingClientRect();return box.left>=viewport.left-1&&box.right<=viewport.right+1; }), true, 'Inline controls stay in the visible horizontal table viewport');
        if (process.env.POST_INLINE_SCREENSHOT && language === 'fa') await page.screenshot({path:`storage/notation-browser-check/post-inline-smooth-${width}.png`});
        assert.equal(await page.locator('.post-inline-expand').count(), 1); assert.equal(await page.locator('[data-post-inline-edit="7"]').getAttribute('aria-expanded'), 'true');
        assert.ok(await page.locator('.post-inline-expand').evaluate(row => row.previousElementSibling.querySelector('[data-post-inline-edit="7"]') && row.cells[0].colSpan === row.closest('table').tHead.rows[0].cells.length), 'Expansion spans the shared identity column and follows its parent row');
        assert.equal(await inline.locator('#peContent,#peVisualContent,#peSummary,#peDescription').count(), 0);
        for (const id of ['peTitle', 'peSlug', 'peStatus', 'peVisibility', 'pePublishedAt', 'pePassword', 'peType', 'peCategories', 'peCover', 'peRelated', 'peGuid']) assert.equal(await inline.locator('#' + id).count(), 1, id + ' is editable inline');
        assert.equal(await page.locator('#peTitle').count(), 1); assert.equal(await inline.locator('#peCategories svg').count(), 0);
        for (const [id, value] of Object.entries({ peTitle: 'Edited title <strong>plain text</strong>', peSlug: 'updated-slug', pePublishedAt: '2027-01-02T03:04', pePassword: 'new-password', peGuid: 'https://example.test/changed' })) await inline.locator('#' + id).fill(value);
        await inline.locator('#peStatus').selectOption('pending'); await inline.locator('#peVisibility').selectOption('premium'); await inline.locator('#peType').selectOption('product');
        await inline.locator('#peCategories input[value="4"]').check(); await inline.locator('#peRelated input[value="9"]').check();
        records[0].views_count = 742;
        await page.evaluate(() => window.dispatchEvent(new CustomEvent('sornaz:remote-mutation', { detail: { url: '/analytics/admin-posts/7/metadata' } })));
        await page.waitForFunction(() => document.querySelector('#postsTable tbody')?.textContent.includes('742'));
        assert.equal(await inline.locator('#peTitle').inputValue(), 'Edited title <strong>plain text</strong>'); assert.equal(await inline.locator('#peCategories input[value="4"]').isChecked(), true);
        await page.evaluate(() => { document.getElementById('peContent').value = 'HIDDEN BODY'; document.getElementById('peSummary').value = 'HIDDEN SUMMARY'; document.getElementById('peDescription').value = 'HIDDEN DESCRIPTION'; document.getElementById('peVisualContent').innerHTML = '<p>HIDDEN VISUAL</p>'; return previewPostEditor(); });
        assert.equal(await page.locator('#postPreviewFrame').getAttribute('sandbox'), ''); const preview = await page.locator('#postPreviewFrame').getAttribute('srcdoc');
        assert.ok(preview.includes('Original <em>article</em>')); assert.equal(preview.includes('HIDDEN VISUAL'), false); await page.evaluate(() => closeModal());
        failSave = true; await page.evaluate(() => savePostEditor('status')); assert.equal(writes.length, 1);
        assert.equal(await inline.locator('#peTitle').inputValue(), 'Edited title <strong>plain text</strong>'); assert.equal(await inline.locator('#peRelated input[value="9"]').isChecked(), true);
        failSave = false; records[0].content = '<p>Concurrent article update.</p>'; records[0].summary = 'Concurrent summary'; records[0].description = 'Concurrent description';
        await page.evaluate(async () => { const first = savePostEditor('status'); savePostEditor('status'); await first; }); assert.equal(writes.length, 2, 'Duplicate clicks write once');
        const saved = writes[1].data;
        for (const [key, value] of Object.entries({ slug: 'updated-slug', status: 'pending', visibility: 'premium', published_at: '2027-01-02T03:04', password: 'new-password', type: 'product', guid: 'https://example.test/changed', cover_media_id: 70, categories: '3,99,4', related_posts_id: '8,199,9' })) assert.equal(saved[key], value, key + ' saved');
        assert.equal(records[0].content, '<p>Concurrent article update.</p>'); assert.equal(records[0].summary, 'Concurrent summary'); assert.equal(records[0].description, 'Concurrent description'); assert.equal(records[0].visibility_user_id, 72);
        await openInline(7); await inline.locator('#peRelated input[value="9"]').waitFor();
        await inline.locator('#peCover').setInputFiles({ name: 'new-featured.png', mimeType: 'image/png', buffer: png });
        await page.waitForFunction(() => document.querySelector('.post-inline-editor #peCoverImg')?.getAttribute('src') === '/cover.svg?uploaded=1');
        await page.evaluate(() => savePostEditor('status')); assert.equal(writes.at(-1).data.cover_media_id, 777); assert.equal(writes.at(-1).data.cover, '/cover.svg?uploaded=1');
        await page.locator('[data-post-title="7"]').click(); await page.locator('#post-editor:not(.hidden) #peVisualContent').waitFor();
        assert.equal(await page.locator('#peContent').inputValue(), records[0].content); assert.equal(await page.locator('#peSummary').isVisible(), true); assert.equal(await page.locator('#peDescription').isVisible(), true); assert.equal(await page.locator('#peTitle').count(), 1);
        await page.locator('#peCodeTab').click();
        for (const mode of ['light', 'dark']) {
          await page.evaluate(value => document.documentElement.dataset.mode = value, mode);
          const surface = element => {
            for (let current = element; current; current = current.parentElement) {
              const background = getComputedStyle(current).backgroundColor;
              if (background !== 'rgba(0, 0, 0, 0)' && background !== 'transparent') return background;
            }
            return 'rgb(255, 255, 255)';
          };
          const expected = await page.locator('#peVisualContent').evaluate(surface);
          await page.waitForFunction(background => getComputedStyle(document.getElementById('peContent')).backgroundColor === background, expected);
          assert.equal(await page.locator('#peContent').evaluate(surface), expected, 'Code and visual editor share their ' + mode + ' surface');
        }
        await page.evaluate(() => document.documentElement.dataset.mode = 'light'); await page.locator('#peVisualTab').click();
        await page.locator('#post-editor [onclick="closePostEditor()"]').click();
        holdGet = true; const getHit = new Promise(resolve => { notifyGet = resolve; });
        await page.evaluate(() => { togglePostInlineEdit(7); return true; }); await getHit;
        await page.evaluate(() => togglePostInlineEdit(8)); await ready(8); releaseGet(); holdGet = false; await page.waitForTimeout(60);
        assert.equal(await inline.locator('#pePostId').textContent(), '8'); assert.equal(await page.locator('.post-inline-expand').count(), 1);
        await page.evaluate(() => togglePostInlineEdit(8)); failGet = true; await page.evaluate(() => togglePostInlineEdit(7));
        assert.ok((await page.locator('.post-inline-expand').textContent()).includes('Fixture detail failure')); failGet = false;
        await page.locator('.post-inline-expand').getByRole('button', { name: /Retry|تلاش/i }).click(); await ready(7); await inline.locator('#peRelated input[value="9"]').waitFor();
        holdUpload = true; const uploadHit = new Promise(resolve => { notifyUpload = resolve; });
        await inline.locator('#peCover').setInputFiles({ name: 'featured.png', mimeType: 'image/png', buffer: png }); await uploadHit;
        failOptions = true; await page.evaluate(() => togglePostInlineEdit(8)); await ready(8); await inline.locator('#peRelated button').waitFor();
        releaseUpload(); holdUpload = false; await page.waitForTimeout(80);
        assert.equal(await inline.locator('#peCoverImg').getAttribute('src'), '/cover.svg?id=8'); await page.evaluate(() => savePostEditor('status'));
        assert.equal(writes.at(-1).id, 8); assert.equal(writes.at(-1).data.cover_media_id, 71); assert.equal(writes.at(-1).data.related_posts_id, '8,199'); assert.equal(writes.at(-1).data.status, 'request-confirmed'); assert.equal(uploads.length, 2);
        failOptions = false; await openInline(7); await page.locator('#postsPagination button').last().click(); await page.locator('[data-post-inline-edit="17"]').waitFor();
        assert.equal(await page.locator('.post-inline-expand').count(), 0); assert.equal(await page.locator('#post-editor #peTitle').count(), 1);
        await page.emulateMedia({reducedMotion:'reduce'}); await page.evaluate(() => togglePostInlineEdit(17)); await ready(17);
        assert.equal(await page.locator('.post-inline-reveal').evaluate(element => element.getAnimations().length), 0, 'Reduced motion opens without animation');
        await page.evaluate(() => togglePostInlineEdit(17));
        await page.evaluate(() => document.documentElement.dataset.mode = 'dark'); assert.equal(await page.locator('html').evaluate(element => element.scrollWidth > innerWidth + 1), false);
        assert.equal(await page.evaluate(() => window.badTitle || window.badCategory), undefined); assert.deepEqual(errors, []);
        console.log(`Post inline editor ${language}/${width}: passed`);
      } finally { releaseGet?.(); releaseUpload?.(); await page.close(); }
    }
    console.log('Post inline metadata: expansion, title navigation, CSRF metadata-only writes, article preservation, background refresh, stale requests, failed saves/options, upload isolation and pagination passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
