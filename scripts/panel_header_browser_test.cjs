const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    for (const width of [390, 1280]) {
      const page = await browser.newPage({ viewport: { width, height: 800 } });
      const errors = [];
      page.on('pageerror', (error) => errors.push(error.message));
      await page.route('**/*', async (route) => {
        const uri = new URL(route.request().url()).pathname;
        if (uri === '/assets/Analytics/js/admin.js') return route.fulfill({ path: './assets/Analytics/js/admin.js' });
        return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<body dir="rtl"><style>.hidden{display:none}</style><header style="display:flex;gap:8px"><button id="mobileMenuBtn">☰</button><h1 id="panelPageTitle">داشبورد</h1></header><div id="sidebar"><a onclick="showSection('dashboard')">داشبورد</a><a onclick="showSection('account')">حساب کاربری</a><a onclick="showSection('settings')">تنظیمات</a></div><section id="dashboard" class="section"><div><h1>داشبورد من</h1><p>توضیح داشبورد</p></div><h2>درخواست‌ها</h2></section><section id="account" class="section hidden"><div><div><h1>حساب کاربری</h1><p>توضیح حساب</p></div><button id="accountAction">ویرایش</button></div><h2>پروفایل</h2></section><section id="settings" class="section hidden"><div><h1>تنظیمات</h1><p>توضیح تنظیمات</p></div><h2>بخش تنظیمات</h2></section><section id="member-schedules" class="section hidden" data-personal-section="1"><h1>برنامه زمانی</h1><table><tr><td>برنامه شخصی</td></tr></table></section><section id="lessons" class="section hidden" data-personal-section="1"><h1>درس‌ها</h1><table><tr><td>درس شخصی</td></tr></table></section><script src="/assets/Analytics/js/admin.js"></script>` });
      });
      await page.goto('http://sornaz.test/');
      await page.evaluate(() => showSection('account'));
      assert.equal(await page.locator('#panelPageTitle').innerText(), 'حساب کاربری');
      assert.equal(await page.locator('#account h1').isVisible(), false);
      assert.equal(await page.locator('#account p').isVisible(), false);
      assert.equal(await page.locator('#accountAction').isVisible(), true);
      assert.equal(await page.locator('#account h2').isVisible(), true);
      await page.evaluate(() => showSection('settings'));
      assert.equal(await page.locator('#panelPageTitle').innerText(), 'تنظیمات');
      assert.equal(await page.locator('#settings h1').isVisible(), false);
      assert.equal(await page.locator('#settings p').isVisible(), false);
      await page.evaluate(() => showSection('dashboard'));
      assert.equal(await page.locator('#panelPageTitle').innerText(), 'داشبورد من');
      assert.equal(await page.locator('#dashboard h2').isVisible(), true);
      await page.evaluate(() => {
        window.loadMemberSchedules = () => { throw Error('Management API used for personal schedule'); };
        window.loadLessonDatabaseData = () => { throw Error('Management API used for personal lessons'); };
        showSection('member-schedules');
        showSection('lessons');
      });
      assert.equal(await page.locator('#lessons td').innerText(), 'درس شخصی');
      if (width < 600) assert.ok(await page.evaluate(() => document.getElementById('panelPageTitle').getBoundingClientRect().left < document.getElementById('mobileMenuBtn').getBoundingClientRect().left));
      assert.deepEqual(errors, []);
      await page.close();
    }
    console.log('Panel header: current page title updates on desktop and mobile passed.');
  } finally { await browser.close(); }
})().catch((error) => { console.error(error); process.exitCode = 1; });
