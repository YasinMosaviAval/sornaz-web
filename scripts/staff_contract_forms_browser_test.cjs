const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
 const browser = await chromium.launch({ channel: 'msedge', headless: true });
 try {
  const page = await browser.newPage();
  const errors=[];page.on('pageerror', e=>errors.push(e.message));
  await page.setContent('<html lang="en"><body><div id="fixture"></div></body></html>');
  await page.evaluate(()=>{
   window.staffTypes=[{value:'teacher',label:'Teacher'},{value:'other',label:'Other'}];
   window.contractCurrencies=[];window.lessonTypes=[{value:1,label:'Piano'},{value:2,label:'Guitar'}];
   window.lessonLevels=[{value:3,label:'Basic'},{value:4,label:'Advanced'}];
   window.staffCatalog={organizations:[{user_id:10,name:'Academy'},{user_id:11,name:'Branch',branch_id:1}],roles:[],
    lessons:[{organization_user_id:10,lesson_id:1,name:'Piano',level_ids:[3]},{organization_user_id:10,lesson_id:2,name:'Guitar',level_ids:[4]},{organization_user_id:11,lesson_id:1,name:'Piano',level_ids:[4]}],
    levels:[{id:3,name:'Basic'},{id:4,name:'Advanced'},{id:5,name:'Unrelated'}]};
  });
  await page.addScriptTag({path:'assets/Analytics/js/teacher-templates.js'});
  for(const mode of ['add','edit','inline']){
   const prefix=mode==='add'?'staff':mode==='edit'?'editStaff':'inlineStaff7';
   await page.evaluate(({mode,prefix})=>{
    const item={id:7,type:'teacher',organizationUserId:10,lessonId:1,levelId:3,lessons:[{type:1,level:3}],price:10};
    document.querySelector('#fixture').innerHTML=mode==='add'?getStaffAddModalHTML():mode==='edit'?getStaffEditModalHTML(item):getStaffInlineEditRowHTML(item);
    updateStaffOrganization(prefix);
   },{mode,prefix});
   const ids=await page.locator('#fixture input[id],#fixture select[id],#fixture textarea[id]').evaluateAll(nodes=>nodes.map(x=>x.id));
   assert(ids.indexOf(prefix+'Organization')<ids.indexOf(prefix+'Type'));
   assert(ids.indexOf(prefix+'ContractTitle')<ids.indexOf(prefix+'StartDate'));
   assert(ids.indexOf(prefix+'StartDate')<ids.indexOf(prefix+'EndDate'));
   assert(ids.indexOf(prefix+'EndDate')<ids.indexOf(prefix+'Currency'));
   assert(await page.evaluate(p=>Boolean(document.getElementById(p+'LessonsBox').compareDocumentPosition(document.getElementById(p+'ContractDescription')) & Node.DOCUMENT_POSITION_FOLLOWING),prefix));
   const type=page.locator('#'+prefix+'LessonsContainer [data-lesson-type]');
   const level=page.locator('#'+prefix+'LessonsContainer [data-lesson-level]');
   if(mode!=='add')assert.equal(await level.inputValue(),'3');
   await type.selectOption('1');
   assert.deepEqual(await level.locator('option').evaluateAll(xs=>xs.map(x=>x.value)),['','3']);
   await type.selectOption('2');
   assert.deepEqual(await level.locator('option').evaluateAll(xs=>xs.map(x=>x.value)),['','4']);
   await page.selectOption('#'+prefix+'Organization','11');
   assert.deepEqual(await type.locator('option').evaluateAll(xs=>xs.map(x=>x.value)),['','1']);
   await type.selectOption('1');
   assert.deepEqual(await level.locator('option').evaluateAll(xs=>xs.map(x=>x.value)),['','4']);
   assert.equal(await page.locator('[data-lesson-row]').evaluate(x=>x.classList.contains('border')),false);
  }
  await page.addScriptTag({path:'assets/Analytics/js/classroom-templates.js'});
  await page.evaluate(()=>document.querySelector('#fixture').innerHTML=getClassroomEquipmentFieldHTML());
  const button=page.locator('#fixture button');assert.equal(await button.textContent(),'');assert.equal(await button.getAttribute('aria-label'),'Remove equipment');
  assert.equal(await button.locator('.fa-trash-alt').count(),1);
  assert.equal(await page.locator('.equip-qty').evaluate(x=>x.parentElement.parentElement===document.querySelector('#fixture button').parentElement.parentElement),true);
  assert.deepEqual(errors,[]);console.log('Staff contract forms: add/edit/inline order, scoped levels, selection preservation and equipment icon passed.');
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
