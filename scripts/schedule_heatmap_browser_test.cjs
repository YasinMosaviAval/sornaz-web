const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']) {
  const page=await browser.newPage(),errors=[],requests=[];let fail=false,delay=false;
  page.on('pageerror',e=>errors.push(e.message));
  const base={termId:1,title:'Piano',lesson:'Piano',student:'Alice',teacher:'Teacher',date:'2026-10-17',startTime:'09:30',endTime:'11:00',branchId:1,academyId:1,classroomId:1,classroom:'Room A',lessonId:1,mode:'in_person',bookingStatus:'approved'};
  const sessions=Array.from({length:12},(_,i)=>({...base,id:i+1}));
  sessions.push({...base,id:13,date:'2026-10-18',title:'Guitar',lessonId:2,classroomId:2,mode:'online',bookingStatus:'pending',startTime:'10:00',endTime:'11:30'});
  sessions.push({...base,id:14,branchId:2,lessonId:2,classroomId:3,bookingStatus:'pending',startTime:'12:00',endTime:'13:00'});
  sessions.push({...base,id:15,date:'2026-10-24'});
  await page.route('**/*',async route=>{
   const url=new URL(route.request().url()),q=url.searchParams;requests.push(url);
   if(url.pathname.includes('week-bounds'))return route.fulfill({json:{success:true,data:{firstDate:'2026-10-17',lastDate:'2026-10-24'}}});
   if(!url.pathname.includes('class-schedules'))return route.fulfill({body:'<html></html>',contentType:'text/html'});
   const rows=sessions.filter(row=>(!q.get('branch')||String(row.branchId)===q.get('branch'))&&(!q.get('mode')||row.mode===q.get('mode'))&&(!q.get('status')||row.bookingStatus===q.get('status'))&&(!q.get('lesson')||String(row.lessonId)===q.get('lesson'))&&(!q.get('classroom')||String(row.classroomId)===q.get('classroom'))&&(!q.get('timeFrom')||row.endTime>=q.get('timeFrom'))&&(!q.get('timeTo')||row.startTime<=q.get('timeTo'))&&(!q.has('day')||String((new Date(row.date+'T12:00:00Z').getUTCDay()+6)%7)===q.get('day'))&&(!q.get('search')||row.title.toLowerCase().includes(q.get('search').toLowerCase()))&&(!q.get('dateFrom')||row.date>=q.get('dateFrom'))&&(!q.get('dateTo')||row.date<=q.get('dateTo')));
   if(q.get('summary')==='heatmap'){
    if(delay)await new Promise(resolve=>setTimeout(resolve,250));
    if(fail)return route.fulfill({status:500,json:{success:false}});
    const groups=new Map();rows.forEach(row=>{const key=row.date+row.startTime+row.endTime;const value=groups.get(key)||{date:row.date,startTime:row.startTime,endTime:row.endTime,count:0};value.count++;groups.set(key,value);});
    return route.fulfill({json:{success:true,data:{heatmap:[...groups.values()]}}});
   }
   const pageNo=Number(q.get('page')||1),per=Number(q.get('perPage')||10);
   return route.fulfill({json:{success:true,data:{schedules:rows.slice((pageNo-1)*per,pageNo*per),total:rows.length,page:pageNo,perPage:per,organizations:[{id:1,name:'A'},{id:2,name:'B'}],classrooms:[{id:1,branchId:1,name:'Room A'},{id:2,branchId:1,name:'Room B'},{id:3,branchId:2,name:'Room C'}],lessons:[{id:1,name:'Piano'},{id:2,name:'Guitar'}],timeRanges:[]}}});
  });
  await page.goto('http://heatmap.test/');await page.clock.install({time:new Date('2026-10-18T12:00:00Z')});
  await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('schedules',lang)}</main>${['panel-page-header-template','panel-filters-template','panel-organization-tabs-template'].map(name=>renderPanelView(name,lang)).join('')}${tableRuntime()}</html>`);
  await page.evaluate(()=>{document.getElementById('schedules').classList.remove('hidden');window.alert=()=>{};});
  for(const script of ['assets/vendor/echarts/echarts.min.js','assets/Analytics/js/charts.js','assets/Analytics/js/schedule-heatmap.js','assets/Analytics/js/schedule-templates.js','assets/Analytics/js/schedules.js'])await page.addScriptTag({path:script});
  await page.waitForFunction(()=>window.SornazCharts?.get('#scheduleHeatmap'));
  const cell=(day,hour)=>page.evaluate(({day,hour})=>SornazCharts.get('#scheduleHeatmap').getOption().series[0].data.find(row=>row[0]===day&&row[1]===hour)?.[2]||0,{day,hour});
  assert.equal(await cell(0,9),12);assert.equal(await cell(0,10),12);assert.equal(await cell(1,11),1);
  const startRequests=requests.length;
  for(const [id,value,day,hour,expected]of [['filterScheduleType','online',1,10,1],['filterScheduleStatus','pending',0,9,0],['filterScheduleInstrument','2',0,12,1],['filterScheduleClassroom','2',1,10,1],['filterScheduleDay','6',0,9,0],['filterScheduleTimeFrom','12:00',0,12,1],['filterScheduleTimeTo','09:30',1,10,0]]) {
   await page.evaluate(({id,value})=>{const field=document.getElementById(id);if(![...field.options].some(o=>o.value===value))field.add(new Option(value,value));field.value=value;filterSchedules();},{id,value});
   assert.equal(await cell(day,hour),expected,id);
   await page.evaluate(id=>{document.getElementById(id).value='';filterSchedules();},id);
  }
  await page.evaluate(()=>filterSchedulesByBranch('2'));assert.equal(await cell(0,9),0);assert.equal(await cell(0,12),1);
  await page.evaluate(()=>{filterSchedulesByBranch('all');document.getElementById('scheduleSearch').value='Guitar';filterSchedules();});assert.equal(await cell(0,9),0);
  assert.equal(requests.length,startRequests,'Weekly filtering must reuse loaded rows');
  await page.evaluate(()=>{document.getElementById('scheduleSearch').value='';filterSchedules();setSchedulesView('list');});
  await page.waitForFunction(()=>document.getElementById('scheduleHeatmapSummary').textContent.includes('15')||document.getElementById('scheduleHeatmapSummary').textContent.includes('۱۵'));
  assert.equal(await cell(0,9),13,'All table pages must count');
  await page.evaluate(()=>{document.getElementById('filterScheduleType').value='online';filterSchedules();});
  await page.waitForFunction(()=>SornazCharts.get('#scheduleHeatmap')?.getOption().series[0].data.find(row=>row[0]===0&&row[1]===9)?.[2]===0);
  assert.equal(await cell(1,10),1);
  assert.ok(requests.some(url=>url.searchParams.get('summary')==='heatmap'&&url.searchParams.get('mode')==='online'));
  await page.evaluate(()=>{document.getElementById('filterScheduleType').value='';filterSchedules();});
  await page.waitForFunction(()=>SornazCharts.get('#scheduleHeatmap')?.getOption().series[0].data.find(row=>row[0]===0&&row[1]===9)?.[2]===13);
  await page.evaluate(()=>{document.documentElement.dataset.mode='dark';});
  await page.waitForFunction(()=>SornazCharts.get('#scheduleHeatmap').getOption().textStyle.color==='#e2e8f0');
  assert.match(await page.evaluate(()=>SornazCharts.exportPng('#scheduleHeatmap')),/^data:image\/png/);
  delay=true;
  await page.evaluate(()=>{document.getElementById('scheduleSearch').value='Piano';filterSchedules();});
  await page.waitForTimeout(30);
  await page.evaluate(()=>{document.getElementById('scheduleSearch').value='';setSchedulesView('weekly');});
  await page.waitForTimeout(350);assert.equal(await cell(0,9),12,'Late list summary must not replace the weekly chart');
  await page.evaluate(()=>{document.getElementById('scheduleSearch').value='No match';filterSchedules();});
  assert.equal(await page.evaluate(()=>SornazCharts.get('#scheduleHeatmap').getOption().series[0].data.length),0);
  await page.setViewportSize({width:390,height:800});await page.waitForTimeout(100);
  assert.ok(await page.locator('#scheduleHeatmap').evaluate(el=>SornazCharts.get(el).getWidth()>0));
  fail=true;
  await page.evaluate(()=>{document.getElementById('scheduleSearch').value='Failure fixture';setSchedulesView('list');});
  await page.waitForFunction(()=>/could not|ناموفق/.test(document.getElementById('scheduleHeatmapSummary').textContent));
  assert.equal(await page.evaluate(()=>Boolean(SornazCharts.get('#scheduleHeatmap'))),false,'Failed refresh must not show stale counts');
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Schedule heatmap: real fa/en rendering, overlapping hours, every weekly filter, zero additional weekly fetches, all-page server summary, filtered requests, view races, empty state, dark mode, resize and PNG passed.');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
