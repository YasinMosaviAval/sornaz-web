const assert = require('node:assert/strict');
const {execFileSync} = require('node:child_process');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {tableRuntime} = require('./panel-table-fixture.cjs');
const render = (name, lang) => execFileSync('php', ['scripts/panel-table-view-fixture.php', name, lang, '', '1'], {encoding:'utf8'});
(async () => {
 const browser = await chromium.launch({channel:'msedge',headless:true});
 try {
  for (const lang of ['fa','en']) {
   const page = await browser.newPage(), errors = [], writes = [];
   let postStatus='draft', commentStatus='pending', fail=false;
   page.on('pageerror', error=>errors.push(error.message));
   await page.route('**/*', async route=>{
    const req=route.request(), path=new URL(req.url()).pathname;
    if(req.method()==='POST') {
     writes.push({path,data:JSON.parse(Buffer.from(new URLSearchParams(req.postData()).get('payload_b64'),'base64url').toString())});
     if(fail) return route.fulfill({status:422,json:{success:false,message:'Fixture failure'}});
     if(path.includes('admin-posts')) postStatus=writes.at(-1).data.status;
     else commentStatus=writes.at(-1).data.status;
     return route.fulfill({json:{success:true}});
    }
    if(path==='/analytics/admin-posts') return route.fulfill({json:{success:true,data:{posts:[{id:1,title:'Post',categories:'7,8',category_names:['Category <one>','Category two'],status:postStatus,type:'post'}],total:1,page:1}}});
    if(path==='/analytics/admin-comments') return route.fulfill({json:{success:true,data:{comments:[{id:2,author:'Author',content:'Body',status:commentStatus}],total:1,page:1}}});
    return route.fulfill({body:'<html></html>',contentType:'text/html'});
   });
   await page.goto('http://workspace.test/');
   const names=['posts','post-categories','media','comments','awards','badges','experiences','certificates','educations','events','polls','publications','gallery','branches','branch-types','availabilities','scheduling-rules','availability-exceptions','users','roles','permissions'];
   await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}</style><h1 id="panelPageTitle"></h1>`+names.map(name=>render(name,lang)).join('')+tableRuntime()+'</html>');
   await page.evaluate(()=>{window.alert=message=>window.lastAlert=message;});
   for(const name of ['admin','posts','comments']) await page.addScriptTag({path:`assets/Analytics/js/${name}.js`});
   for(const [ids,title] of [
    [['posts','post-categories','media','comments'],lang==='en'?'Posts':'نوشته ها'],
    [['awards','badges','experiences','certificates','educations','events','polls','publications'],lang==='en'?'Records and achievements':'سوابق و دستاوردها'],
    [['gallery-cover','gallery-logo','gallery-intro-video','gallery-collection'],lang==='en'?'Gallery':'گالری'],
    [['branches','branch-types','availabilities','scheduling-rules','availability-exceptions'],lang==='en'?'Organization':'سازمان'],
    [['users','roles','permissions'],lang==='en'?'Roles and permissions':'نقش ها و دسترسی ها']]) {
     for(const id of ids) {
      await page.evaluate(id=>showSection(id),id);
      assert.equal(await page.locator('#panelPageTitle').textContent(),title);
      assert.equal(await page.locator('#'+id+' h1').count(),0);
      assert.equal(await page.locator('#'+id).isVisible(),true);
     }
   }
   assert.deepEqual(await page.locator('#users .panel-page-tabs button').evaluateAll(nodes=>nodes.map(n=>n.getAttribute('onclick').match(/"([^"]+)"/)[1])),['roles','permissions','users','teachers','member-schedules','employee-leaves']);
   assert.match(await page.locator('#branches .panel-page-tabs button').first().getAttribute('onclick'),/branch-types/);
   await page.locator('#permissions .panel-page-tabs button').nth(2).click();
   assert.equal(await page.locator('#users').isVisible(),true);
   await page.evaluate(()=>showSection('branches'));
   assert.match(await page.locator('#branches [aria-current="page"]').getAttribute('onclick'),/branches/);
   await page.evaluate(()=>{document.getElementById('gallery').remove();showSection('gallery');});
   assert.equal(await page.locator('#gallery-cover').isVisible(),true);
   await page.evaluate(()=>showSection('posts'));
   await page.evaluate(()=>loadPosts());
   assert.match(await page.locator('#postsTable tbody').textContent(),/Category <one>, Category two/);
   assert.equal(await page.locator('#postsTable tbody one').count(),0);
   await page.locator('#postsTable button[onclick^="cyclePostStatus"]').click();
   await page.waitForFunction(()=>document.querySelector('#postsTable button[onclick^="cyclePostStatus"]').textContent.includes('منتشر'));
   assert.deepEqual(writes.at(-1),{path:'/analytics/admin-posts/1/status',data:{status:'published'}});
   fail=true;
   await page.locator('#postsTable button[onclick^="cyclePostStatus"]').click();
   await page.waitForFunction(()=>window.lastAlert==='Fixture failure');
   assert.equal(await page.locator('#postsTable button[onclick^="cyclePostStatus"]').isEnabled(),true);
   fail=false;
   await page.evaluate(()=>showSection('comments'));
   await page.evaluate(()=>loadAdminComments());
   await page.locator('#adminCommentsTable button[onclick^="cycleCommentStatus"]').click();
   await page.waitForFunction(()=>document.querySelector('#adminCommentsTable button[onclick^="cycleCommentStatus"]').textContent.includes('تأیید'));
   assert.equal(writes.at(-1).data.status,'approved');
   assert.deepEqual(errors,[]);
   await page.close();
  }
  console.log('Workspace tabs: fa/en navigation, shared headers, removed introductions, tab order, escaped category names, status writes and failed-write recovery passed.');
 } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
