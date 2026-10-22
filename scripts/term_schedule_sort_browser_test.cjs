const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 const page=await browser.newPage();
 await page.setContent('<style>.hidden{display:none}</style>'+'<main id="mainContent">'+renderPanelView('terms')+'</main>'+tableRuntime()+'<div id="modalContainer"></div>');
 for(const file of ['term-templates.js','terms.js','schedule-templates.js'])await page.addScriptTag({path:'assets/Analytics/js/'+file});
 await page.evaluate(()=>{
  document.getElementById('terms').classList.remove('hidden');
  filteredTerms=[{id:1,name:'Monday late',sessions:[{date:'2030-01-07',startTime:'18:00'}]},{id:2,name:'Saturday early',sessions:[{date:'2030-01-05',startTime:'08:00'}]},{id:3,name:'Sunday draft',scheduleDraft:{recurrence:{weekday:0},startTime:'12:00'},sessions:[]},{id:4,name:'Unscheduled',sessions:[]}];
  renderTermsTable(filteredTerms);
 });
 const order=()=>page.locator('#termsTable [data-term-action="inline-edit"]').evaluateAll(buttons=>buttons.map(button=>Number(button.dataset.termId)));
 await page.locator('[onclick="sortTermsBy(\'days\')"]').click();assert.deepEqual(await order(),[2,3,1,4]);
 await page.locator('[onclick="sortTermsBy(\'days\')"]').click();assert.deepEqual(await order(),[1,3,2,4]);
 await page.locator('[onclick="sortTermsBy(\'times\')"]').click();assert.deepEqual(await order(),[2,3,1,4]);
 await page.locator('[onclick="sortTermsBy(\'times\')"]').click();assert.deepEqual(await order(),[1,3,2,4]);
 for(const lang of ['fa','en']){
  await page.evaluate(lang=>{
   document.documentElement.lang=lang;
   const row={id:51,title:'Session',canWriteTeacherNote:true,attendance:[],teacherNote:'Saved note'};
   document.getElementById('modalContainer').innerHTML='<table><tbody><tr>'+getScheduleRowHTML(row)+'</tr></tbody></table>';
   window.openScheduleAttendanceModal=id=>{window.openedNote=id;document.getElementById('modalContainer').innerHTML=getScheduleAttendanceModalHTML(row);};
  },lang);
  assert.equal(await page.locator('[data-action="teacher-note"]').textContent(),lang==='en'?'Teacher note':'یادداشت استاد');
  await page.locator('[data-action="teacher-note"]').click();
  assert.equal(await page.evaluate(()=>openedNote),51);
  assert.equal(await page.locator('[data-teacher-note]').inputValue(),'Saved note');
  assert.equal(await page.evaluate(()=>getScheduleRowHTML({id:52,attendance:[],canWriteTeacherNote:false}).includes('data-action="teacher-note"')),false);
 }
 console.log('Term day/time ascending and descending sorting, draft/unscheduled terms, and explicit teacher note entry passed.');
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
