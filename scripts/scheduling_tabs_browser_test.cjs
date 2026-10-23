const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {renderPanelView, tableRuntime} = require('./panel-table-fixture.cjs');
(async () => {
 const browser = await chromium.launch({channel:'msedge', headless:true});
 try {
  for (const language of ['fa','en']) {
   const page = await browser.newPage(), errors = [], writes = [];
   page.on('pageerror', error => errors.push(error.message));
   const organizations = [{user_id:10,name:'Academy'},{user_id:11,branch_id:101,name:'Branch'}];
   const members = [{id:100,user_id:1000,name:'Academy employee',organizationUserId:10},{id:200,user_id:2000,name:'<img src=x> Branch employee',organizationUserId:11}];
   const exceptions = [{id:1,name:'Organization holiday',targetValue:'organization:10',organizationUserId:10,membershipId:0,type:'closed',branchName:'Academy',status:'فعال'}, {id:2,name:'Employee leave',targetValue:'member:200',organizationUserId:11,membershipId:200,type:'vacation',branchName:'Branch',status:'فعال'}];
   await page.route('**/*', async route => {
    if (route.request().method() === 'POST') {
     writes.push(JSON.parse(Buffer.from(new URLSearchParams(route.request().postData()).get('payload_b64'),'base64').toString()));
     return route.fulfill({json:{success:true,data:{ids:[3]}}});
    }
    return route.fulfill({json:{success:true,data:{staff_catalog:{organizations},members,exceptions,version:'fixture'}}});
   });
   await page.goto('http://schedule.test/');
   const ids = ['branches','teachers','member-schedules','availabilities','scheduling-rules','availability-exceptions','schedules'];
   await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style><main id="mainContent">${ids.map(id=>renderPanelView(id,language)).join('')}</main><div id="modalContainer"></div><span id="panelPageTitle"></span>${tableRuntime()}</html>`);
   await page.evaluate(()=>{window.renderPanelOrganizationTabs=()=>{};window.matchesOrganizationFilter=()=>true;window.alert=()=>{};});
   for (const script of ['admin','availability-exception-templates','availability-exceptions']) await page.addScriptTag({path:`assets/Analytics/js/${script}.js`});
   await page.evaluate(()=>loadHolidayLeaves());
   await page.evaluate(()=>showOrganizationPage('branches'));
   assert.equal(await page.locator('#branches .panel-page-tabs button').count(),4);
   await page.locator('#branches .panel-page-tabs button').last().click();
   assert.equal(await page.locator('#holidayLeavesTable tbody tr').count(),1);
   assert((await page.locator('#holidayLeavesTable tbody').textContent()).includes('Organization holiday'));
   await page.locator('button[onclick="openAddHolidayLeaveModal()"]').click();
   assert.equal(await page.locator('#hlMember').isVisible(),false);
   assert(!(await page.locator('#hlType option').evaluateAll(nodes=>nodes.map(x=>x.value))).includes('vacation'));
   await page.selectOption('#hlBranch','10'); await page.locator('#hlDate').fill('2026-11-01'); await page.locator('#hlAllDay').check();
   await page.locator('button[onclick="saveHolidayLeave()"] ').click();
   await page.waitForFunction(()=>!document.querySelector('#hlDate'));
   assert.equal(writes.at(-1).targetType,'organization');assert.equal(writes.at(-1).organizationUserId,10);
   await page.evaluate(()=>showStaffPage('teachers'));
   assert.equal(await page.locator('#teachers .panel-page-tabs button').count(),4);
   await page.locator('#teachers .panel-page-tabs button').last().click();
   assert((await page.locator('#holidayLeavesTable tbody').textContent()).includes('Employee leave'));
   assert(!(await page.locator('#holidayLeavesTable tbody').textContent()).includes('Organization holiday'));
   await page.locator('button[onclick="openAddHolidayLeaveModal()"]').click();
   assert.equal(await page.locator('#hlMember').isDisabled(),true);
   await page.selectOption('#hlBranch','11');
   assert.equal(await page.locator('#hlMember').isEnabled(),true);
   assert.deepEqual(await page.locator('#hlMember option').evaluateAll(nodes=>nodes.map(x=>x.value)),['','member:200']);
   assert.equal(await page.locator('#modalContainer img').count(),0);
   const types=await page.locator('#hlType option').evaluateAll(nodes=>nodes.map(x=>x.value));assert(!types.includes('holiday')&&!types.includes('closed'));
   await page.selectOption('#hlMember','member:200');await page.locator('#hlDate').fill('2026-11-02');await page.locator('#hlAllDay').check();
   await page.locator('button[onclick="saveHolidayLeave()"] ').click();await page.waitForFunction(()=>!document.querySelector('#hlDate'));
   assert.equal(writes.at(-1).targetType,'member');assert.equal(writes.at(-1).membershipId,200);assert.equal(writes.at(-1).organizationUserId,11);
   await page.evaluate(()=>toggleHolidayLeaveInlineEdit(2));
   assert.equal(await page.locator('#inlineHl2Member').inputValue(),'member:200');
   await page.selectOption('#inlineHl2Branch','10');
   assert.equal(await page.locator('#inlineHl2Member').inputValue(),'');
   assert.deepEqual(await page.locator('#inlineHl2Member option').evaluateAll(nodes=>nodes.map(x=>x.value)),['','member:100']);
   await page.evaluate(()=>editHolidayLeave(2));
   assert.equal(await page.locator('#editHlMember').inputValue(),'member:200');
   assert(!(await page.locator('#editHlType option').evaluateAll(nodes=>nodes.map(x=>x.value))).includes('holiday'));
   await page.evaluate(()=>closeModal());
   await page.evaluate(()=>showStaffPage('member-schedules'));assert.equal(await page.locator('#member-schedules').isVisible(),true);
   const range=await page.evaluate(()=>buildHolidayLeaveTimeSlotsHTML('',10,[]));assert(range.includes('fa-trash-alt'));assert(!range.includes('items-end rounded-2xl border'));
   for (const script of ['member-schedules','availabilities','member-schedule-templates','availability-templates']) await page.addScriptTag({path:`assets/Analytics/js/${script}.js`});
   const ranges=await page.evaluate(()=>[buildMemberScheduleTimeSlotsHTML('',10,[]),buildBranchScheduleTimeSlotsHTML('',10,[])]);
   ranges.forEach(html=>{assert(html.includes('fa-trash-alt'));assert(!html.includes('items-end rounded-2xl border'));});
   assert.deepEqual(errors,[]);await page.close();
  }
  console.log('Scheduling tabs: fa/en navigation, separated holiday/leave lists, organization-dependent employees, type limits, scoped payloads, escaped names and compact range controls passed.');
 } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
