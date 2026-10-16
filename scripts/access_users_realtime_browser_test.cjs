const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {tableRuntime,renderPanelView}=require('./panel-table-fixture.cjs');
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{
  const page=await browser.newPage(),errors=[],requests=[];page.on('pageerror',e=>errors.push(e.message));
  let count=25;
  await page.route('http://access.test/**',async route=>{
   const uri=new URL(route.request().url());
   if(uri.pathname==='/analytics/admin-user-access'){
    const p=uri.searchParams;requests.push(Object.fromEntries(p));
    let rows=Array.from({length:count},(_,i)=>({id:count-i,name:'Worker '+(count-i),roles:[],permissions:[],status:'approved',canEdit:false}));
    if(p.get('search')) rows=[{...rows[0],name:p.get('search')}];
    if(p.get('search')==='old')await new Promise(r=>setTimeout(r,150));
    const current=Number(p.get('page')||1),per=Number(p.get('perPage'));
    return route.fulfill({json:{data:{users:rows.slice((current-1)*per,current*per),total:rows.length,page:current,roles:[],permissions:[],branches:[]}}});
   }
   return route.fulfill({contentType:'text/html',body:'<html lang="en"></html>'});
  });
  await page.goto('http://access.test/');
  await page.setContent('<html lang="en"><body><main id="mainContent">'+renderPanelView('users')+'</main><div id="modalContainer"></div>'+renderPanelView('panel-filters-template')+tableRuntime()+'</body></html>');
  await page.locator('#users').evaluate(x=>x.classList.remove('hidden'));
  await page.addScriptTag({path:'assets/Analytics/js/users.js'});
  await page.locator('#accessUsersBody tr[data-id]').first().waitFor();
  const footer=page.locator('#users [data-panel-pagination]');
  assert.equal(await page.locator('#accessUsersBody tr').count(),10);
  assert.match(await footer.textContent(),/Showing 1 to 10 of 25/);
  await footer.getByRole('button',{name:'Next',exact:true}).click();
  await page.waitForFunction(()=>document.querySelector('#accessUsersBody tr')?.dataset.id==='15');
  await footer.getByRole('button',{name:'Last',exact:true}).click();
  await page.waitForFunction(()=>document.querySelector('#accessUsersBody tr')?.dataset.id==='5');
  assert.equal(await page.locator('#accessUsersBody tr').count(),5);
  await page.addScriptTag({path:'assets/Analytics/js/teacher-templates.js'});
  await page.addScriptTag({path:'assets/Analytics/js/teachers.js'});
  await page.exposeFunction('createWorker',()=>{count++;});
  await page.evaluate(async()=>{
   window.alert=()=>{};window.closeModal=()=>document.querySelector('#modalContainer').replaceChildren();
   window.encodeBranchPayload=x=>JSON.stringify(x);
   window.branchRequest=async(url)=>{if(url==='/academy/admin/members')await window.createWorker();return {members:[],staff_catalog:{}};};
   window.staffCatalog={organizations:[{user_id:10,name:'Academy'}],roles:[{id:7,name:'academy_employee',title:'Employee'}]};
   await openAddStaffModal();
   for(const [key,value]of Object.entries({Name:'New employee',Phone:'09120000000',NationalId:'1234567890',BirthDate:'2000-01-01',ContractTitle:'Contract',StartDate:'2026-01-01',EndDate:'2026-12-31',Price:'100'}))document.getElementById('staff'+key).value=value;
   document.getElementById('staffType').value='other';updateStaffOrganization('staff');document.getElementById('staffRole').value='7';
   await saveStaff();
  });
  await page.waitForFunction(()=>document.querySelector('#accessUsersBody tr')?.dataset.id==='26');
  assert.equal(count,26);assert.equal(requests.at(-1).page,'1');assert.equal(requests.at(-1).perPage,'10');
  await page.evaluate(async()=>{
   const search=document.getElementById('accessUserSearch');search.value='old';const old=loadAccessUsers(1);search.value='latest';const latest=loadAccessUsers(1);await Promise.all([old,latest]);
  });
  assert.match(await page.locator('#accessUsersBody').textContent(),/latest/);assert(!/old/.test(await page.locator('#accessUsersBody').textContent()));
  assert.equal(await footer.isHidden(),true);assert.deepEqual(errors,[]);
  console.log('Access users: shared server pagination, immediate refresh after real save handler, filter retention and stale-response protection passed.');
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
