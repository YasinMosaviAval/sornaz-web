const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']) {
  const page=await browser.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('students',lang)}</main>${['panel-page-header-template','panel-filters-template','panel-organization-tabs-template'].map(name=>renderPanelView(name,lang)).join('')}${tableRuntime()}</html>`);
  for(const script of ['assets/vendor/echarts/echarts.min.js','assets/Analytics/js/charts.js','assets/Analytics/js/term-lesson-chart.js','assets/Analytics/js/student-lesson-chart.js','assets/Analytics/js/student-templates.js','assets/Analytics/js/students.js'])await page.addScriptTag({path:script});
  await page.evaluate(()=>{
   document.getElementById('students').classList.remove('hidden');
   window.matchesOrganizationFilter=(item,branch)=>branch==='all'||String(item.organizationUserId)===String(branch);
   window.renderPanelOrganizationTabs=()=>{};
   const reg={termId:1,instrument:'Term A',lessonId:10,lessonName:'Piano',level:'Level A',deliveryModes:['in_person']};
   window.chartMembers=Array.from({length:12},(_,i)=>({id:i+1,user_id:i+101,type:'student',name:'Alice '+i,birthDate:'2000-01-01',organizationUserId:1,financial:'Settled',remaining:0,termRegistrations:[reg,{...reg,termId:2}]}));
   chartMembers[0].termRegistrations.push({...reg,termId:3,deliveryModes:['online']});
   chartMembers.push({id:13,user_id:999,type:'student',name:'Bob',birthDate:'2015-01-01',organizationUserId:2,financial:'Debt',remaining:7,termRegistrations:[{termId:4,instrument:'Term B',lessonId:20,lessonName:'Guitar <img src=x>',level:'Level B',deliveryModes:['online']}]});
   chartMembers.push({...chartMembers[0],id:99,termRegistrations:[reg]});
   chartMembers.push({id:14,type:'student',name:'Unscheduled',organizationUserId:1,deliveryModes:[],termRegistrations:[]});
   window.fetch=async()=>({ok:true,json:async()=>({success:true,data:{version:'fixture',members:chartMembers}})});
   for(const [id,value]of Object.entries({filterStudentLevel:'Level B',filterStudentInstrument:'Term B',filterStudentFinancial:'Debt',filterStudentRemaining:'6+'})){
    let select=document.getElementById(id);if(!select){select=document.createElement('select');select.id=id;select.add(new Option('All',''));document.getElementById('students').append(select);}select.add(new Option(value,value));
   }
   dispatchEvent(new CustomEvent('academy-data-loaded',{detail:{members:chartMembers}}));
  });
  const data=()=>page.evaluate(()=>SornazCharts.get('#studentLessonChart').getOption().series.map(series=>series.data.map(row=>row[1])));
  assert.deepEqual(await data(),[[0,12],[1,1]],'Distinct students per lesson/mode across every filtered registration and page');
  assert.ok(await page.locator('#studentLessonChart canvas').count()>0);
  assert.equal(await page.locator('#studentLessonChart img').count(),0);
  for(const [id,value]of Object.entries({filterStudentAgeGroup:'child',filterStudentLevel:'Level B',filterStudentInstrument:'Term B',filterStudentFinancial:'Debt',filterStudentRemaining:'6+'})){
   await page.evaluate(({id,value})=>{document.getElementById(id).value=value;filterStudents();},{id,value});
   assert.deepEqual(await data(),[[0],[1]],id);
   await page.evaluate(id=>{document.getElementById(id).value='';filterStudents();},id);
  }
  await page.evaluate(()=>{document.getElementById('studentSearch').value='Bob';filterStudents();});
  assert.deepEqual(await data(),[[0],[1]]);
  await page.evaluate(()=>{document.getElementById('studentSearch').value='';filterStudentsByBranch('2');});
  assert.deepEqual(await data(),[[0],[1]]);
  await page.evaluate(()=>{document.getElementById('studentSearch').value='Missing';filterStudents();});
  assert.deepEqual(await data(),[[],[]]);
  await page.evaluate(()=>{document.getElementById('studentSearch').value='';filterStudents();document.documentElement.dataset.mode='dark';});
  await page.waitForFunction(()=>SornazCharts.get('#studentLessonChart').getOption().textStyle.color==='#e2e8f0');
  assert.match(await page.evaluate(()=>SornazCharts.exportPng('#studentLessonChart')),/^data:image\/png/);
  await page.setViewportSize({width:390,height:800});await page.waitForTimeout(100);
  assert.ok(await page.locator('#studentLessonChart').evaluate(el=>SornazCharts.get(el).getWidth()>0));
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Student cylinder chart: real fa/en rendering, full filtered results, distinct people across terms/memberships, hybrid/unknown modes, all filters, search, organization, empty state, dark mode, resize and PNG passed.');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
