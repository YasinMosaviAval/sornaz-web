// Flutter's shared-editor behavioral fixtures, executed against website assets only.
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const root=path.resolve(__dirname,'..');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 const page=await browser.newPage();
 await page.route('http://notation.test/**',route=>{const url=new URL(route.request().url());const file=path.resolve(root,'.'+url.pathname);return file.startsWith(root+path.sep)&&fs.existsSync(file)?route.fulfill({path:file}):route.fulfill({status:404,body:''});});
 for(const name of ['timing','musicxml','import','import_ui','grand_staff','grand_staff_ui','small_screen']){
  await page.goto('http://notation.test/scripts/fixtures/notation/'+name+'.html');
  await page.waitForFunction(()=>{const value=document.body.dataset.result||document.getElementById('result')?.textContent;return value&&value!=='pending'&&value!=='RUNNING';},{},{timeout:15000});
  const result=await page.evaluate(()=>document.body.dataset.result||document.getElementById('result')?.textContent);assert.ok(['passed','PASS'].includes(result),name+': '+result);console.log('Shared notation '+name+': passed');
 }
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
