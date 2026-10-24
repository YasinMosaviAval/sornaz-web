const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']) {
  const page=await browser.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('teachers',lang)}</main>${['panel-page-header-template','panel-filters-template','panel-organization-tabs-template'].map(name=>renderPanelView(name,lang)).join('')}${tableRuntime()}</html>`);
  await page.evaluate(()=>{
   window.renderPanelOrganizationTabs=()=>{};
   window.matchesOrganizationFilter=(row,branch)=>!branch||branch==='all'||String(row.organizationUserId)===String(branch);
   window.fetch=async()=>({ok:true,text:async()=>JSON.stringify({success:true,data:{version:'fixture'}}),json:async()=>({success:true,data:{version:'fixture'}})});
   window.alert=()=>{};document.getElementById('teachers').classList.remove('hidden');
  });
  for(const script of ['assets/vendor/echarts/echarts.min.js','assets/Analytics/js/charts.js','assets/Analytics/js/contract-type-charts.js','assets/Analytics/js/teacher-templates.js','assets/Analytics/js/teachers.js'])await page.addScriptTag({path:script});
  await page.evaluate(()=>{
   const base={type:'teacher',typeLabel:'Teacher',name:'Alice',contractTitle:'Teaching',status:'فعال',currency:'IRR',organizationUserId:1,price:0,lessons:[{type:1}],contractId:1};
   allStaff=Array.from({length:12},(_,i)=>({...base,id:i+1,user_id:101+i,contractId:i+1,contracts:[{type:'teacher',contractId:i+1},{type:'teacher',contractId:i+1}]}));
   allStaff[0].contracts.push({type:'manager',contractId:100});
   allStaff.push({...base,id:13,user_id:999,name:'Bob',type:'receptionist',contractId:13,organizationUserId:2,status:'غیرفعال',currency:'USD',lessons:[{type:2}]});
   allStaff.push({...allStaff[0],id:99,contracts:[{type:'teacher',contractId:99}]});
   allStaff.push({...base,id:14,user_id:1000,type:'waiting',contractId:0});
   for(const [id,value]of Object.entries({filterStaffCurrency:'USD',filterStaffLesson:'2'})){const select=document.getElementById(id);select.add(new Option(value,value));}
   currentStaffBranch='all';filterStaff();
  });
  const data=()=>page.evaluate(()=>SornazCharts.get('#contractTypePie').getOption().series[0].data.map(row=>[row.id,row.value]));
  assert.deepEqual(await data(),[['manager',1],['receptionist',1],['teacher',12]],'Count people by type across every page without repeated contracts/memberships');
  assert.equal(await page.locator('#contractTypePie canvas,#contractTypeDonut canvas').count(),2);
  assert.deepEqual(await page.evaluate(()=>SornazCharts.get('#contractTypePie').getOption().series[0].data),await page.evaluate(()=>SornazCharts.get('#contractTypeDonut').getOption().series[0].data));
  const radii=await page.evaluate(()=>['contractTypePie','contractTypeDonut'].map(id=>SornazCharts.get('#'+id).getOption().series[0].radius));
  assert.notDeepEqual(radii[0],radii[1]);
  for(const [id,value,expected]of [['filterStaffType','teacher',[['teacher',12]]],['filterStaffType','manager',[['manager',1]]],['filterStaffStatus','غیرفعال',[['receptionist',1]]],['filterStaffCurrency','USD',[['receptionist',1]]],['filterStaffLesson','2',[['receptionist',1]]]]){
   await page.evaluate(({id,value})=>{document.getElementById(id).value=value;filterStaff();},{id,value});
   assert.deepEqual(await data(),expected,id);
   await page.evaluate(id=>{document.getElementById(id).value='';filterStaff();},id);
  }
  await page.evaluate(()=>{currentStaffBranch='2';filterStaff();});assert.deepEqual(await data(),[['receptionist',1]]);
  await page.evaluate(()=>{currentStaffBranch='all';document.getElementById('staffSearch').value='Bob';filterStaff();});assert.deepEqual(await data(),[['receptionist',1]]);
  await page.evaluate(()=>{document.getElementById('staffSearch').value='No match';filterStaff();});assert.deepEqual(await data(),[]);
  await page.evaluate(()=>{document.getElementById('staffSearch').value='';filterStaff();document.documentElement.dataset.mode='dark';});
  await page.waitForFunction(()=>SornazCharts.get('#contractTypePie').getOption().textStyle.color==='#e2e8f0');
  await page.setViewportSize({width:390,height:800});await page.waitForTimeout(100);
  assert.ok(await page.locator('#contractTypeDonut').evaluate(el=>SornazCharts.get(el).getWidth()>0));
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Contract charts: real pie/donut rendering, unique people per type, multiple contracts, full result sets, all staff filters, selected type, empty state, dark mode and resize passed (fa/en).');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
