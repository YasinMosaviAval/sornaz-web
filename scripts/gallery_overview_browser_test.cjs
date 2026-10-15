const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    let submitted = '';
    page.on('pageerror', error => errors.push(error.message));
    await page.route('**/*', async route => {
      const path = new URL(route.request().url()).pathname;
      if (path === '/assets/Analytics/js/gallery.js' || path === '/assets/Analytics/js/gallery-templates.js')
        return route.fulfill({ path: '.' + path });
      if (path === '/analytics/admin-gallery/realtime-version')
        return route.fulfill({ json: { success: true, data: { version: 'v1' } } });
      if (path === '/analytics/admin-gallery' && route.request().method() === 'POST') {
        submitted = route.request().postData() || '';
        return route.fulfill({ json: { success: true, data: { id: 4 } } });
      }
      if (path === '/analytics/admin-gallery') return route.fulfill({ json: { success: true, data: {
        owners: [{ userId: 2, name: 'نام آزمایشی' }], hideOwnerFilters: true,
        items: [
          { id: 1, ownerId: 2, ownerName: 'نام آزمایشی', category: 'cover', type: 'image', title: 'کاور', url: '/cover.jpg' },
          { id: 2, ownerId: 2, ownerName: 'نام آزمایشی', category: 'gallery', type: 'image', title: 'اول', url: '/one.jpg' },
          { id: 3, ownerId: 2, ownerName: 'نام آزمایشی', category: 'gallery', type: 'video', title: 'دوم', url: '/two.mp4' },
        ],
      } } });
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<style>.hidden{display:none}</style><div id="gallery" class="section"><button id="add" onclick="openAddGalleryModal()">افزودن</button>${['cover','logo','intro_video','gallery'].map(k => `<section data-gallery-overview-category="${k}"><h2>${k}</h2><div class="gallery-grid" data-gallery-category="${k}"></div></section>`).join('')}</div><div id="modalContainer"></div><script>window.adminCsrfToken='test';window.closeModal=()=>document.getElementById('modalContainer').innerHTML='';</script><script src="/assets/Analytics/js/gallery-templates.js"></script><script src="/assets/Analytics/js/gallery.js"></script>` });
    });
    await page.goto('http://sornaz.test/');
    await page.waitForSelector('[data-gallery-category="cover"] .gallery-card');
    assert.deepEqual(await page.locator('[data-gallery-overview-category]').evaluateAll(nodes => nodes.map(n => n.dataset.galleryOverviewCategory)), ['cover','logo','intro_video','gallery']);
    assert.equal(await page.locator('[data-gallery-category="gallery"] .gallery-card').count(), 2);
    await page.click('#add');
    assert.equal(await page.locator('#galleryTitle').inputValue(), 'آیتم شماره 3 گالری نام آزمایشی');
    await page.selectOption('#galleryCategory', 'cover');
    assert.equal(await page.locator('#galleryTitle').inputValue(), 'کاور نام آزمایشی');
    await page.selectOption('#galleryCategory', 'logo');
    assert.equal(await page.locator('#galleryTitle').inputValue(), 'لوگو نام آزمایشی');
    await page.selectOption('#galleryCategory', 'intro_video');
    assert.equal(await page.locator('#galleryTitle').inputValue(), 'ویدیو معرفی نام آزمایشی');
    assert.equal(await page.locator('#galleryFile').getAttribute('accept'), 'video/mp4,video/webm,video/quicktime');
    await page.locator('#galleryFile').setInputFiles({ name: 'sample.mp4', mimeType: 'video/mp4', buffer: Buffer.from('sample') });
    await page.evaluate(() => saveGalleryItem());
    assert(submitted.includes('name="collection"') && submitted.includes('intro_video'));
    assert.deepEqual(errors, []);
    console.log('Gallery overview: order, cards, titles, media type and submission passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
