const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    for (const language of ['fa', 'en']) {
      const page = await browser.newPage({ viewport: { width: 390, height: 850 } });
      const errors = [];
      page.on('pageerror', e => errors.push(e.message));
      await page.setContent(`<html lang="${language}" dir="${language === 'fa' ? 'rtl' : 'ltr'}"><style>.hidden{display:none}</style><div id="modalContainer"></div></html>`);
      await page.evaluate(() => {
        window.alerts = []; window.alert = text => alerts.push(text);
        window.closeModal = () => document.getElementById('modalContainer').replaceChildren();
        window.encodeBranchPayload = data => btoa(unescape(encodeURIComponent(JSON.stringify(data))));
        window.writes = [];
        window.lookupFields = { name: true, phone: true, nationalId: true, gender: true, birthDate: true, fatherName: true };
        window.branchRequest = async (url, form) => {
          if (url.endsWith('/lookup')) return { users: [{ token: 'fixture-selection', username: '<img src=x onerror=alert(1)>', name: 'Existing User', fields: lookupFields, under18: window.lookupMinor || false }] };
          if (url.endsWith('/student-schedule-preview')) {
            const data=JSON.parse(decodeURIComponent(escape(atob(form.payload_b64))));
            return {sessions:(data.dates.length?data.dates:[data.firstDate,'2030-01-12']).map(date=>({date,error:''}))};
          }
          if (form) writes.push({ url, data: JSON.parse(decodeURIComponent(escape(atob(form.payload_b64)))) });
          return { member_id: 9, status: 'active', staff_catalog: window.staffCatalog, members: [], staff: [] };
        };
      });
      for (const file of ['teachers.js', 'teacher-templates.js', 'students.js', 'student-templates.js', 'member-user-picker.js','term-templates.js','student-term-schedule.js']) {
        await page.addScriptTag({ path: `assets/Analytics/js/${file}` });
      }
      await page.addScriptTag({path:'assets/vendor/jalalidatepicker/jalalidatepicker.min.js'});
      await page.addScriptTag({path:'assets/theme/localized-date.js'});
      async function birthField(prefix, visible) {
        const field=page.locator('#'+prefix+'BirthDate').locator('xpath=ancestor::div[@data-member-identity-field][1]');
        assert.equal(await field.isVisible(),visible);
        assert.equal(await field.locator('label').isVisible(),visible);
        if(visible) assert.equal(await field.locator(language==='fa'?'input[data-jdp]':'input[type=date]').isVisible(),true);
      }
      await page.evaluate(() => {
        window.staffCatalog = { organizations: [{ user_id: 10, name: 'Academy', branch_id: null }, { user_id: 11, name: 'Branch', branch_id: 101 }], roles: [{ id: 40, name: 'academy_manager_1', title: 'Manager' }], currencies: [{ id: 1, name: 'Toman' }], levels: [], lessons: [] };
        window.staffExistingUsers=[{user_id:8,username:'existing_user',name:'Previously registered employee'}];
        window.studentOrganizations = [{ user_id: 10, kind: 'academy', id: 100, name: 'Academy' }, { user_id: 11, kind: 'branch', id: 101, name: 'Branch' }];
        window.studentProvinces = [{ province_id: 1, province_name: 'Fixture' }];
        window.studentCounties = [{ province_id: 1, county_name: 'Fixture City' }];
        window.studentTermGroups = [{ id:5,key: '5', name: 'Term', organizationUserId: 10, status: 'open', sessionCount:2,repeatType:'week',hasSessions:false,scheduleDraft:{startTime:'10:00',endTime:'10:30',recurrence:{weekday:6}}, levels: [{ name: 'Basic', termIds: [5] }] }];
        window.ensureStudentFormOptions = async () => true;
        window.filterStudents = () => {};
        window.fetch = async () => ({ ok: true, json: async () => ({ data: { members: [] } }) });
      });
      async function choose(prefix) {
        const host = page.locator(`[data-member-user-picker="${prefix}"]`);
        if(await host.locator('[data-existing-staff]').count()) await host.locator('[data-existing-staff]').selectOption('existing_user');
        else {
          await host.locator('[data-user-query]').fill('existing_user');
          await host.locator('[data-user-search]').click();
        }
        await host.locator('[data-user-results] button').click();
        assert.equal(await host.locator('img').count(), 0);
      }
      await page.evaluate(() => openAddStaffModal());
      await choose('staff');
      for (const suffix of ['Name', 'Phone', 'NationalId', 'Gender', 'BirthDate']) assert.equal(await page.locator('#staff' + suffix).isVisible(), false);
      await birthField('staff',false);
      await page.selectOption('#staffType', 'manager');
      await page.selectOption('#staffRole', '40');
      await page.locator('#staffContractTitle').fill('Existing employee contract');
      await page.locator('#staffStartDate').evaluate(el=>el.value='2030-01-01');
      await page.locator('#staffEndDate').evaluate(el=>el.value='2031-01-01');
      await page.locator('#staffPrice').fill('100');
      await page.evaluate(() => saveStaff());
      assert.equal(await page.locator('#registerForm').count(), 0);
      assert.equal(await page.evaluate(() => writes[0]?.data.existingUserToken), 'fixture-selection');
      assert.equal(await page.evaluate(() => writes[0]?.url), '/academy/admin/members');

      await page.evaluate(() => openAddStaffModal());
      await page.evaluate(() => { lookupFields = { ...lookupFields, birthDate: false }; });
      await choose('staff');
      await birthField('staff',true);
      assert.equal(await page.locator('#staffNationalId').isVisible(), false);
      await page.selectOption('#staffOrganization', '11');
      assert.equal(await page.evaluate(() => getMemberUserSelection('staff')), null);
      assert.equal(await page.locator('#staffNationalId').isVisible(), true);

      await page.evaluate(() => { lookupFields.birthDate = true; return openAddStudentModal(); });
      await choose('stu');
      for (const suffix of ['Name', 'Phone', 'NationalId', 'FatherName', 'BirthDate']) assert.equal(await page.locator('#stu' + suffix).isVisible(), false);
      await birthField('stu',false);
      assert.equal(await page.locator('#stuParentWrap').isVisible(), false);
      await page.locator('.student-address-province').selectOption('Fixture');
      await page.locator('.student-address-city').selectOption('Fixture City');
      await page.locator('.student-address-text').fill('Fixture address');
      await page.selectOption('#stuInstrument', '5');
      assert.equal(await page.locator('#stuTermSchedule').isVisible(),true);
      await page.locator('#stuTermSchedule [data-student-first-date]').evaluate(input=>{input.value='2030-01-05';input.dispatchEvent(new Event('change',{bubbles:true}));});
      await page.waitForFunction(()=>document.querySelectorAll('#stuTermSchedule input.term-session-date:not([data-jdp])').length===2);
      assert.deepEqual(await page.evaluate(()=>readStudentTermSchedule('stu').dates),['2030-01-05','2030-01-12']);
      await page.evaluate(() => saveStudent());
      assert.equal(await page.evaluate(() => writes.at(-1)?.url), '/academy/admin/students');
      assert.equal(await page.evaluate(() => writes.at(-1)?.data.existingUserToken), 'fixture-selection');
      assert.deepEqual(await page.evaluate(()=>writes.at(-1).data.studentSchedule.dates),['2030-01-05','2030-01-12']);
      await page.evaluate(() => { lookupMinor = true; return openAddStudentModal(); });
      await choose('stu');
      assert.equal(await page.locator('#stuParentWrap').isVisible(), true);
      await page.locator('[data-user-reset]').click();
      assert.equal(await page.locator('#stuNationalId').isVisible(), true);
      await birthField('stu',true);
      await page.evaluate(()=>{lookupMinor=false;lookupFields.birthDate=false;return openAddStudentModal();});
      await choose('stu');
      await birthField('stu',true);
      for(const stored of [true,false]) {
        await page.evaluate(stored=>{
          const item={id:9,name:'Existing',birthDate:stored?'1990-01-01':'',type:'manager',lessons:[],organizationUserId:10};
          document.getElementById('modalContainer').innerHTML=getStaffEditModalHTML(item);
        },stored);
        await birthField('editStaff',!stored);
        await page.evaluate(stored=>{
          document.getElementById('modalContainer').innerHTML=getStudentEditModalHTML({id:9,name:'Existing',birthDate:stored?'1990-01-01':'',organizationUserId:10});
        },stored);
        await birthField('editStu',!stored);
      }
      await page.evaluate(()=>{
        document.getElementById('modalContainer').innerHTML=getStudentEditModalHTML({id:9,organizationUserId:10,termKey:'5',studentSchedule:{termId:5,firstDate:'2030-01-05',dates:['2030-01-05','2030-01-19']}});
      });
      assert.deepEqual(await page.evaluate(()=>readStudentTermSchedule('editStu').dates),['2030-01-05','2030-01-19']);
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Member picker: staff/student dialogs, fa/en, hidden saved fields, missing fields, organization reset, escaped results, existing-account saves and minor parent fields passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
