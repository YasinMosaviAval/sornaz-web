const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {renderPanelView,tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const language of ['fa','en'])for(const receptionist of [false,true]){
  const page=await browser.newPage(),errors=[],writes=[];page.on('pageerror',e=>errors.push(e.message));let terms=[];
  const courses=[{id:500,name:'Course',organizationUserId:10,organizationKind:'academy',organizationId:100,branchId:null,lesson_id:5,teacher_capacity:2,student_capacity:1}];
  const organizations=[{id:100,user_id:10,kind:'academy',name:'Academy'}];
  const classrooms=[{id:1001,name:'Room',academyId:100,branchId:0},{id:2001,name:'Foreign room',academyId:200,branchId:0}];
  await page.route('**/*',async route=>{
   const path=new URL(route.request().url()).pathname;
   if(path==='/academy/admin/terms/batch'){
    const data=JSON.parse(Buffer.from(new URLSearchParams(route.request().postData()).get('payload_b64'),'base64').toString());writes.push(data);
    terms=Array.from({length:16},(_,i)=>{const start=600+i*30,end=start+30,time=m=>`${String(Math.floor(m/60)).padStart(2,'0')}:${String(m%60).padStart(2,'0')}`;return{id:i+1,name:`${data.name} ${time(start)}–${time(end)}`,courseId:500,course:'Course',branchName:'Academy',branchId:0,classroomId:0,sessionCount:data.sessionCount,status:receptionist?'در انتظار تأیید':'باز',status_code:receptionist?'pending':'open',teachers:[{id:1,name:'Teacher'}],students:[],sessions:[],scheduleDraft:{classroomId:0,startTime:time(start),endTime:time(end),timezoneId:1,recurrence:{weekday:'6'},batchSlot:true}};});
    return route.fulfill({json:{success:true,data:{count:16,ids:terms.map(x=>x.id)}}});
   }
   if(path==='/academy/admin/terms/1/classroom'){
    terms[0].classroomId=1001;terms[0].scheduleDraft.classroomId=1001;return route.fulfill({json:{success:true,data:{id:1,classroomId:1001}}});
   }
   if(path==='/academy/admin/courses/student-options')return route.fulfill({json:{success:true,data:{organizations,terms:[],provinces:[{province_id:1,province_name:'Province'}],counties:[{county_id:1,province_id:1,county_name:'City'}],timezones:[{id:1,name:'Asia/Tehran'}]}}});
   if(path==='/academy/admin/terms/1/student'){
    const data=JSON.parse(Buffer.from(new URLSearchParams(route.request().postData()).get('payload_b64'),'base64').toString());
    assert.equal(data.termId,1);assert.equal(data.organizationUserId,10);
    terms[0].students=[{id:55,name:data.name}];return route.fulfill({json:{success:true,data:{id:55,status:'active'}}});
   }
   if(path==='/academy/admin/terms')return route.fulfill({json:{success:true,data:{terms,classrooms,currencies:[{id:1,name:'Toman'}],timezones:[{id:1,name:'Asia/Tehran'}],discounts:[],members:[{id:1,name:'Teacher',academyId:100,branchId:0,lessonId:5,type:'teacher'}],permissions:{isReceptionist:receptionist}}}});
   if(path==='/academy/admin/courses')return route.fulfill({json:{success:true,data:{courses,organizations}}});
   if(path==='/academy/admin/term-available-times')return route.fulfill({json:{success:true,data:{times:Array.from({length:120},(_,i)=>{const minute=540+i*5;return `${String(Math.floor(minute/60)).padStart(2,'0')}:${String(minute%60).padStart(2,'0')}`;}),timezoneId:1}}});
   return route.fulfill({json:{success:true,data:{version:'fixture'}}});
  });
  await page.goto('http://batch.test/');
  await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('terms',language)}</main><div id="modalContainer"></div>${renderPanelView('panel-page-header-template')}${renderPanelView('panel-organization-tabs-template')}${renderPanelView('panel-filters-template')}${tableRuntime()}</html>`);
  await page.locator('#terms').evaluate(el=>el.classList.remove('hidden'));
  await page.evaluate(()=>{window.alert=()=>{};window.closeModal=()=>document.getElementById('modalContainer').replaceChildren();});
  for(const name of ['panel-organization-filters','term-templates','terms','student-templates','students'])await page.addScriptTag({path:`assets/Analytics/js/${name}.js`});
  await page.evaluate(()=>{window.encodeBranchPayload=data=>btoa(unescape(encodeURIComponent(JSON.stringify(data))));window.branchRequest=async(url,fields)=>{const response=await fetch(url,{method:'POST',body:new URLSearchParams(fields)});return (await response.json()).data;};});
  await page.evaluate(async()=>{await loadTerms();await openAddTermModal();});
  assert.equal(await page.locator('[data-term-batch]').count(),0);
  assert.equal(await page.locator('#termStatus').count(),1);
  await page.evaluate(()=>closeModal());
  await page.locator('button[onclick="openTeacherTermBatchModal()"]').click();
  await page.waitForFunction(()=>document.getElementById('termRangeStart')?.disabled===false);
  assert.equal(await page.locator('select#termRangeStart,select#termRangeEnd').count(),2);
  assert.equal(await page.locator('#termDiscount,#termInstallmentCount,#termStatus').count(),0);
  assert.equal(await page.locator('#termClassroom').getAttribute('required'),null);
  await page.locator('#termName').fill('<img src=x> Piano');await page.selectOption('#termCourse','500');
  assert.equal(await page.locator('#termTeachersContainer select').count(),1);
  await page.selectOption('#termTeachersContainer select','1');
  assert((await page.locator('[data-batch-preview]').textContent()).includes(language==='fa'?'۱۶':'16'));
  await page.selectOption('#termRangeEnd','17:45');assert.equal(await page.locator('[data-term-batch] [type=submit]').isDisabled(),true);
  await page.selectOption('#termRangeEnd','18:00');assert.equal(await page.locator('[data-term-batch] [type=submit]').isEnabled(),true);
  await page.evaluate(async()=>{const pending=saveTermBatch();saveTermBatch();await pending;});
  assert.equal(writes.length,1);assert.equal(writes[0].classroomId,0);assert.equal(writes[0].durationMinutes,30);assert.equal(writes[0].sessionCount,8);assert(/^[a-f0-9]{32}$/.test(writes[0].requestKey));
  for(const field of ['discountId','installmentCount','status'])assert(!(field in writes[0]));
  assert.equal(await page.evaluate(()=>allTerms.length),16);assert.equal(await page.locator('#termsTable tbody tr').count(),10);assert.equal(await page.locator('#termsTable img').count(),0);
  assert((await page.locator('#termsTable tbody').textContent()).includes('10:00'));
  await page.evaluate(()=>openTermClassroomAssignment(1));
  assert.equal(await page.locator('[data-assign-term-classroom]').count(),0);
  await page.locator('button[onclick="openTermStudentAssignment(1)"]').click();
  await page.waitForSelector('[data-student-term-id="1"]');
  assert.equal(await page.locator('#stuOrganization').getAttribute('type'),'hidden');
  assert.equal(await page.locator('#stuInstrument,#stuAvailabilityWrap').count(),0);
  await page.locator('#stuName').fill('Student');await page.locator('#stuNationalId').fill('1234567890');await page.locator('#stuFatherName').fill('Father');await page.locator('#stuBirthDate').fill('1990-01-01');await page.locator('#stuPhone').fill('09123456789');
  await page.locator('#modalContainer').getByRole('button',{name:language==='fa'?'بعدی':'Next',exact:true}).click();
  await page.selectOption('.student-address-province','Province');await page.selectOption('.student-address-city','City');await page.locator('.student-address-text').fill('Address');
  await page.locator('#modalContainer').getByRole('button',{name:language==='fa'?'بعدی':'Next',exact:true}).click();
  await page.locator('#modalContainer').getByRole('button',{name:language==='fa'?'بعدی':'Next',exact:true}).click();
  await page.evaluate(async()=>{const pending=saveStudent();saveStudent();await pending;});
  assert.equal(await page.locator('button[onclick="openTermStudentAssignment(1)"]').count(),0);
  await page.evaluate(()=>openTermClassroomAssignment(1));
  assert.deepEqual(await page.locator('[data-assign-term-classroom] option').evaluateAll(nodes=>nodes.map(x=>x.value)),['','1001']);
  await page.selectOption('[data-assign-term-classroom] select','1001');await page.locator('[data-assign-term-classroom] [type=submit]').click();
  await page.waitForFunction(()=>allTerms[0].classroomId===1001);

  await page.evaluate(()=>{allTerms[1].status_code='ongoing';openTermStudentAssignment(2);});assert.equal(await page.locator('[data-student-term-id]').count(),0);
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Term batch UI: fa/en, owner/receptionist forms, 16-slot preview, optional room, hidden financial/status fields, duplicate-click protection, table pagination, safe titles and later classroom assignment passed.');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
