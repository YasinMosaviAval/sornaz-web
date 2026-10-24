const assert=require('node:assert/strict');
const {execFileSync}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{
 const html=execFileSync('php',['scripts/guide-library-view-fixture.php','1'],{encoding:'utf8',maxBuffer:32*1024*1024});
 assert.equal(execFileSync('php',['scripts/guide-library-view-fixture.php','2'],{encoding:'utf8'}),'');
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try {
  for(const mode of ['light','dark']) {
   const page=await browser.newPage(),errors=[];
   page.on('pageerror',e=>errors.push(e.message));
   await page.setContent(`<html data-mode="${mode}"><style>.hidden{display:none}</style><main id="mainContent">${html}</main>${['panel-page-header-template','panel-filters-template','panel-organization-tabs-template'].map(name=>renderPanelView(name)).join('')}${tableRuntime()}</html>`);
   await page.evaluate(()=>document.getElementById('guides').classList.remove('hidden'));
   await page.waitForFunction(()=>document.getElementById('guides').dataset.adminUiNormalized==='1');
   const cards=page.locator('.guide-document-card');
   assert.ok(await cards.count()>30);
   assert.equal(await page.locator('.guide-document-card:not(details)').count(),0,'Normalization must not replace details with divs');
   assert.equal(await page.locator('.guide-library table:not(details table)').count(),0);
   assert.equal(await page.locator('.guide-library [data-admin-identity-column],.guide-library [data-panel-auto-sort]').count(),0,'Documentation tables must not become management tables');
   const count=await cards.count();
   for(let i=0;i<count;i++) {
    const card=cards.nth(i);
    assert.equal(await card.getAttribute('open'),null);
    assert.equal(await card.locator('.guide-md').isVisible(),false);
    await card.locator('summary').click();
    assert.equal(await card.locator('.guide-md').isVisible(),true);
    await card.locator('.guide-document-close').click();
    assert.equal(await card.locator('.guide-md').isVisible(),false);
   }
   await cards.nth(0).locator('summary').click();
   await cards.nth(1).locator('summary').click();
   await page.waitForFunction(()=>document.querySelectorAll('.guide-document-card[open]').length===1);
   await page.evaluate(()=>filterGuideDocuments('Modules/'));
   assert.equal(await page.locator('.guide-document-card:not([hidden])').count(),await page.locator('.guide-document-card[data-guide-search*="modules/"]').count());
   assert.deepEqual(errors,[]);
   await page.close();
  }
  console.log('Guide library: every repository document remains collapsed after table normalization, summary/close controls, accordion, search, light/dark modes and founder-only rendering passed.');
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
