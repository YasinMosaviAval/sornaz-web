const assert=require('node:assert/strict'),fs=require('node:fs'),cp=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
(async()=>{
 const html=JSON.parse(cp.execFileSync('php',['scripts/home_hero_fixture.php'],{encoding:'utf8'}));
 const script=fs.readFileSync('assets/Page/js/page-content-editor.js','utf8');
 const expected={fa:['همراه شما در مسیر یادگیری موسیقی','بهترین آموزشگاه موسیقی را پیدا کنید','آموزشگاه‌ها، اساتید، کلاس‌ها و دوره‌های موسیقی سراسر ایران را جستجو و مقایسه کنید.'],en:['With you on your music learning journey','Find the best music academy','Search and compare music academies, teachers, classes and courses across Iran.']};
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{
  for(const lang of ['fa','en'])for(const role of ['guest','member','admin']){
   const page=await browser.newPage();
   await page.route('**/*',route=>route.request().resourceType()==='document'?route.fulfill({contentType:'text/html; charset=utf-8',body:'<html><head><meta charset="utf-8"></head><body><header id="sentinel">'+role+'</header><main>'+html[lang]+'</main></body></html>'}):route.abort());
   await page.goto('http://home.test/?cms_page=home'+(role==='admin'?'&cms=1':''));
   await page.evaluate(()=>{window.fetch=async()=>({json:async()=>({data:{items:Array.from({length:300},(_,i)=>({key:'site.page.home.text.'+i,value:'Old saved text'}))}})});});
   await page.addScriptTag({content:script});
   await page.evaluate(()=>document.dispatchEvent(new Event('DOMContentLoaded')));
   await page.waitForFunction(()=>document.getElementById('sentinel').textContent==='Old saved text');
   const copy=(await page.locator('[data-fixed-copy]').allTextContents()).map(s=>s.replace(/\s+/g,' ').trim());
   assert.deepEqual(copy,expected[lang],lang+' '+role);
   assert.equal(await page.locator('[data-fixed-copy][data-page-content-key]').count(),0);
   await page.close();
  }
 }finally{await browser.close();}
 console.log('Home hero: identical public copy for guests, members and admins in both locales, including stale CMS overrides.');
})().catch(e=>{console.error(e);process.exitCode=1;});
