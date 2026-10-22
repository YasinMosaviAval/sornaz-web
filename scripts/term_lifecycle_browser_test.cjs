const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']){
  const page=await browser.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.setContent('<html lang="'+lang+'"><div id="modalContainer"></div></html>');
  await page.addScriptTag({path:'assets/Analytics/js/terms.js'});
  await page.evaluate(()=>{
   allTerms=[{id:7,name:'Lifecycle term',status_code:'ongoing'}];window.termPermissions={isReceptionist:false};
   window.closeModal=()=>document.getElementById('modalContainer').replaceChildren();window.loadTerms=async()=>{};window.statusRequests=[];window.statusURLs=[];
   window.fetch=async(url,options)=>{
    const params=new URLSearchParams(options.body),payload=JSON.parse(decodeURIComponent(escape(atob(params.get('payload_b64').replace(/-/g,'+').replace(/_/g,'/')))));
    window.statusRequests.push(payload);window.statusURLs.push(url);
    if(window.rejectStatus)return{ok:false,text:async()=>JSON.stringify({success:false,message:'Status denied'})};
    allTerms[0].status_code=payload.status;return{ok:true,text:async()=>JSON.stringify({success:true,data:{status:payload.status}})};
   };
  });
  await page.evaluate(()=>cycleTermStatus(7));
  const options=await page.locator('#termStatusChoice option').evaluateAll(nodes=>nodes.map(node=>node.value));assert(options.includes('canceled')&&options.includes('suspended'));
  assert.equal(await page.evaluate(()=>statusRequests.length),0,'Opening the status dialog never changes state');
  await page.selectOption('#termStatusChoice','suspended');await page.evaluate(()=>window.rejectStatus=true);
  await page.locator('[onclick^="saveTermStatus"]').click();await page.waitForFunction(()=>document.querySelector('[data-term-status-error]').textContent==='Status denied');
  assert.equal(await page.locator('#termStatusChoice').inputValue(),'suspended');
  await page.evaluate(()=>window.rejectStatus=false);await page.locator('[onclick^="saveTermStatus"]').click();await page.waitForFunction(()=>!document.querySelector('#termStatusChoice'));
  assert.deepEqual(await page.evaluate(()=>statusRequests.at(-1)),{status:'suspended',fromStatus:'ongoing'});
  assert.equal(await page.evaluate(()=>statusURLs.at(-1)),'/academy/admin/terms/7/set-status','Explicit status route cannot fall back to cycling on older servers');
  await page.evaluate(()=>cycleTermStatus(7));await page.selectOption('#termStatusChoice','ongoing');await page.locator('[onclick^="saveTermStatus"]').click();await page.waitForFunction(()=>!document.querySelector('#termStatusChoice'));
  assert.deepEqual(await page.evaluate(()=>statusRequests.at(-1)),{status:'ongoing',fromStatus:'suspended'});
  await page.evaluate(()=>{allTerms[0].status_code='canceled';return cycleTermStatus(7);});
  assert.deepEqual(await page.locator('#termStatusChoice option').evaluateAll(nodes=>nodes.map(node=>node.value)),['canceled']);
  await page.evaluate(()=>{closeModal();window.termPermissions.isReceptionist=true;return cycleTermStatus(7);});assert.equal(await page.locator('#termStatusChoice').count(),0);
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Term status dialog: explicit cancel/suspend/resume, no accidental cycling, failed-save preservation and receptionist guard passed (fa/en).');
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
