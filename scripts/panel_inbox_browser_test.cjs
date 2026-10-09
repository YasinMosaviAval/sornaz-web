const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const writes = [], errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    await page.route('**/*', async (route) => {
      const path = new URL(route.request().url()).pathname;
      if (path.startsWith('/assets/Analytics/js/')) return route.fulfill({ path: '.' + path });
      if (route.request().method() !== 'GET') {
        writes.push(path);
        return route.fulfill({ status: 403, json: { success: false } });
      }
      if (path === '/analytics/admin-messages') return route.fulfill({ json: { success: true, data: { messages: [{ id: 10, title: 'Personal message', body: 'Message body', sender: 'Sender', receiver: 'Me', type: 'پیام', status: 'منتشر شده', readStatus: 'خوانده‌نشده', date: '2026/10/07', incoming: true }], recipients: [], unread: { messages: 1, notifications: 1 } } } });
      if (path === '/analytics/admin-notifications') return route.fulfill({ json: { success: true, data: { notifications: [{ id: 12, title: 'Personal notice', body: 'Notice body', recipientId: 2, recipientUsername: 'me', branchName: 'سیستم', audience: 'همه', priority: 'کم', status: 'منتشر شده', readStatus: 'خوانده‌نشده', date: '2026/10/07' }] } } });
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<div id="messages" data-read-only="1"><input id="messageSearch"><select id="filterMessageStatus"><option value=""></option></select><table id="messagesTable"><tbody></tbody></table><div id="messagesPagination"><span id="messagesPaginationInfo"></span><div id="messagesPaginationButtons"></div></div></div><div id="notifications" data-read-only="1" data-can-create="0"><div id="notificationsBranchTabs"><button data-value="all"></button></div><input id="notificationSearch"><select id="filterNotificationStatus"><option value=""></option></select><select id="filterNotificationPriority"><option value=""></option></select><select id="filterNotificationAudience"><option value=""></option></select><table id="notificationsTable"><tbody></tbody></table><div id="notificationsPagination"><span id="notificationsPaginationInfo"></span><div id="notificationsPaginationButtons"></div></div></div><div id="modalContainer"></div><script>window.matchesOrganizationFilter=()=>true;</script><script src="/assets/Analytics/js/message-templates.js"></script><script src="/assets/Analytics/js/messages.js"></script><script src="/assets/Analytics/js/notification-templates.js"></script><script src="/assets/Analytics/js/notifications.js"></script>` });
    });
    await page.goto('http://sornaz.test/');
    await page.waitForTimeout(1000);
    assert.match(await page.locator('#messagesTable tbody').innerText(), /Personal message/);
    assert.match(await page.locator('#notificationsTable tbody').innerText(), /Personal notice/);
    await page.evaluate(async () => { await viewMessage(10); await viewNotification(12); });
    assert.equal(await page.locator('#messagesTable button[onclick^="deleteMessage"]').count(), 0);
    assert.equal(await page.locator('#notificationsTable button[onclick^="deleteNotification"]').count(), 0);
    assert.deepEqual(writes, []);
    assert.deepEqual(errors, []);
    console.log('Panel inbox browser: personal details open read-only without mutation passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => { console.error(error); process.exitCode = 1; });
