const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    await page.route('**/*', (route) => {
      const path = new URL(route.request().url()).pathname;
      if (path.startsWith('/js/')) return route.fulfill({ path: 'assets/Analytics/js/' + path.slice(4) });
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<div id="modalContainer"></div><script>window.closeModal=()=>document.getElementById('modalContainer').innerHTML='';window.alert=()=>{};</script><script src="/js/panel-organization-filters.js"></script><script src="/js/classroom-templates.js"></script><script src="/js/classrooms.js"></script>` });
    });
    await page.goto('http://sornaz.test/');
    await page.evaluate(() => {
      allClassroomTypes = [{ id: 1, name: 'کلاس موسیقی' }];
      allClassroomBranches = [{ id: 101, name: 'شعبه واقعی' }];
      window.classroomOrganizations = [{ id: 100, user_id: 10, kind: 'academy', name: 'آموزشگاه واقعی' }, { id: 101, user_id: 11, kind: 'branch', name: 'شعبه واقعی' }];
      window.classroomApi = async (_url, data) => { window.submittedRoom = data; return { ...data, id: 1 }; };
      openAddClassroomModal();
    });
    assert.deepEqual(await page.locator('#classroomOrganization option').allTextContents(), ['سازمان را انتخاب کنید', 'آموزشگاه واقعی', 'شعبه واقعی']);
    assert.equal(await page.locator('#classroomBranch').count(), 0);
    await page.locator('#classroomName').fill('کلاس آزمایشی');
    await page.selectOption('#classroomOrganization', '10');
    await page.evaluate(() => saveClassroom());
    assert.deepEqual(await page.evaluate(() => [submittedRoom.organizationUserId, submittedRoom.branchId]), [10, 0]);
    await page.evaluate(() => {
      allClassrooms = [{ id: 1, name: 'کلاس آزمایشی', typeId: 1, organizationUserId: 10, branchId: 0, capacity: 5, equipment: [], deliveryMode: 'in_person' }];
      return editClassroom(1);
    });
    assert.equal(await page.locator('#editClassroomOrganization').inputValue(), '10');
    assert.equal(await page.locator('#editClassroomOrganization option[value="10"]').isEnabled(), true);
    await page.selectOption('#editClassroomOrganization', '11');
    await page.evaluate(() => saveEditedClassroom(1));
    assert.deepEqual(await page.evaluate(() => [submittedRoom.organizationUserId, submittedRoom.branchId]), [11, 101]);
    await page.evaluate(() => editClassroom(1));
    await page.selectOption('#editClassroomOrganization', '10');
    await page.evaluate(() => saveEditedClassroom(1));
    assert.deepEqual(await page.evaluate(() => [submittedRoom.organizationUserId, submittedRoom.branchId]), [10, 0], 'Editing supports returning classroom ownership to the academy');
    await page.evaluate(() => {
      window.panelBranchContext = true;
      window.classroomOrganizations = [{ id: 101, user_id: 11, kind: 'branch', name: 'Own branch' }];
      openAddClassroomModal();
    });
    await page.waitForFunction(() => document.getElementById('classroomOrganization').value === '11');
    assert.equal(await page.locator('#classroomOrganization').isVisible(), false);
    await page.locator('#classroomName').fill('Scoped classroom');
    await page.evaluate(() => saveClassroom());
    assert.deepEqual(await page.evaluate(() => [submittedRoom.organizationUserId, submittedRoom.branchId]), [11, 101], 'Hidden organization submits the authorized branch, not academy');
    assert.deepEqual(errors, []);
    console.log('Classroom forms: academy/branch organization selection and add/edit payloads passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
