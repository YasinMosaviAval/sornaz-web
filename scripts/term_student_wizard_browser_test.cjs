const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
 const browser = await chromium.launch({channel:'msedge',headless:true});
 try {
  for (const lang of ['fa','en']) for (const width of [390,1280]) {
   const page = await browser.newPage({viewport:{width,height:900}}), errors=[];
   page.on('pageerror', error=>errors.push(error.message));
   await page.route('**/*', route=>route.fulfill({json:{success:true,data:{members:[],sessions:[]}}}));
   await page.goto('http://student-wizard.test/');
   await page.setContent('<html lang="'+lang+'" dir="'+(lang==='en'?'ltr':'rtl')+'"><style>.hidden,[hidden]{display:none!important}</style><div id="modalContainer"></div></html>');
   for (const file of ['student-term-schedule','student-templates','students','terms']) await page.addScriptTag({path:'assets/Analytics/js/'+file+'.js'});
   await page.addScriptTag({path:'assets/vendor/tailwind/tailwindcss.js'});
   await page.addStyleTag({path:'assets/theme/theme.css'});
   await page.evaluate(()=>{
    window.studentOrganizations=[{user_id:10,name:'Academy',kind:'academy'}];
    window.studentTermGroups=[{id:1,key:'1',name:'Term',organizationUserId:10,status:'open',cost:100,hasSessions:false,sessionCount:2,scheduleDraft:{recurrence:{weekday:6},startTime:'10:00',endTime:'10:30'}}];
    window.studentClassrooms=[{id:4,name:'Room',organizationUserId:10}];
    window.studentProvinces=[{province_id:1,province_name:'Province'}];
    window.studentCounties=[{county_id:1,province_id:1,county_name:'City'}];
    window.termBranches=[{id:10,kind:'academy',organizationId:100}];
    window.termDiscounts=[{id:6,name:'<img src=x onerror="window.badDiscount=1">',organizationUserId:10,status:'active'},{id:7,name:'Foreign',organizationUserId:20,status:'active'},{id:8,name:'Inactive',organizationUserId:10,status:'inactive'}];
    window.getMemberUserPickerHTML=()=>'<div data-picker>Picker</div>';
    window.assignmentWrites=[];window.alerts=[];window.failAssignment=true;
    window.alert=message=>alerts.push(message);
    window.encodeBranchPayload=data=>btoa(unescape(encodeURIComponent(JSON.stringify(data))));
    window.branchRequest=async(url,options)=>{
     assignmentWrites.push({url,data:JSON.parse(decodeURIComponent(escape(atob(options.payload_b64))))});
     if(failAssignment)throw Error('Fixture assignment failure');
     return {id:44,status:'active'};
    };
    window.filterStudents=()=>{};window.loadTerms=async()=>{};
    window.previewStudentTermSchedule=()=>{};
    window.closeModal=()=>document.getElementById('modalContainer').replaceChildren();
    document.getElementById('modalContainer').innerHTML=getStudentAddModalHTML({assignmentTermId:1,organizationUserId:10,name:'Student name',phone:'09120000001',nationalId:'1000000001',fatherName:'Father',birthDate:'1990-01-01',addresses:[{province:'Province',city:'City',address:'Street'}]});
    initTermStudentWizard(1);initTermStudentWizard(1);
   });
   const next=page.getByRole('button',{name:lang==='en'?'Next':'بعدی',exact:true});
   const previous=page.getByRole('button',{name:lang==='en'?'Previous':'قبلی',exact:true});
   const save=page.getByRole('button',{name:lang==='en'?'Save student':'ذخیره هنرجو',exact:true});
   assert.equal(await page.locator('ol li').count(),5);
   assert.equal(await page.locator('ol [aria-current="step"]').count(),1);
   assert.equal(await page.locator('[data-term-student-step]').count(),5,'Initialization is idempotent');
   assert.equal(await page.locator('#stuInstallmentCount').isVisible(),false);
   await next.click();assert.equal(await page.locator('#stuAddresses').isVisible(),true);
   await next.click();assert.equal(await page.locator('[data-student-first-date]').isVisible(),true);
   assert.equal(await page.locator('[data-assignment-classroom]').isVisible(),false);
   await next.click();await page.locator('[data-assignment-classroom]').selectOption('4');
   assert.equal(await page.locator('#stuInstallmentCount').isVisible(),false);
   await next.click();assert.equal(await page.locator('#stuInstallmentCount').isVisible(),true);
   assert.equal(await page.locator('#stuInstallmentCount').inputValue(),'1');
   assert.equal(await page.locator('#stuInstallmentCount').getAttribute('max'),'2');
   assert.equal(await page.locator('#stuDiscount').inputValue(),'0');
   assert.deepEqual(await page.locator('#stuDiscount option').evaluateAll(options=>options.map(option=>option.value)),['0','6']);
   assert.equal(await page.locator('#stuDiscount img').count(),0);
   await page.locator('#stuInstallmentCount').fill('2');await page.locator('#stuDiscount').selectOption('6');
   await previous.click();assert.equal(await page.locator('[data-assignment-classroom]').inputValue(),'4');
   await next.click();assert.equal(await page.locator('#stuInstallmentCount').inputValue(),'2');
   assert.equal(await page.locator('#stuDiscount').inputValue(),'6');
   assert.equal(await page.locator('#stuName').inputValue(),'Student name');
   assert.equal(await page.evaluate(()=>assignmentWrites.length),0,'Only the fifth step submits');
   await page.locator('#stuInstallmentCount').fill('3');await save.click();
   assert.equal(await page.evaluate(()=>assignmentWrites.length),0,'Out-of-range count cannot submit');
   await page.locator('#stuInstallmentCount').fill('1.5');await save.click();
   assert.equal(await page.evaluate(()=>assignmentWrites.length),0,'Fractional count cannot submit');
   await page.locator('#stuInstallmentCount').fill('2');await save.click();
   await page.waitForFunction(()=>alerts.includes('Fixture assignment failure'));
   assert.equal(await page.locator('[data-term-student-step="5"]').isVisible(),true);
   assert.equal(await page.locator('#stuDiscount').inputValue(),'6','Failed saves retain choices');
   await page.evaluate(async()=>{failAssignment=false;await Promise.all([saveStudent(),saveStudent()]);});
   const writes=await page.evaluate(()=>assignmentWrites);
   assert.equal(writes.length,2,'Retry writes once despite duplicate clicks');
   for(const write of writes){assert.equal(write.url,'/academy/admin/terms/1/student');assert.equal(write.data.installmentCount,2);assert.equal(write.data.discountId,6);assert.equal(write.data.classroomId,4);assert.equal(write.data.name,'Student name');}
   assert.equal(await page.locator('[data-student-term-id]').count(),0);
   assert.equal(await page.evaluate(()=>window.badDiscount),undefined);
   assert.deepEqual(errors,[]);await page.close();
  }
  console.log('Five assignment steps: fa/en, mobile/desktop, retained choices, scoped escaped discounts, integer bounds, final-only real save payload, failed-save retention and duplicate-submit guard passed.');
 }finally{await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});