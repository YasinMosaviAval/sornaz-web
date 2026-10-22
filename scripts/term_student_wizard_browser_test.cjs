const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH);
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
const page=await browser.newPage();
await page.setContent('<html lang="fa"><style>.hidden{display:none}</style><div id="modalContainer"></div></html>');
for(const file of ['student-term-schedule.js','student-templates.js','terms.js'])await page.addScriptTag({path:'assets/Analytics/js/'+file});
for(const lang of ['fa','en']){
await page.evaluate(lang=>{
 document.documentElement.lang=lang;
 window.studentOrganizations=[{user_id:10,name:'Academy',kind:'academy'}];
 window.studentTermGroups=[{id:1,key:'1',name:'Term',organizationUserId:10,status:'open',hasSessions:false,sessionCount:2,scheduleDraft:{recurrence:{weekday:6},startTime:'10:00',endTime:'10:30'}}];
 window.studentClassrooms=[{id:4,name:'Room',organizationUserId:10}];
 window.validateTermStudentStep=()=>true;
 window.getMemberUserPickerHTML=()=>'<div data-picker>Picker</div>';
 window.saveStudent=()=>{window.saved=true;};window.saved=false;
 document.getElementById('modalContainer').innerHTML=getStudentAddModalHTML({assignmentTermId:1,organizationUserId:10});
 initTermStudentWizard(1);
},lang);
const next=page.getByRole('button',{name:lang==='en'?'Next':'بعدی',exact:true});
assert.equal(await page.locator('ol [aria-current="step"]').count(),1);
await page.locator('#stuName').fill('Student name');
await next.click();assert.equal(await page.locator('#stuAddresses').isVisible(),true);assert.equal(await page.locator('#stuName').isVisible(),false);
await next.click();assert.equal(await page.locator('[data-student-first-date]').isVisible(),true);assert.equal(await page.locator('[data-assignment-classroom]').isVisible(),false);
await next.click();await page.locator('[data-assignment-classroom]').selectOption('4');
await page.getByRole('button',{name:lang==='en'?'Previous':'قبلی',exact:true}).click();
assert.equal(await page.evaluate(()=>readStudentTermSchedule('stu').classroomId),4);
await next.click();assert.equal(await page.locator('#stuName').inputValue(),'Student name');
assert.equal(await page.evaluate(()=>saved),false);
await page.getByRole('button',{name:lang==='en'?'Save student':'ذخیره هنرجو',exact:true}).click();assert.equal(await page.evaluate(()=>saved),true);
}
console.log('Four assignment steps: Persian/English, single active indicator, separated fields, retained identity/classroom and final-only submit passed.');
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
