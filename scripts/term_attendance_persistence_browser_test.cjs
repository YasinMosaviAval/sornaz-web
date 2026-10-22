const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
  const browser = await chromium.launch({channel: 'msedge', headless: true});
  try {
    for (const lang of ['fa', 'en']) {
      const page = await browser.newPage();
      const errors = [];
      page.on('pageerror', error => errors.push(error.message));
      await page.setContent(`<html lang="${lang}"><div id="modalContainer"></div></html>`);
      await page.evaluate(() => {
        window.adminCsrfToken = 'fixture-csrf';
        window.savedAttendance = [];
        window.fetch = async (url, options) => {
          if (options.method === 'POST') {
            if (window.failSave) return {ok: false, text: async () => JSON.stringify({success: false, message: 'Access denied'})};
            const body = new URLSearchParams(options.body);
            const encoded = body.get('payload_b64').replace(/-/g, '+').replace(/_/g, '/');
            window.savedAttendance.push({url, token: options.headers['X-CSRF-TOKEN'], data: JSON.parse(decodeURIComponent(escape(atob(encoded))))});
            return {ok: true, text: async () => JSON.stringify({success: true})};
          }
          return {ok: true, text: async () => JSON.stringify({success: true, data: {total: 1, schedules: [{id: 51, termId: 7, date: '2030-01-05', title: 'Real session', canWriteTeacherNote: true, teacherNote: 'Previous note', attendance: [{id: 12, name: 'Student', status: 'late', description: 'Previous description'}]}]}})};
        };
      });
      for (const file of ['term-templates.js', 'schedule-templates.js', 'terms.js']) {
        await page.addScriptTag({path: `assets/Analytics/js/${file}`});
      }
      await page.evaluate(async () => {
        allTerms = [{id: 7, name: 'Term', sessions: [{id: 51}]}];
        await openTermAttendanceModal(7);
      });
      assert.equal(await page.locator('.schedule-att-status').inputValue(), 'late');
      assert.equal(await page.locator('.schedule-att-description').inputValue(), 'Previous description');
      await page.locator('[data-teacher-note]').fill('Actual teacher note <script>');
      await page.locator('.schedule-att-status').selectOption('present');
      await page.locator('[onclick^="saveTermSessionAttendance"]').click();
      await page.waitForFunction(() => window.savedAttendance.length === 1);
      assert.deepEqual(await page.evaluate(() => savedAttendance[0]), {url: '/academy/admin/class-schedules/51/attendance', token: 'fixture-csrf', data: {attendance: [{memberId: 12, status: 'present', description: 'Previous description'}], teacherNote: 'Actual teacher note <script>'}});
      await page.evaluate(() => window.failSave = true);
      await page.locator('[data-teacher-note]').fill('Unsaved note');
      await page.locator('[onclick^="saveTermSessionAttendance"]').click();
      await page.waitForFunction(() => document.querySelector('[data-attendance-result]').textContent === 'Access denied');
      assert.equal(await page.locator('[data-teacher-note]').inputValue(), 'Unsaved note');
      assert.equal(await page.evaluate(() => savedAttendance.length), 1);
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Actual term attendance dialog: persisted session IDs, existing statuses, CSRF, notes and failed-save input preservation passed (fa/en).');
  } finally { await browser.close(); }
})().catch(error => {console.error(error); process.exitCode = 1;});
