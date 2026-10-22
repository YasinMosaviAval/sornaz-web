const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
  const browser = await chromium.launch({channel: 'msedge', headless: true});
  try {
    for (const language of ['fa', 'en']) {
      const page = await browser.newPage();
      const errors = [];
      page.on('pageerror', error => errors.push(error.message));
      await page.setContent(`<html lang="${language}"><div id="staffBranchTabs"></div><div id="modalContainer"><input id="staffName" value="Keep this input"><select id="staffOrganization"><option value="10">Academy</option></select><select id="termBranch"><option value="10">Academy</option></select>${['stuOrganization', 'editStuOrganization', 'inlineStu7Organization'].map(id => `<select id="${id}"><option value="10">Academy</option></select>`).join('')}</div></html>`);
      await page.evaluate(() => {
        window.academyHasNoBranches = false;
        window.branchAccount = false;
        window.academyPanelBranches = [{id: 101, name: 'Old branch'}];
        window.staffCatalog = {organizations: [{user_id: 10, name: 'Academy'}]};
        window.termBranches = [{id: 10, name: 'Academy'}];
        window.loadTerms = async () => { window.termBranches = window.nextOrganizations.map(row => ({id: row.user_id, name: row.name})); };
        window.loadCourses = () => new Promise(() => {}); // unrelated slow panel
        window.ensureStudentFormOptions = async () => {window.studentOrganizations = window.nextOrganizations;};
        window.renderPanelOrganizationTabs = undefined;
      });
      await page.addScriptTag({path: 'assets/Analytics/js/panel-organization-filters.js'});
      await page.evaluate(() => {
        renderPanelOrganizationTabs('staffBranchTabs', 'all', () => {});
        window.dispatchEvent(new CustomEvent('academy-data-loaded', {detail: {branches: academyPanelBranches, academies: []}}));
        window.nextOrganizations = [{user_id: 10, name: 'Academy renamed'}, {user_id: 11, name: 'New branch'}];
        window.staffCatalog.organizations = window.nextOrganizations;
        window.academyPanelBranches = [{id: 101, name: 'Branch renamed'}, {id: 102, name: 'New branch'}];
        window.dispatchEvent(new CustomEvent('academy-data-loaded', {detail: {branches: academyPanelBranches, academies: []}}));
      });
      await page.waitForFunction(() => document.getElementById('termBranch').options.length === 2);
      assert.equal(await page.locator('#staffName').inputValue(), 'Keep this input');
      assert.equal(await page.locator('#staffOrganization').inputValue(), '10');
      assert.deepEqual(await page.locator('#termBranch option').allTextContents(), ['Academy renamed', 'New branch']);
      for (const id of ['stuOrganization', 'editStuOrganization', 'inlineStu7Organization']) {
        assert.deepEqual(await page.locator(`#${id} option`).allTextContents(), ['Academy renamed', 'New branch']);
        assert.equal(await page.locator(`#${id}`).inputValue(), '10');
      }
      assert((await page.locator('#staffBranchTabs').textContent()).includes('Branch renamed'));
      await page.evaluate(() => {
        document.getElementById('staffOrganization').value = '11';
        window.nextOrganizations = [{user_id: 10, name: 'Academy renamed'}];
        window.staffCatalog.organizations = window.nextOrganizations;
        window.academyPanelBranches = [];
        window.dispatchEvent(new CustomEvent('academy-data-loaded', {detail: {branches: [], academies: []}}));
      });
      await page.waitForFunction(() => document.getElementById('staffOrganization').options.length === 1);
      assert.equal(await page.locator('#staffOrganization').inputValue(), '');
      await page.addScriptTag({path: 'assets/Analytics/js/availability-templates.js'});
      await page.evaluate(() => {
        window.getBranchScheduleOrganizations = () => window.nextOrganizations.map(row => ({id: row.user_id, name: row.name}));
        document.getElementById('modalContainer').innerHTML = getBranchScheduleAddModalHTML();
        window.nextOrganizations = [{user_id: 10, name: 'New academy title'}, {user_id: 12, name: 'Another branch'}];
        window.academyPanelBranches = [{id: 103, name: 'Another branch'}];
        window.dispatchEvent(new CustomEvent('academy-data-loaded', {detail: {branches: academyPanelBranches, academies: []}}));
      });
      await page.waitForFunction(() => [...document.querySelectorAll('#bsBranch option')].some(option => option.value === '12'));
      assert((await page.locator('#bsBranch option').allTextContents()).includes('New academy title'));
      await page.addScriptTag({path: 'assets/Analytics/js/schedule-templates.js'});
      await page.evaluate(() => {
        document.getElementById('modalContainer').innerHTML = getScheduleAttendancePanelHTML({id: 1, title: 'Session', attendance: [], canWriteTeacherNote: true, teacherNote: '<script>bad()</script>'}, false);
      });
      assert.equal(await page.locator('[data-teacher-note]').inputValue(), '<script>bad()</script>');
      assert.equal(await page.locator('#modalContainer script').count(), 0);
      await page.evaluate(() => {
        document.getElementById('modalContainer').innerHTML = getScheduleAttendancePanelHTML({id: 1, title: 'Session', attendance: [], canWriteTeacherNote: false}, false);
      });
      assert.equal(await page.locator('[data-teacher-note]').count(), 0);
      await page.addScriptTag({path: 'assets/Analytics/js/student-term-schedule.js'});
      await page.evaluate(() => {
        window.studentClassrooms = [{id: 1001, name: 'Classroom', organizationUserIds: [10]}];
        document.getElementById('modalContainer').innerHTML = getStudentTermScheduleHTML('stu', {id: 1, organizationUserId: 10, status: 'open', scheduleDraft: {recurrence: {weekday: '6'}}});
      });
      await page.selectOption('[data-student-classroom]', '1001');
      assert.deepEqual(await page.evaluate(() => readStudentTermSchedule('stu')), {firstDate: '', classroomId: 1001, dates: []});
      await page.addScriptTag({path: 'assets/Analytics/js/branches.js'});
      const refreshed = await page.evaluate(async () => {
        let reads = 0;
        window.branchOfferingData = {organizations: [{id: 100, user_id: 10, name: 'Stale'}]};
        window.fetch = async () => {reads++; return {ok: true, text: async () => JSON.stringify({success: true, data: {organizations: [{id: 100, user_id: 10, name: 'Fresh'}]}})};};
        await loadBranchOfferings();
        const before = reads;
        await loadBranchOfferings(true);
        return {before, reads, name: branchOfferingData.organizations[0].name};
      });
      assert.deepEqual(refreshed, {before: 0, reads: 1, name: 'Fresh'});
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Organization live UI: fa/en, rename/create/delete, open selectors, preserved input, teacher-only notes and escaping passed.');
  } finally { await browser.close(); }
})().catch(error => {console.error(error); process.exitCode = 1;});
