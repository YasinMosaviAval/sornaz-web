const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { tableRuntime, renderPanelView } = require('./panel-table-fixture.cjs');
(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [], deletes = [];
    page.on('pageerror', (error) => errors.push(error.message));
    const usage = { courses: [{ id: 5, title: '<img src=x onerror=alert(1)>', organization: 'Academy' }], contracts: [{ id: 7, title: 'Teacher contract', teacher: 'Teacher A', organization: 'Branch A' }] };
    let used = true, race = false;
    await page.route('**/*', (route) => {
      const path = new URL(route.request().url()).pathname;
      if (path.startsWith('/assets/')) return route.fulfill({ path: '.' + path });
      if (path.endsWith('/usage')) return route.fulfill({ json: { data: { success: true, data: used ? usage : { courses: [], contracts: [] } } } });
      if (path.endsWith('/delete')) {
        deletes.push(path);
        return route.fulfill({ status: race ? 409 : 200, json: { data: race ? { success: false, dependencies: usage } : { success: true } } });
      }
      if (path.endsWith('/realtime-version')) return route.fulfill({ json: { data: { success: true, data: { version: '1' } } } });
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<html lang="fa"><body><style>.hidden{display:none}</style><main id="mainContent">${renderPanelView('lessons')}</main><div id="modalContainer"></div>${tableRuntime()}<script>window.AppDialog={confirmDelete:async()=>true};window.closeModal=()=>document.getElementById('modalContainer').innerHTML='';</script><script src="/assets/Analytics/js/lesson-templates.js"></script><script src="/assets/Analytics/js/lessons.js"></script></body></html>` });
    });
    await page.goto('http://sornaz.test/');
    await page.evaluate(() => {
      const item = { id: 10, title: 'Lesson', user_id: 1, lesson_id: 1, status: 'active', start_date: '2026-10-01', canEdit: false, canDelete: false, canInspectDependencies: true, canChangeStatus: false };
      window.allUserLessons = [item];
      document.querySelector('#lessonsTable tbody').innerHTML = AdminPanelTable.rowHTML([{ fragment: getLessonRowHTML(item) }]);
      document.getElementById('lessons').classList.remove('hidden');
    });
    assert.equal(await page.locator('[data-lesson-inline-edit-id]').count(), 0);
    await page.locator('button[onclick="deleteLesson(10)"]').click();
    await page.waitForSelector('#lessonUsageTitle');
    assert.match(await page.getByRole('dialog').textContent(), /Teacher A/);
    assert.match(await page.getByRole('dialog').textContent(), /Teacher contract/);
    assert.equal(await page.getByRole('dialog').locator('img').count(), 0);
    assert.equal(deletes.length, 0);
    await page.evaluate(() => closeModal());
    used = false; race = true;
    await page.evaluate(() => deleteLesson(10));
    assert.equal(deletes.length, 1);
    assert.equal(await page.locator('#lessonUsageTitle').count(), 1, 'Late server dependency conflict is shown');
    await page.evaluate(() => {
      closeModal();
      const row = { id: 11, title: 'Inherited', canEdit: false, canChangeStatus: false, canDelete: false, canInspectDependencies: false };
      document.querySelector('#lessonsTable tbody').innerHTML = AdminPanelTable.rowHTML([{ fragment: getLessonRowHTML(row) }]);
    });
    assert.equal(await page.locator('button[onclick="deleteLesson(11)"]').count(), 0);
    assert.match(await page.locator('#lessons').textContent(), /اولین مرحله مدیریت امور آموزشگاه و شعبه ها/);
    await page.evaluate(() => {
      window.allUserLessons = Array.from({ length: 25 }, (_, i) => ({
        id: 25 - i, title: 'Lesson ' + String(i + 1).padStart(2, '0'), status: 'active',
        canEdit: false, canInspectDependencies: true,
      }));
      filterLessons();
    });
    const ids = () => page.locator('#lessonsTable [data-admin-identity-cell]').allTextContents();
    const descending = (from, count) => Array.from({ length: count }, (_, i) => String(from - i));
    assert.deepEqual(await ids(), Array.from({ length: 10 }, (_, i) => String(i + 1)));
    await page.locator('#lessonsTable [data-admin-identity-column] button').click();
    assert.deepEqual(await ids(), descending(25, 10), 'ID sort uses all 25 records');
    await page.evaluate(() => changeLessonsPage(2));
    assert.deepEqual(await ids(), descending(15, 10), 'ID direction survives page change');
    await page.locator('#lessonsTable button[onclick="sortLessonsBy(\'title\')"]').click();
    assert.deepEqual(await ids(), descending(15, 10), 'Native sort replaces ID sorting');
    await page.evaluate(() => changeLessonsPage(3));
    assert.deepEqual(await ids(), descending(5, 5), 'Native sort survives page change');
    await page.locator('#lessonsTable [data-admin-identity-column] button').click();
    assert.deepEqual(await ids(), ['21', '22', '23', '24', '25'], 'Ascending ID sort also uses all rows');
    for (const name of ['lessons', 'terms', 'teachers', 'member-schedules']) {
      for (const language of ['fa', 'en']) {
        await page.setContent(renderPanelView(name, language));
        await page.locator('.section').evaluate(element => element.classList.remove('hidden'));
        const guide = page.locator('.panel-page-guide');
        const body = guide.locator('p').first();
        assert.equal(await guide.count(), 1, `${name}: shared guide`);
        assert.equal(await body.isVisible(), false, `${name}: initially collapsed`);
        await guide.locator('summary').click();
        assert.equal(await body.isVisible(), true, `${name}: expands on click`);
        await guide.locator('summary').focus();
        await page.keyboard.press('Enter');
        assert.equal(await body.isVisible(), false, `${name}: collapses with keyboard`);
      }
    }
    assert.deepEqual(errors, []);
    console.log('Lesson UI: dependency lists, escaped titles, restricted actions, inherited rows and late deletion conflicts passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
