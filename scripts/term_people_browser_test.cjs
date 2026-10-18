const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const language of ['fa','en']){
  const page=await browser.newPage(),errors=[];page.on('pageerror',error=>errors.push(error.message));
  await page.setContent(`<html lang="${language}"><div id="modalContainer"></div></html>`);
  await page.addScriptTag({path:'assets/Analytics/js/term-templates.js'});await page.addScriptTag({path:'assets/Analytics/js/terms.js'});
  await page.evaluate(()=>{
   window.termBranches=[{id:10,organizationId:100,kind:'academy'},{id:11,organizationId:101,academyId:100,kind:'branch'},{id:12,organizationId:102,academyId:100,kind:'branch'}];
   window.termCourses=[{id:1,organizationUserId:11,organizationId:101,organizationKind:'branch',branchId:101,lessonId:5,teacher_capacity:2,student_capacity:1},{id:2,organizationUserId:10,organizationId:100,organizationKind:'academy',branchId:null,lessonId:5,teacher_capacity:2,student_capacity:1}];
   window.termClassrooms=[{id:1,name:'Shared academy room',branchId:0,academyId:100},{id:2,name:'Branch room',branchId:101,academyId:100},{id:3,name:'Other branch room',branchId:102,academyId:100},{id:4,name:'Second branch room',branchId:101,academyId:100}];
   window.termMembers=[{id:1,name:'Branch teacher',branchId:101,academyId:100,type:'teacher',lessonId:5},{id:2,name:'<img src=x> Academy teacher',branchId:0,academyId:100,type:'teacher',lessonId:5},{id:3,name:'Other branch teacher',branchId:102,academyId:100,type:'teacher',lessonId:5},{id:4,name:'Foreign teacher',branchId:0,academyId:200,type:'teacher',lessonId:5},{id:5,name:'Other lesson',branchId:101,academyId:100,type:'teacher',lessonId:6}];
   window.termCurrencies=[];window.termDiscounts=[];window.termTimezones=[];window.termPermissions={};
   window.refreshAllTermSessionAvailability=()=>{};window.syncTermDateAvailability=()=>{};
  });
  for(const mode of ['add','edit','inline'])for(const courseId of [1,2]){
   const prefix=mode==='add'?'':mode==='edit'?'editTerm':'inlineTerm9';const field=name=>'#'+(prefix||'term')+name;
   await page.evaluate(({mode,courseId})=>{
    const item={id:9,courseId,organizationUserId:courseId===1?11:10,sessionCount:1,teachers:[{id:courseId===1?1:2,name:'Selected teacher'}],sessions:[]};
    document.getElementById('modalContainer').innerHTML=mode==='add'?getTermAddModalHTML():mode==='edit'?getTermEditModalHTML(item):getTermInlineEditRowHTML(item);
   },{mode,courseId});
   await page.selectOption(field('Branch'),courseId===1?'11':'10');
   await page.selectOption(field('Course'),String(courseId));
   await page.evaluate(prefix=>refreshTermCourse(prefix),prefix);
   const teacher=page.locator(field('TeachersContainer')+' select').first();
   await teacher.selectOption(courseId===1?'1':'2');
   for(const room of (courseId===1?['2','4']:['2','1'])){
    await page.locator(field('Classroom')).evaluate(el=>el.disabled=false);
    await page.selectOption(field('Classroom'),room);
    assert.equal(await teacher.inputValue(),courseId===1?'1':'2',`${mode}: room selection retains eligible teacher`);
    const ids=await teacher.locator('option').evaluateAll(options=>options.map(x=>x.value));
    assert(!ids.includes('4')&&!ids.includes('5'));
    if(courseId===1)assert(!ids.includes('2')&&!ids.includes('3'));
   }
   assert.equal(await page.locator(field('TeachersContainer')+' img').count(),0);
   if(courseId===2){
    await page.selectOption(field('Classroom'),'2');
    const second=page.locator(field('TeachersContainer')+' select').nth(1);
    await second.selectOption('1');
    await page.selectOption(field('Classroom'),'3');
    assert.equal(await teacher.inputValue(),'2');
    assert.equal(await second.inputValue(),'','Only an ineligible venue-specific selection is cleared');
   }
  }
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Term people UI: add/edit/inline, fa/en, course-scoped teachers retained across shared and branch rooms, foreign/other-lesson exclusion and escaped names passed.');
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
