const assert = require('node:assert/strict');
const fs = require('node:fs');
const { tableRuntime } = require('./panel-table-fixture.cjs');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const css = fs.readFileSync('Modules/Analytics/Resources/Views/sections/users.php', 'utf8').match(/<style>([\s\S]*?)<\/style>/)?.[1];
  assert.ok(css, 'User access selection styles missing');
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    const writes = [];
    const member = { id: 8, name: 'کاربر آزمایشی', username: 'member', email: 'member@example.test', phone: '09120000000', roles: [{ id: 11, title: 'مدیر' }], permissions: [{ id: 21, title: 'مشاهده' }, { id: 22, title: 'ویرایش' }], rolePermissionIds: [21], rolePermissions: [{ id: 21, title: 'مشاهده' }], canEdit: true };
    page.on('pageerror', (error) => errors.push(error.message));
    await page.route('**/*', async (route) => {
      const uri = new URL(route.request().url()).pathname;
      if (uri === '/assets/Analytics/js/users.js') return route.fulfill({ contentType: 'application/javascript; charset=utf-8', body: fs.readFileSync('assets/Analytics/js/users.js', 'utf8') });
      if (uri === '/analytics/admin-user-access') return route.fulfill({ contentType: 'application/json', body: JSON.stringify({ data: { users: [member], roles: [{ id: 11, title: 'مدیر' }, { id: 12, title: 'مدرس' }], permissions: [{ id: 21, title: 'مشاهده', group: 'academy' }, { id: 22, title: 'ویرایش', group: 'academy' }, { id: 23, title: 'ارسال', group: 'academy' }, { id: 24, title: 'حذف', group: 'academy' }], branches: [], total: 1, page: 1, perPage: 20 } }) });
      if (uri === '/analytics/admin-user-access/8') {
        const params = new URLSearchParams(route.request().postData());
        const data = JSON.parse(Buffer.from(params.get('payload_b64'), 'base64url').toString('utf8'));
        writes.push(data);
        if ('roleIds' in data) {
          member.roles = data.roleIds.map((id) => ({ id, title: id === 12 ? 'مدرس' : 'مدیر' }));
          member.rolePermissionIds = data.roleIds.includes(12) ? [21, 23] : [21];
          member.rolePermissions = member.rolePermissionIds.map((id) => ({ id, title: id === 23 ? 'ارسال' : 'مشاهده' }));
        }
        if ('permissionIds' in data) member.permissions = data.permissionIds.map((id) => ({ id, title: ({ 21: 'مشاهده', 22: 'ویرایش', 24: 'حذف' })[id] }));
        return route.fulfill({ contentType: 'application/json', body: JSON.stringify({ success: true }) });
      }
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<html dir="rtl"><style>:root{--theme-500:#10b981}${css}</style><table><tbody id="accessUsersBody"></tbody></table><span id="accessUsersInfo"></span><select id="accessUsersPerPage"><option value="20">20</option></select><div id="accessUsersPagination"></div><input id="accessUserSearch"><select id="accessUserBranch"></select><select id="accessUserStatus"></select><div id="modalContainer"></div><script>window.adminCsrfToken='test';</script>${tableRuntime()}<script src="/assets/Analytics/js/users.js"></script></html>` });
    });
    await page.goto('http://sornaz.test/');
    await page.waitForTimeout(700);
    assert.deepEqual(errors, []);
    assert.match(await page.locator('body').innerText(), /تعیین نقش‌ها/);
    assert.equal(await page.locator('#accessUsersBody tr td').first().innerText(), 'کاربر آزمایشی');
    await page.getByRole('button', { name: 'تعیین نقش‌ها' }).click();
    assert.equal(await page.getByRole('dialog').getByRole('heading').innerText(), 'تعیین نقش‌ها');
    assert.equal(await page.getByRole('dialog').locator('h3, p, .border-t').count(), 0);
    assert.equal(await page.getByRole('dialog').getByRole('button', { name: 'مدیر' }).getAttribute('aria-pressed'), 'true');
    assert.equal(await page.getByRole('dialog').getByRole('button', { name: 'مدرس' }).getAttribute('aria-pressed'), 'false');
    assert.equal(await page.getByRole('dialog').getByRole('button', { name: 'ویرایش' }).count(), 0);
    await page.getByRole('dialog').getByRole('button', { name: 'مدرس' }).click();
    assert.equal(await page.getByRole('dialog').getByRole('button', { name: 'مدرس' }).getAttribute('aria-pressed'), 'true');
    assert.match(await page.getByRole('dialog').getByRole('button', { name: 'مدرس' }).evaluate((el) => getComputedStyle(el).backgroundColor), /\/ 0\.25\)/);
    await page.getByRole('button', { name: 'ذخیره نقش‌ها' }).click();
    assert.deepEqual(writes[0], { roleIds: [11, 12] });
    assert.deepEqual(member.permissions.map((x) => x.id), [21, 22]);
    await page.getByRole('button', { name: 'تعیین دسترسی‌ها' }).click();
    assert.equal(await page.getByRole('dialog').getByRole('heading').innerText(), 'تعیین دسترسی‌ها');
    assert.equal(await page.getByRole('dialog').locator('h3, p, .border-t').count(), 0);
    assert.equal(await page.getByRole('dialog').getByRole('button', { name: 'مشاهده' }).count(), 0);
    assert.equal(await page.getByRole('dialog').getByRole('button', { name: 'ارسال' }).count(), 0);
    await page.getByRole('dialog').getByRole('button', { name: 'ویرایش' }).click();
    await page.getByRole('dialog').getByRole('button', { name: 'حذف' }).click();
    await page.getByRole('button', { name: 'ذخیره دسترسی‌ها' }).click();
    assert.deepEqual(writes[1], { permissionIds: [24, 21] });
    assert.deepEqual(member.roles.map((x) => x.id), [11, 12]);
    await page.getByRole('button', { name: 'مشاهده دسترسی‌ها' }).click();
    assert.match(await page.getByRole('dialog').getByText('دسترسی‌های نقش‌ها').locator('..').innerText(), /مشاهده.*ارسال/s);
    assert.match(await page.getByRole('dialog').getByText('دسترسی‌های مستقل از نقش').locator('..').innerText(), /حذف/);
    assert.doesNotMatch(await page.getByRole('dialog').getByText('دسترسی‌های مستقل از نقش').locator('..').innerText(), /مشاهده/);
    assert.deepEqual(errors, []);
    console.log('User access: separate dialogs, theme selection, and independent saves passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
