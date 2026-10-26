const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']) {
  const page=await browser.newPage(),errors=[],requests=[];let fail=false;
  page.on('pageerror',error=>errors.push(error.message));
  const rooms=Array.from({length:12},(_,i)=>({id:i+1,name:i===0?'<img src=x> Room':`Room ${i+1}`,statusValue:i===11?'unavailable':'available',status:i===11?'خارج از دسترس':'در دسترس',type:'Studio',typeLabel:'Studio',capacity:2,equipment:[],branchId:1,organizationUserId:1}));
  await page.route('**/*',async route=>{
   const url=new URL(route.request().url());
   if(url.pathname==='/academy/admin/classrooms/heatmap'){
    requests.push(url.searchParams.get('week'));
    if(fail)return route.fulfill({status:500,json:{success:false}});
    const isFirst=url.searchParams.get('week')==='2026-10-24';
    return route.fulfill({json:{success:true,data:{weekStart:isFirst?'2026-10-24':'2026-10-31',weekEnd:isFirst?'2026-10-30':'2026-11-06',heatmap:isFirst?[
     {classroomId:1,date:'2026-10-24',startTime:'09:30',endTime:'11:00'},
     {classroomId:1,date:'2026-10-24',startTime:'09:45',endTime:'10:30'},
     {classroomId:11,date:'2026-10-24',startTime:'09:00',endTime:'10:00'},
     {classroomId:12,date:'2026-10-24',startTime:'09:00',endTime:'10:00'},
     {classroomId:99,date:'2026-10-24',startTime:'09:00',endTime:'10:00'}]:[]}}});
   }
   return route.fulfill({json:{success:true,data:{classrooms:rooms,branches:[],classroomTypes:[]}}});
  });
  await page.goto('http://classroom-heatmap.test/');
  await page.clock.install({time:new Date('2026-10-25T12:00:00Z')});
  await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('classrooms',lang)}</main>${['panel-page-header-template','panel-filters-template','panel-organization-tabs-template'].map(name=>renderPanelView(name,lang)).join('')}${tableRuntime()}</html>`);
  await page.evaluate(()=>{document.getElementById('classrooms').classList.remove('hidden');window.renderPanelOrganizationTabs=()=>{};window.matchesOrganizationFilter=(room,branch)=>branch==='all'||!branch||String(room.organizationUserId)===String(branch);window.alert=()=>{};});
  for(const script of ['assets/vendor/echarts/echarts.min.js','assets/Analytics/js/charts.js','assets/Analytics/js/schedule-heatmap.js','assets/Analytics/js/classroom-heatmap.js','assets/Analytics/js/classrooms.js'])await page.addScriptTag({path:script});
  await page.evaluate(async()=>{await loadClassrooms();await loadClassroomHeatmap('2026-10-24');});
  const cell=(day,hour)=>page.evaluate(({day,hour})=>SornazCharts.get('#classroomHeatmap').getOption().series[0].data.find(row=>row[0]===day&&row[1]===hour)?.[2],{day,hour});
  assert.equal(await cell(0,9),2,'Unique available rooms, including beyond page one');
  assert.equal(await cell(0,10),1);assert.equal(await cell(0,11),0,'End is exclusive');
  assert.equal(await page.locator('#classroomHeatmapRoom img').count(),0);
  const tooltip=await page.evaluate(()=>SornazCharts.get('#classroomHeatmap').getOption().tooltip[0].formatter({value:[0,9,2]}));
  assert(tooltip.includes(lang==='en'?'9 without bookings':'۹ بدون رزرو'));
  await page.selectOption('#classroomHeatmapRoom','1');assert.equal(await cell(0,9),1);
  await page.selectOption('#classroomHeatmapRoom','12');assert.equal(await cell(0,9),0,'Unavailable room must not imply free availability');
  await page.selectOption('#classroomHeatmapRoom','');
  const before=requests.length;
  await page.locator('#classroomSearch').fill('Room 11');await page.evaluate(()=>filterClassrooms());
  assert.equal(await cell(0,9),1);assert.equal(requests.length,before,'Local filters must not refetch');
  await page.locator('#classroomSearch').fill('No match');await page.evaluate(()=>filterClassrooms());
  assert.equal(await page.evaluate(()=>SornazCharts.get('#classroomHeatmap').getOption().series[0].data.length),0);
  await page.locator('#classroomSearch').fill('');await page.evaluate(()=>filterClassrooms());
  await page.evaluate(()=>{document.documentElement.dataset.mode='dark';});
  await page.setViewportSize({width:390,height:800});await page.waitForTimeout(100);
  assert((await page.evaluate(()=>SornazCharts.exportPng('#classroomHeatmap'))).startsWith('data:image/png;base64,'));
  await page.evaluate(()=>loadClassroomHeatmap('2026-10-31'));
  assert.equal(await cell(0,9),0);assert.equal(await page.evaluate(()=>SornazCharts.get('#classroomHeatmap').getOption().series[0].data.length),168,'Empty week still shows zero slots');
  fail=true;await page.evaluate(()=>loadClassroomHeatmap('2026-11-07'));
  assert.equal(await page.evaluate(()=>!!SornazCharts.get('#classroomHeatmap')),false,'No stale chart on failure');
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Classroom heatmap: real fa/en chart, unique occupied/free available rooms, all pages, unavailable/foreign exclusion, filters, room selection, empty week, error, dark mode, mobile and PNG passed.');
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
