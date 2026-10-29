const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const envelope=(data,status=200)=>({status,data:{success:status===200,...(status===200?{data}:{message:'Denied fixture'})}});
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{for(const lang of ['fa','en'])for(const width of [390,1280]){
  const page=await browser.newPage({viewport:{width,height:850}});const errors=[];page.on('pageerror',e=>errors.push(e.message));
  let allowed=true,denied=false,reads=0,uploads=0,deletes=0,release;
  await page.route('**/*',async route=>{
   const req=route.request(),url=new URL(req.url());
   if(!url.pathname.startsWith('/academy/sessions/'))return route.fulfill({contentType:'text/html',body:`<html lang="${lang}" dir="${lang==='fa'?'rtl':'ltr'}"><body><button data-session-files="1">files</button><button data-session-files="2">second</button><div id="modalContainer"><div id="parent-dialog">parent</div></div></body></html>`});
   if(req.method()==='POST'){
    assert.equal(req.headers()['x-csrf-token'],'fixture-token');assert.match(req.postData()||'',/fixture-token/);
    if(url.pathname.endsWith('/delete'))deletes++;else{uploads++;assert.match(req.postData()||'',/name="file"/);}
    return route.fulfill({json:envelope({})});
   }
   if(url.pathname.endsWith('/download'))return route.fulfill({status:403,json:envelope(null,403)});
   reads++;const current=reads;
   if(current===4)await new Promise(r=>release=r);
   if(denied && current!==4)return route.fulfill({status:403,json:envelope(null,403)});
   return route.fulfill({json:envelope({sessionId:1,files:[{id:7,name:current===4?'STALE_PRIVATE':'<img src=x onerror="window.injected=1"> موسیقی.txt',size:15,canDelete:allowed}],canUpload:allowed,canDownload:true,maxBytes:1000})});
  });
  await page.goto('http://session-fixture.test/');await page.evaluate(()=>window.siteCsrfToken='fixture-token');await page.addScriptTag({path:'assets/Analytics/js/session-files.js'});
  await page.locator('[data-session-files="1"]').click();await page.waitForSelector('[data-session-file-download]');
  assert.equal(await page.locator('#parent-dialog').count(),1,'Opening files retains its parent dialog');
  assert.equal(await page.locator('[data-session-files-list] img').count(),0);assert.equal(await page.evaluate(()=>window.injected),undefined);
  assert.equal(await page.locator('[role="dialog"]').evaluate(e=>e.parentElement.style.backgroundColor),'');
  assert.equal(await page.locator('[data-session-files-upload]').isVisible(),true);
  await page.locator('input[type=file]').setInputFiles({name:'exercise.txt',mimeType:'text/plain',buffer:Buffer.from('exercise')});
  await page.locator('[data-session-files-upload] button').click();await page.waitForFunction(()=>!document.querySelector('[data-session-files-retry]').disabled);assert.equal(uploads,1);
  await page.locator('[data-session-file-delete]').click();await page.locator('[data-session-file-row] button').filter({hasText:lang==='fa'?'حذف':'Delete'}).click();await page.waitForFunction(()=>!document.querySelector('[data-session-files-retry]').disabled);assert.equal(deletes,1);
  // Two concurrent refreshes: the older successful reply must not restore files after a denial.
  await page.locator('[data-session-files-retry]').click();await page.waitForTimeout(30);denied=true;
  await page.locator('[data-session-files-retry]').click();await page.waitForFunction(()=>document.querySelector('[data-session-files-list]').children.length===0);
  release();await page.waitForTimeout(80);assert.equal(await page.locator('[data-session-files-list]').textContent(),'');assert.equal(await page.locator('[data-session-files-upload]').isVisible(),false);
  denied=false;allowed=false;await page.locator('[data-session-files-retry]').click();await page.waitForSelector('[data-session-file-download]');assert.equal(await page.locator('[data-session-files-upload]').isVisible(),false);assert.equal(await page.locator('[data-session-file-delete]').count(),0);
  await page.locator('[data-session-file-download]').click();await page.waitForFunction(()=>document.querySelector('[data-session-files-list]').children.length===0);assert.equal(await page.locator('iframe').count(),0);
  await page.keyboard.press('Escape');assert.equal(await page.locator('[role=dialog]').count(),0);assert.equal(await page.locator('#parent-dialog').count(),1);
  assert.deepEqual(errors,[]);await page.close();
 }}finally{await browser.close();}
 console.log('Session files browser passed: Persian/English, mobile/desktop, upload/delete CSRF, student controls, escaped names, stale reads and download denials.');
})().catch(e=>{console.error(e);process.exitCode=1;});
