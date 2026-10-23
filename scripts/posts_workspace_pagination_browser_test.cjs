const assert = require('node:assert/strict');
const fs = require('node:fs');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {renderPanelView, tableRuntime} = require('./panel-table-fixture.cjs');
(async () => {
 const browser = await chromium.launch({channel:'msedge',headless:true});
 try {
  for (const language of ['fa','en']) for (const total of [0,10,11,25]) {
   const page = await browser.newPage(), errors = [], requests = [];
   page.on('pageerror', e => errors.push(e.message));
   await page.route('**/*', async route => {
    const url = new URL(route.request().url()), size = Number(url.searchParams.get('perPage') || 10), current = Number(url.searchParams.get('page') || 1);
    const all = Array.from({length:total}, (_,i) => ({id:i+1,title:'Title '+(i+1),title_fa:'Category '+(i+1),title_en:'Category '+(i+1),slug:'slug-'+i,author:'Author',content:'Body',postTitle:'Post',status:'pending',type:'post',dateISO:'2026-10-01',url:'/image.png',filename:'image.png',mime:'image/png',size:12}));
    const slice = all.slice((current-1)*size,current*size);
    if (url.pathname.startsWith('/analytics/admin-')) {
     requests.push(url);
     if (['/analytics/admin-posts','/analytics/admin-comments','/analytics/admin-media'].includes(url.pathname)) assert.equal(size,10);
     const data = url.pathname.endsWith('post-categories') ? all : url.pathname.endsWith('posts') ? {posts:slice,total,page:current,perPage:10,statusCounts:{pending:total}} : url.pathname.endsWith('comments') ? {comments:slice,total,page:current,perPage:10,statusCounts:{pending:total},posts:[]} : url.pathname.endsWith('media') ? {items:slice.map(x=>({...x,type:'image'})),total,page:current,perPage:10,months:[]} : {reports:all,branches:[]};
     return route.fulfill({json:{success:true,data}});
    }
    return route.fulfill({contentType:'text/html',body:'<html></html>'});
   });
   await page.goto('http://workspace.test/');
   const templates = ['panel-page-header-template','panel-filters-template','panel-organization-tabs-template'].map(key=>renderPanelView(key,language)).join('');
   const shell = fs.readFileSync('Modules/Analytics/Resources/Views/sections/panel-table-template.php','utf8').match(/<template id="adminTableTemplate">[\s\S]*?<\/template>/)[0];
   await page.setContent('<html lang="'+language+'"><style>.hidden{display:none} [data-panel-filter-row]{display:grid;grid-auto-flow:column;grid-auto-columns:minmax(0,1fr);gap:16px} [data-panel-filter-row]>*{min-width:0;box-sizing:border-box}</style><main id="mainContent">'+['posts','post-categories','media','comments','reports','chart-gallery'].map(key=>renderPanelView(key,language)).join('')+'</main>'+templates+shell+tableRuntime()+'</html>');
   await page.evaluate(()=>{document.querySelectorAll('.section').forEach(node=>node.classList.remove('hidden'));window.alert=()=>{};});
   for (const script of ['posts','post-categories','media','comments','report-templates','reports']) await page.addScriptTag({path:'assets/Analytics/js/'+script+'.js'});
   await page.evaluate(async()=>{await Promise.all([loadPosts(),loadPostCategories(),loadAdminMedia(1),loadAdminComments()]);});
   await page.waitForTimeout(400);
   for (const [key,table,pager] of [['posts','postsTable','postsPagination'],['post-categories','postCategoriesTable','postCategoriesPagination'],['media','mediaTable','mediaPagination'],['comments','adminCommentsTable','commentsPagination'],['reports','reportsTable','reportsPagination']]) {
    assert.equal(await page.locator('#'+table).getAttribute('data-panel-table'),'1');
    const footer=page.locator('#'+pager);
    assert.equal(await footer.isVisible(),total>10,key+' pager threshold');
    assert.equal(await page.locator('#'+table+' tbody tr').count(),Math.min(total,10)||1,key+' fixed page size');
    if (total>10) {
     assert.equal(await footer.locator('button').first().textContent(),language==='en'?'First':'\u0627\u0648\u0644');
     assert.equal(await footer.locator('button').last().textContent(),language==='en'?'Last':'\u0622\u062e\u0631');
     await footer.locator('button').last().click();
     await page.waitForTimeout(60);
     assert.equal(await page.locator('#'+table+' tbody tr').count(),total%10||10,key+' last page');
     await footer.locator('button').first().click();
     await page.waitForTimeout(60);
     assert.equal(await page.locator('#'+table+' tbody tr').count(),10);
    }
   }
   if (total===25) {
    await page.locator('#postCategoriesTable [data-admin-identity-column] button').click();
    assert.equal(await page.locator('#postCategoriesTable tbody tr').first().getAttribute('data-id'),'25');
    await page.locator('#postCategoriesPagination button').last().click();
    assert.equal(await page.locator('#postCategoriesTable tbody tr').last().getAttribute('data-id'),'1');
   }
   assert.equal(await page.locator('#postsPerPage,#commentsPerPage,#mediaPerPage').count(),0);
   for (const id of ['media','chart-gallery']) {
    const fields=page.locator('#'+id+' [data-panel-filter-row]');
    assert.equal(await fields.locator(':scope > input').count(),1);
    assert.equal(await fields.locator(':scope > :first-child').getAttribute('id'),id==='media'?'mediaSearch':'chartGallerySearch');
    assert.equal(await fields.evaluate(row=>new Set([...row.children].map(node=>Math.round(node.getBoundingClientRect().top))).size),1);
   }
   const before=requests.length;
   await page.evaluate(()=>setMediaView('list'));
   assert.equal(await page.locator('#mediaList').isVisible(),true);
   assert.equal(await page.locator('#mediaListButton').getAttribute('aria-pressed'),'true');
   await page.evaluate(()=>setMediaView('grid'));
   assert.equal(await page.locator('#mediaList').isVisible(),false);
   assert.equal(await page.locator('#mediaGridButton').getAttribute('aria-pressed'),'true');
   assert.equal(requests.length,before,'View switches must not fetch');
   await page.evaluate(async()=>{
    document.getElementById('mediaTable').removeAttribute('id');
    await loadAdminMedia(1);
    document.getElementById('mediaListBody').closest('table').id='mediaTable';
    await Promise.all([changePostsPerPage(50),changeCommentsPerPage(50),changeMediaPerPage(50)]);
   });
   assert.deepEqual(errors,[]);
   await page.close();
  }
  console.log('Posts workspace: five shared pagers at 0/10/11/25 rows, fixed size, first/last navigation, filter rows and media view controls passed (fa/en).');
 } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
