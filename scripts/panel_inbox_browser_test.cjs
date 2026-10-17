const assert = require('node:assert/strict');
const { tableRuntime } = require('./panel-table-fixture.cjs');
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
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<div id="messages" data-read-only="1"><input id="messageSearch"><select id="filterMessageStatus"><option value=""></option></select><table id="messagesTable"><tbody></tbody></table><div id="messagesPagination"><span id="messagesPaginationInfo"></span><div id="messagesPaginationButtons"></div></div></div><div id="notifications" data-read-only="1" data-can-create="0"><div id="notificationsBranchTabs"><button data-value="all"></button></div><input id="notificationSearch"><select id="filterNotificationActivity"><option value=""></option></select><select id="filterNotificationStatus"><option value=""></option></select><select id="filterNotificationPriority"><option value=""></option></select><select id="filterNotificationAudience"><option value=""></option></select><table id="notificationsTable"><tbody></tbody></table><div id="notificationsPagination"><span id="notificationsPaginationInfo"></span><div id="notificationsPaginationButtons"></div></div></div><div id="modalContainer"></div><script>window.matchesOrganizationFilter=()=>true;</script>${tableRuntime()}<script src="/assets/Analytics/js/message-templates.js"></script><script src="/assets/Analytics/js/messages.js"></script><script src="/assets/Analytics/js/notification-templates.js"></script><script src="/assets/Analytics/js/notifications.js"></script>` });
    });
    await page.goto('http://sornaz.test/');
    await page.waitForTimeout(1000);
    assert.match(await page.locator('#messagesTable tbody').innerText(), /Personal message/);
    assert.match(await page.locator('#notificationsTable tbody').innerText(), /Personal notice/);
    assert.equal(await page.locator('#notificationsTable tbody tr').first().locator('td').count(), 6);
    await page.evaluate(() => { document.getElementById('notifications').dataset.siteAdmin = '1'; renderNotificationsTable(); });
    assert.equal(await page.locator('#notificationsTable tbody tr').first().locator('td').count(), 9);
    await page.evaluate(() => { document.getElementById('notifications').dataset.siteAdmin = '0'; renderNotificationsTable(); });
    await page.evaluate(async () => { await viewMessage(10); await viewNotification(12); });
    assert.equal(await page.locator('#messagesTable button[onclick^="deleteMessage"]').count(), 0);
    assert.equal(await page.locator('#notificationsTable button[onclick^="deleteNotification"]').count(), 0);
    await page.route('**/analytics/admin-notifications', route => route.fulfill({json:{success:true,data:{notifications:Array.from({length:12},(_,i)=>({id:i+100,title:'Notice',activityTitle:i<6?'Course activity':'Payment activity',recipientId:i+1,recipientUsername:'recipient'+String(12-i).padStart(2,'0'),status:'published'}))}}}));
    await page.evaluate(async()=>{document.getElementById('notifications').dataset.siteAdmin='1';await loadAdminNotifications();await sortNotificationsBy('recipientUsername');});
    assert.match(await page.locator('#notificationsTable tbody tr').first().locator('td').nth(1).textContent(), /recipient01/);
    await page.evaluate(()=>changeNotificationsPage(2));
    assert.match(await page.locator('#notificationsTable tbody tr').first().locator('td').nth(1).textContent(), /recipient11/);
    await page.evaluate(async()=>{await sortNotificationsBy('recipientUsername');await changeNotificationsPage(1);});
    assert.match(await page.locator('#notificationsTable tbody tr').first().locator('td').nth(1).textContent(), /recipient12/);
    await page.selectOption('#filterNotificationActivity','Payment activity');
    await page.evaluate(()=>filterNotifications());
    assert.equal(await page.locator('#notificationsTable tbody tr').count(),6);
    await page.evaluate(()=>loadAdminNotifications());
    assert.equal(await page.locator('#filterNotificationActivity').inputValue(),'Payment activity');
    assert.equal(await page.locator('#notificationsTable tbody tr').count(),6);
    await page.selectOption('#filterNotificationActivity','');await page.evaluate(()=>filterNotifications());
    assert.equal(await page.locator('#notificationsTable tbody tr').count(),10);
    assert.deepEqual(writes, []);
    assert.deepEqual(errors, []);
    console.log('Panel inbox browser: personal details open read-only without mutation passed.');
  } finally {
    await browser.close();
  }
})().catch((error) => { console.error(error); process.exitCode = 1; });
