const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const fs = require('node:fs');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { tableRuntime, renderPanelView } = require('./panel-table-fixture.cjs');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    const config = {
      attributes: 'id="sample" data-no-identity-column class="w-full"',
      columns: [{ value: 'نام <آزمون>' }, { value: 'عملیات' }],
      rows: [
        { cells: [{ value: '<img src=x onerror=alert(1)>' }, { html: '<button onclick="window.clicked=(window.clicked||0)+1">ویرایش</button>' }] },
        { attributes: 'class="expanded"', cells: [{ value: 'جزئیات', attributes: 'colspan="2"' }] },
      ],
    };
    const phpTable = execFileSync('php', ['scripts/panel-table-view-fixture.php', 'data', 'fa', JSON.stringify(config)], { encoding: 'utf8' });
    await page.setContent(`<main id="mainContent"><section id="fixture" class="section"><div class="overflow-hidden bg-white shadow"><div class="overflow-x-auto">${phpTable}</div></div></section></main>${renderPanelView('panel-table-template')}${tableRuntime()}`);
    assert.equal(await page.locator('#sample th').count(), 2);
    assert.equal(await page.locator('#sample tbody tr').count(), 2);
    assert.equal(await page.locator('#sample img').count(), 0);
    assert.equal(await page.locator('#sample td').first().textContent(), '<img src=x onerror=alert(1)>');
    assert.equal(await page.locator('#sample .expanded td').getAttribute('colspan'), '2');
    await page.getByRole('button', { name: 'ویرایش' }).click();
    assert.equal(await page.evaluate(() => window.clicked), 1);
    await page.evaluate((data) => AdminPanelTable.render('#sample', data), config);
    assert.equal(await page.locator('#sample th').count(), 2);
    assert.equal(await page.locator('#sample td').first().textContent(), '<img src=x onerror=alert(1)>');
    await page.getByRole('button', { name: 'ویرایش' }).click();
    assert.equal(await page.evaluate(() => window.clicked), 2);
    await page.evaluate(() => {
      const row = AdminPanelTable.createRow('data-id="7"');
      row.innerHTML = AdminPanelTable.cellsHTML([{ value: 'هفت' }, { fragment: AdminPanelTable.cellsHTML([{ value: 'اختیاری' }]) }]);
      document.querySelector('#sample tbody').appendChild(row);
    });
    assert.equal(await page.locator('#sample [data-id="7"] td').count(), 2);
    for (const locale of ['fa', 'en']) {
      for (const view of ['students', 'courses', 'terms', 'classrooms', 'teachers', 'branches', 'users', 'lessons', 'classroom-types', 'roles', 'permissions', 'personal-panel-sections']) {
        const html = renderPanelView(view, locale);
        await page.setContent(html);
        assert(await page.locator('table[data-panel-table="1"]').count(), `${view}/${locale} renders shared tables`);
        const headerCounts = { courses: 8, teachers: 10, terms: 9 };
        if (headerCounts[view]) assert.equal(await page.locator('table').first().locator('thead th').count(), headerCounts[view], `${view}: complete column headings`);
        assert(!(await page.locator('body').textContent()).includes('panelTable0'), `${view}/${locale} contains no unrendered PHP`);
        if (view === 'personal-panel-sections') {
          assert.equal(await page.locator('table').count(), 5);
          assert.equal(await page.locator('table tbody tr').count(), 5);
          assert((await page.locator('table').allTextContents()).join(' ').includes('پیانو'));
          assert.equal(await page.locator('#lessons .panel-page-tabs button').count(), 2, 'Personal lessons do not expose academy courses');
        }
        if (view === 'courses') {
          assert.equal(await page.locator('.panel-page-tabs button').count(), 3);
          assert.equal(await page.locator('.panel-page-tabs button').nth(2).getAttribute('aria-current'), 'page');
          assert.equal(await page.locator('.panel-page-tabs button').nth(2).textContent(), locale === 'en' ? 'Courses' : 'دوره‌ها');
        }
      }
    }
    for (const language of ['fa', 'en']) {
      for (const view of ['lesson-page-tabs', 'classroom-page-tabs', 'staff-page-tabs', 'organization-page-tabs', 'my-learning-tabs']) {
        await page.setContent(`<style>.sticky{position:sticky;top:0}.fixed{position:fixed}</style>${renderPanelView(view, language)}<div style="height:1800px"></div><script>window.lastSection=null;window.showSection=id=>window.lastSection=id;window.showStaffPage=window.showSection;window.showOrganizationPage=window.showSection;</script>`);
        const tabs = page.locator('.panel-page-tabs');
        assert.equal(await tabs.count(), 1, `${view}: shared navigation`);
        if (view === 'lesson-page-tabs') assert.equal(await tabs.locator('button').first().textContent(), language === 'en' ? 'Lesson levels' : 'سطح درس‌ها');
        if (view === 'classroom-page-tabs') assert.equal(await tabs.locator('button').first().textContent(), language === 'en' ? 'Classroom types' : 'انواع کلاس');
        await tabs.locator('button').first().click();
        assert(await page.evaluate(() => Boolean(window.lastSection)), `${view}: navigation works`);
        const before = await tabs.boundingBox();
        await page.evaluate(() => scrollTo(0, 500));
        const after = await tabs.boundingBox();
        assert(after.y < before.y - 400, `${view}: tabs scroll out with the page`);
        await page.evaluate(() => scrollTo(0, 0));
      }
    }
    for (const language of ['fa', 'en']) {
      await page.setContent(`<html lang="${language}"><body><div id="staffFixture"></div></body></html>`);
      await page.addScriptTag({ content: fs.readFileSync('assets/Analytics/js/teacher-templates.js', 'utf8') });
      await page.evaluate((lang) => {
        window.staffTypes = [{ value: 'teacher', label: 'Teacher' }];
        window.contractCurrencies = [];
        window.staffCatalog = {
          organizations: [{ user_id: 10, branch_id: null, name: 'Academy' }, { user_id: 11, branch_id: 1, name: 'Branch' }],
          roles: [
            { id: 10, name: 'academy_teacher', title: lang === 'en' ? 'Academy teacher' : 'مدرس آموزشگاه' },
            { id: 13, name: 'academy_branch_teacher', title: lang === 'en' ? 'Branch teacher' : 'مدرس شعبه' },
          ],
        };
        document.getElementById('staffFixture').innerHTML = getStaffAddModalHTML();
        updateStaffOrganization('staff');
      }, language);
      assert.deepEqual(await page.locator('#staffRole option').evaluateAll(nodes => nodes.map(n => n.value)), ['', '10']);
      assert.equal(await page.locator('#staffRole option[value="10"]').textContent(), language === 'en' ? 'Academy teacher' : 'مدرس آموزشگاه');
      await page.evaluate(() => {
        document.getElementById('staffOrganization').value = '11';
        updateStaffOrganization('staff');
      });
      assert.deepEqual(await page.locator('#staffRole option').evaluateAll(nodes => nodes.map(n => n.value)), ['', '13']);
      await page.evaluate(() => {
        document.getElementById('staffFixture').innerHTML = getStaffEditModalHTML({ id: 1, type: 'teacher', roleId: 13, organizationUserId: 11 });
        updateStaffOrganization('editStaff');
      });
      assert.equal(await page.locator('#editStaffRole').inputValue(), '13');
      assert.equal(await page.locator('#editStaffRole option[value="13"]').textContent(), language === 'en' ? 'Branch teacher' : 'مدرس شعبه');
      await page.evaluate(() => {
        document.getElementById('staffFixture').innerHTML = getStaffEditModalHTML({ id: 1, type: 'teacher', roleId: 99, organizationUserId: 11 });
        updateStaffOrganization('editStaff');
      });
      assert.equal(await page.locator('#editStaffRole').inputValue(), '', 'Unavailable saved role is not silently replaced');
    }
    await page.setContent('<html lang="fa"><body><div id="classroomFixture"></div></body></html>');
    await page.addScriptTag({ content: fs.readFileSync('assets/Analytics/js/classroom-templates.js', 'utf8') });
    await page.evaluate(() => {
      window.classroomOrganizations = [{ user_id: 10, id: 100, kind: 'academy', name: 'آموزشگاه آزمایشی', selectable: true }, { user_id: 11, id: 101, kind: 'branch', name: 'شعبه آزمایشی' }];
      window.classroomPermissions = { isBranchContext: false };
      document.getElementById('classroomFixture').innerHTML = getClassroomAddModalHTML();
    });
    assert.equal(await page.locator('#classroomOrganization option[value="10"]').textContent(), 'آموزشگاه آزمایشی');
    assert.equal(await page.locator('#classroomOrganization').inputValue(), '10');
    await page.evaluate(() => {
      document.getElementById('classroomFixture').innerHTML = getClassroomEditModalHTML({ id: 1, organizationUserId: 10 });
    });
    assert.equal(await page.locator('#editClassroomOrganization').inputValue(), '10');
    await page.evaluate(() => {
      classroomOrganizations[0].selectable = false;
      document.getElementById('classroomFixture').innerHTML = getClassroomAddModalHTML();
    });
    assert.equal(await page.locator('#classroomOrganization option[value="10"]').isDisabled(), true);
    assert.equal(await page.locator('#classroomOrganization').inputValue(), '11');
    assert.equal(await page.locator('button[onclick="promptAddClassroomType()"]').count(), 0);
    await page.setContent(`${renderPanelView('branches')}${tableRuntime()}`);
    await page.addScriptTag({ content: fs.readFileSync('assets/Analytics/js/branch-templates.js', 'utf8') });
    await page.evaluate(() => {
      window.branchSiteAdmin = false;
      window.getBranchPhysicalTypeLabel = value => value;
      document.getElementById('branchesTableBody').innerHTML = getBranchTableRowHTML({ id: 1, name: 'Branch', academy_name: 'Hidden academy', type: 'Music', physical_type: 'physical', status: 'active' });
    });
    assert.equal(await page.locator('#branchesTableBody tr td').count(), 6);
    assert.equal(await page.locator('#branchesTableWrap th').count(), 6);
    assert(!(await page.locator('#branchesTableBody').textContent()).includes('Hidden academy'));
    await page.setContent(`<style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('users')}</main>${renderPanelView('panel-table-template')}${tableRuntime()}`);
    await page.addScriptTag({ content: fs.readFileSync('assets/Analytics/js/users.js', 'utf8') });
    await page.evaluate(async () => {
      document.getElementById('users').classList.remove('hidden');
      window.fetch = async (url) => {
        const params = new URL(url, 'http://sornaz.test').searchParams;
        let records = Array.from({ length: 25 }, (_, i) => ({ id: i + 1, name: 'User ' + (i + 1), roles: [], permissions: [], status: 'approved', canEdit: false }));
        if (params.get('sortDirection') !== 'asc') records.reverse();
        const page = Number(params.get('page') || 1), per = Number(params.get('perPage') || 20);
        return { ok: true, json: async () => ({ data: { users: records.slice((page - 1) * per, page * per), total: records.length, page, roles: [], permissions: [], branches: [] } }) };
      };
      await loadAccessUsers(1);
    });
    assert.equal(await page.locator('#accessUsersTable').getAttribute('data-panel-table'), '1');
    assert.equal(await page.locator('.admin-ui-table #accessUsersTable').count(), 1);
    assert.equal(await page.locator('#accessUsersTable th').count(), 6, 'Shared identity column appears after asynchronous load');
    assert.equal(await page.locator('#accessUsersTable tbody tr').first().locator('td').count(), 6);
    await page.locator('#accessUsersTable [data-admin-identity-column] button').click();
    await page.waitForFunction(() => document.querySelector('#accessUsersTable [data-admin-identity-cell]')?.textContent === '1');
    await page.evaluate(() => changeAccessUsersPage(2));
    assert.deepEqual(await page.locator('#accessUsersTable [data-admin-identity-cell]').allTextContents(), Array.from({ length: 10 }, (_, i) => String(i + 11)));
    assert.deepEqual(errors, []);
    console.log('Shared panel table: PHP/JS rendering, fa/en views, escaped values, actions, optional cells, expansion and repeated renders passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
