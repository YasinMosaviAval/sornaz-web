const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']){
  const page=await browser.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.setContent('<html lang="'+lang+'"><style>.hidden{display:none}</style><main id="mainContent">'+renderPanelView('students',lang)+'</main><div id="modalContainer"></div>'+tableRuntime());
  await page.addScriptTag({path:'assets/Analytics/js/panel-organization-filters.js'});
  await page.addScriptTag({path:'assets/Analytics/js/student-templates.js'});await page.addScriptTag({path:'assets/Analytics/js/students.js'});
  await page.evaluate(()=>{
   window.fixtureStudents=[{id:11,type:'student',name:'Alice',organizationUserId:10,branchId:0,branch:'Academy',termId:2,termRegistrations:[{termId:1,termKey:'1',instrument:'Term A',level:'Level A',teacher:'Teacher A',teacherNotes:[{text:'Note A'}]},{termId:2,termKey:'2',instrument:'Term B',level:'Level B',teacher:'Teacher B',teacherNotes:[{text:'Note B'}]}]},{id:12,type:'student',name:'Bob',organizationUserId:10,branchId:0,branch:'Academy',termId:2,termRegistrations:[{termId:2,termKey:'2',instrument:'Term B',level:'Level B',teacher:'Teacher B'}]}];
   window.fetch=async()=>({ok:true,json:async()=>({success:true,data:{version:'fixture',members:fixtureStudents}})});
   window.studentOrganizations=[{id:100,user_id:10,kind:'academy',name:'Academy'}];
   window.studentTermGroups=[1,2].map(id=>({id,key:String(id),name:'Term '+(id===1?'A':'B'),organizationUserId:10,status:'open',levels:[]}));
   window.ensureStudentFormOptions=async()=>{};
   document.getElementById('students').classList.remove('hidden');
   dispatchEvent(new CustomEvent('academy-data-loaded',{detail:{members:fixtureStudents}}));
  });
  assert.equal(await page.locator('#studentsTable [onclick^="viewStudent"]').count(),3,'Each student/term pair has its own row');
  assert.equal(await page.locator('#studentsTable [onclick="viewStudent(11,1)"]').count(),1);
  assert.equal(await page.locator('#studentsTable [onclick="viewStudent(11,2)"]').count(),1);
  assert.equal(await page.locator('#studentsTable [onclick="viewStudent(12,2)"]').count(),1,'Group term has a row for each student');
  await page.locator('[onclick="viewStudent(11,2)"]').click();
  assert((await page.locator('#modalContainer').textContent()).includes('Note B'));
  assert(!(await page.locator('#modalContainer').textContent()).includes('Note A'),'Detail dialog belongs to the selected registration');
  await page.evaluate(()=>document.getElementById('modalContainer').replaceChildren());
  await page.locator('[onclick="toggleStudentInlineEdit(11,2)"]').click();
  assert.equal(await page.locator('#inlineStu11_2Instrument').inputValue(),'2','Inline edit selects the clicked term');
  assert.equal(await page.locator('#inlineStu11_1Instrument').count(),0,'Other term is not expanded');
  await page.evaluate(()=>dispatchEvent(new CustomEvent('academy-data-loaded',{detail:{members:fixtureStudents}})));
  assert.equal(await page.locator('#studentsTable [onclick^="viewStudent"]').count(),3,'Live refresh retains per-term rows');
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Student term rows: multiple terms, group enrollment, exact detail/edit selection and live refresh passed (fa/en).');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
