const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
  const browser = await chromium.launch({channel: 'msedge', headless: true});
  try {
    for (const language of ['fa', 'en']) {
      const page = await browser.newPage();
      const errors = [];
      page.on('pageerror', error => errors.push(error.message));
      await page.setContent(`<html lang="${language}"><div id="staffBranchTabs"></div><div id="modalContainer"><input id="staffName" value="Keep this input"><select id="staffOrganization"><option value="10">Academy</option></select><select id="termBranch"><option value="10">Academy</option></select></div></html>`);
      await page.evaluate(() => {
        window.academyHasNoBranches = false;
        window.branchAccount = false;
        window.academyPanelBranches = [{id: 101, name: 'Old branch'}];
        window.staffCatalog = {organizations: [{user_id: 10, name: 'Academy'}]};
        window.termBranches = [{id: 10, name: 'Academy'}];
        window.loadTerms = async () => { window.termBranches = window.nextOrganizations.map(row => ({id: row.user_id, name: row.name})); };
        window.ensureStudentFormOptions = async () => {};
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
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Organization live UI: fa/en, rename/create/delete, open selectors, preserved input, teacher-only notes and escaping passed.');
  } finally { await browser.close(); }
})().catch(error => {console.error(error); process.exitCode = 1;});
