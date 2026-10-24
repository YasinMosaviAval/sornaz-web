const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']) {
  const page=await browser.newPage(), errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('terms',lang)}</main>${['panel-page-header-template','panel-filters-template','panel-organization-tabs-template'].map(name=>renderPanelView(name,lang)).join('')}${tableRuntime()}</html>`);
  for(const script of ['assets/vendor/echarts/echarts.min.js','assets/Analytics/js/charts.js','assets/Analytics/js/term-lesson-chart.js','assets/Analytics/js/term-templates.js','assets/Analytics/js/terms.js'])await page.addScriptTag({path:script});
  await page.evaluate(()=>{
   document.getElementById('terms').classList.remove('hidden');
   window.matchesOrganizationFilter=(item,branch)=>branch==='all'||String(item.branchId)===String(branch);
   window.termCourses=[{id:1,lessonId:10,instrument:'Piano'},{id:2,lessonId:20,instrument:'Guitar <img src=x onerror=alert(1)>'}];
   window.termClassrooms=[{id:1,deliveryMode:'in_person'},{id:2,deliveryMode:'online'}];
   const base={courseId:1,course:'Course A',name:'Alpha',status:'در حال برگزاری',branchId:1,currency:'IRR',discount:'None',classroom:'Room A',classroomId:1,students:[{id:1},{id:2}],installments:[{}],sessions:[{date:'2030-01-07',deliveryMode:'in_person'},{date:'2030-01-14',deliveryMode:'in_person'}]};
   allTerms=Array.from({length:12},(_,i)=>({...base,id:i+1}));
   allTerms.push({...base,id:13,name:'Beta',status:'پایان یافته',courseId:2,course:'Course B',branchId:2,currency:'USD',discount:'Discount',classroom:'Room B',classroomId:2,installments:[{},{}],sessions:[{date:'2030-01-06',deliveryMode:'online'}]});
   allTerms.push({...base,id:14,name:'Hybrid',sessions:[{deliveryMode:'in_person'},{deliveryMode:'online'}]});
   allTerms.push({...base,id:15,students:[]});
   allTerms.push({...base,id:16,classroomId:0,sessions:[]});
   for(const [id,value] of Object.entries({filterTermStatus:'در حال برگزاری',filterTermCourse:'Course B',filterTermCurrency:'USD',filterTermDiscount:'Discount',filterTermClassroom:'Room B',filterTermInstallmentCount:'2'})) {
    const select=document.getElementById(id);if(!select)throw new Error('Missing filter: '+id);if(![...select.options].some(x=>x.value===value))select.add(new Option(value,value));
   }
   currentTermBranch='all'; filterTerms();
  });
  const data=()=>page.evaluate(()=>SornazCharts.get('#termLessonChart').getOption().series.map(series=>series.data.map(row=>row[1])));
  assert.deepEqual(await data(),[[0,13],[1,1]],'All filtered terms, not the first table page, must be aggregated by lesson');
  assert.equal(await page.locator('#termLessonChart canvas').count()>0,true);
  assert.equal(await page.locator('#termLessonChart img').count(),0);
  assert.match(await page.locator('#termLessonChartSummary').textContent(),/1|۱/);
  for(const [id,value]of Object.entries({filterTermStatus:'در حال برگزاری',filterTermCourse:'Course B',filterTermCurrency:'USD',filterTermDiscount:'Discount',filterTermClassroom:'Room B',filterTermInstallmentCount:'2',filterTermWeekday:'0'})) {
   await page.evaluate(({id,value})=>{document.getElementById(id).value=value;filterTerms();},{id,value});
   assert.deepEqual(await data(),id==='filterTermStatus'?[[13],[1]]:[[0],[1]],id+' must affect chart');
   await page.evaluate(id=>{document.getElementById(id).value='';filterTerms();},id);
  }
  await page.evaluate(()=>{document.getElementById('termSearch').value='Beta';filterTerms();});
  assert.deepEqual(await data(),[[0],[1]]);
  await page.evaluate(()=>{document.getElementById('termSearch').value='';currentTermBranch='2';filterTerms();});
  assert.deepEqual(await data(),[[0],[1]]);
  await page.evaluate(()=>{currentTermBranch='all';document.getElementById('termSearch').value='No match';filterTerms();});
  assert.deepEqual(await data(),[[],[]]);
  await page.evaluate(()=>{document.getElementById('termSearch').value='';filterTerms();document.documentElement.dataset.mode='dark';});
  await page.waitForFunction(()=>SornazCharts.get('#termLessonChart').getOption().textStyle.color==='#e2e8f0');
  assert.match(await page.evaluate(()=>SornazCharts.exportPng('#termLessonChart')),/^data:image\/png/);
  await page.evaluate(()=>{
   window.termCourses=Array.from({length:12},(_,i)=>({id:i+1,lessonId:i+1,instrument:'Lesson '+i}));
   const base=allTerms[0];
   allTerms=Array.from({length:12},(_,i)=>({...base,id:i+1,courseId:i+1}));filterTerms();
  });
  assert.equal(await page.evaluate(()=>SornazCharts.get('#termLessonChart').getOption().dataZoom.length),2);
  await page.setViewportSize({width:390,height:800});
  await page.waitForTimeout(100);
  assert.ok(await page.locator('#termLessonChart').evaluate(el=>SornazCharts.get(el).getWidth()>0));
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Term lesson cylinder chart: real rendering, all filters, entire result set, unique terms, hybrid/unknown modes, empty states, escaping, dark mode, resize and PNG export passed (fa/en).');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
