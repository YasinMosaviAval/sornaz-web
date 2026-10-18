const assert=require('node:assert/strict');
const fs=require('node:fs');
const {spawnSync}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
function markup(staff,owner,language){
 const path='Modules/System/Resources/Views/partials/registration-form.php';
 const keys=[...new Set(fs.readFileSync(path,'utf8').match(/authentication_[a-z_]+/g))].filter(x=>x!=='authentication_array');
 const input=`<?php function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}function locale(){return '${language}';}function trans($k,$v=''){return $v;}function csrf_token(){return 'fixture';}function translations($prefix){return json_decode('${JSON.stringify(Object.fromEntries(keys.map(k=>[k,k])))}',true);}function auth(){return new class {function id(){return ${owner?1:10};}};} require 'Modules/System/Services/RegistrationOtpPolicy.php'; $registrationContext=['staff'=>${staff?'true':'false'}]; require '${path}';`;
 const result=spawnSync('php',[],{input,encoding:'utf8'});assert.equal(result.status,0,result.stderr);return result.stdout;
}
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const language of ['fa','en'])for(const owner of [false,true]){
  const page=await browser.newPage(),errors=[],writes=[],otps=[];page.on('pageerror',e=>errors.push(e.message));
  await page.route('**/*',async route=>{
   const path=new URL(route.request().url()).pathname;
   if(path==='/academy/admin/members/registration-form')return route.fulfill({json:{success:true,data:{html:markup(true,owner,language)}}});
   if(path==='/academy/admin/members/registration-otp'){otps.push(route.request().postData());return route.fulfill({json:{success:true,data:{success:true,expires_in:120}}});}
   if(path==='/register/send-otp')return route.fulfill({json:{success:true,data:{success:true,expires_in:120}}});
   if(path==='/academy/admin/members'){writes.push(JSON.parse(Buffer.from(new URLSearchParams(route.request().postData()).get('payload_b64'),'base64').toString()));return route.fulfill({json:{success:true,data:{member_id:5}}});}
   return route.fulfill({json:{success:true,data:{staff:[],catalog:{organizations:[{user_id:10,name:'Academy',branch_id:null}],roles:[{id:40,name:'academy_manager_1',title:'Manager'},{id:41,name:'academy_receptionist_1',title:'Receptionist'}],currencies:[{id:1,name:'Toman'}],levels:[],lessons:[]}}}});
  });
  await page.goto('http://staff.test/');await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style><div id="modalContainer"></div></html>`);
  await page.evaluate(()=>{window.alert=()=>{};window.closeModal=()=>document.getElementById('modalContainer').replaceChildren();window.adminCsrfToken='fixture';window.encodeBranchPayload=data=>btoa(unescape(encodeURIComponent(JSON.stringify(data))));window.branchRequest=async(url,fields,method='POST')=>{const r=await fetch(url,{method,body:fields?new URLSearchParams(fields):undefined});const p=await r.json();if(!r.ok||p.success===false)throw Error(p.message);return p.data;};});
  for(const name of ['assets/System/js/registration.js','assets/Analytics/js/teachers.js','assets/Analytics/js/teacher-templates.js'])await page.addScriptTag({path:name});
  await page.evaluate(()=>{window.staffCatalog={organizations:[{user_id:10,name:'Academy',branch_id:null}],roles:[{id:40,name:'academy_manager_1',title:'Manager'},{id:41,name:'academy_receptionist_1',title:'Receptionist'}],lessons:[],levels:[]};});
  for(const type of ['manager','receptionist']){
   await page.evaluate(()=>openAddStaffModal());
   for(const [id,value]of Object.entries({Name:'Employee <img src=x>',Phone:'09123330001',NationalId:'2000000001',BirthDate:'1990-01-01',ContractTitle:'Contract',StartDate:'2030-01-01',EndDate:'2031-01-01',Price:'100000'}))await page.locator('#staff'+id).fill(value);
   await page.selectOption('#staffType',type);await page.selectOption('#staffRole',type==='manager'?'40':'41');
   await page.evaluate(async()=>{await saveStaff();});assert.equal(writes.length,0);assert.equal(await page.locator('#staffName').count(),0);assert.equal(await page.locator('[name=full_name]').count(),0);assert.equal(await page.locator('[name=phone]').getAttribute('type'),'hidden');assert.equal(await page.locator('#regMethod').inputValue(),'phone');assert.equal(await page.locator('img').count(),0);
   await page.evaluate(()=>cancelStaffRegistration());assert.equal(writes.length,0);assert.equal(await page.locator('#registerForm').count(),0);
  }
  await page.evaluate(async()=>{await openAddStaffModal();});
  for(const [id,value]of Object.entries({Name:'Employee',Phone:'09123330001',NationalId:'2000000001',BirthDate:'1990-01-01',ContractTitle:'Contract',StartDate:'2030-01-01',EndDate:'2031-01-01',Price:'100000'}))await page.locator('#staff'+id).fill(value);
  await page.selectOption('#staffType','manager');await page.selectOption('#staffRole','40');await page.evaluate(()=>saveStaff());
  await page.locator('[name=username]').fill('fixture_manager');await page.locator('[name=password]').fill('StrongPass123!');await page.locator('[name=password2]').fill('StrongPass123!');await page.locator('[name=terms]').check();
  await page.locator('#regSendOtpBtn').click();
  if(!owner){await page.waitForFunction(()=>!document.getElementById('regOtpStep').classList.contains('hidden'));assert.equal(writes.length,0);for(let i=0;i<6;i++)await page.locator('.reg-otp').nth(i).fill(String(i+1));await page.locator('#regOtpStep button[type=submit]').click();}
  await page.waitForFunction(()=>!document.getElementById('registerForm'));assert.equal(writes.length,1);assert.equal(writes[0].name,'Employee');assert.equal(writes[0].registration.register_method,'phone');assert.equal(writes[0].registration.phone,'09123330001');assert.equal(writes[0].registration.otp,owner?'':'123456');assert.equal(otps.length,owner?0:1);assert.deepEqual(errors,[]);
  // Public signup still uses the same full form, its original method and normal submit contract.
  await page.setContent(markup(false,owner,language));assert.equal(await page.locator('[name=full_name]').count(),1);assert.equal(await page.locator('#regMethod').inputValue(),'email');
  await page.evaluate(()=>initializeRegistrationForm(document.getElementById('registerForm')));
  await page.locator('[name=email]').fill('public@example.test');await page.locator('[name=username]').fill('public_user');await page.locator('[name=password]').fill('StrongPass123!');await page.locator('[name=password2]').fill('StrongPass123!');await page.locator('[name=terms]').check();
  const ready=await page.evaluate(()=>handleRegisterSubmit(document.getElementById('registerForm')));assert.equal(ready,owner);
  if(!owner){await page.waitForFunction(()=>!document.getElementById('regOtpStep').classList.contains('hidden'));for(let i=0;i<6;i++)await page.locator('.reg-otp').nth(i).fill(String(i+1));assert.equal(await page.evaluate(()=>handleRegisterSubmit(document.getElementById('registerForm'))),true);}
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Staff signup browser: manager/receptionist, fa/en, cancel without writes, shared form, supplied identity, default mobile, OTP/owner policy and final contract passed.');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
