const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const browser = await chromium.launch({ channel: 'msedge', headless: true });
  try {
    const page = await browser.newPage();
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    await page.route('**/*', (route) => {
      const path = new URL(route.request().url()).pathname;
      if (path === '/filters.js') return route.fulfill({ path: './assets/Analytics/js/panel-organization-filters.js' });
      if (path === '/branches.js') return route.fulfill({ path: './assets/Analytics/js/branches.js' });
      if (path === '/member-schedules.js') return route.fulfill({ path: './assets/Analytics/js/member-schedules.js' });
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `
        <nav id="dashboardTabsShell" class="admin-ui-organization-tabs"><div data-slot="tabs"><div id="dashboardBranchTabs"><button>همه</button></div></div></nav>
        <div id="scheduleTabsShell"><div id="memberSchedulesBranchTabs"><button data-value="all">همه</button></div></div>
        <script>window.testOrganizations=[{user_id:10,name:'آموزشگاه نمونه',kind:'academy'}];window.getBranchScheduleOrganizations=()=>window.testOrganizations;</script>
        <script src="/filters.js"></script><script src="/branches.js"></script><script src="/member-schedules.js"></script>` });
    });
    await page.goto('http://sornaz.test/');
    await page.evaluate(async () => {
      await window.renderMemberSchedulesBranchTabs();
      window.academyHasNoBranches = true;
      window.applyAcademyOrganizationTabs();
    });
    assert.equal(await page.locator('#dashboardTabsShell.hidden, #scheduleTabsShell.hidden').count(), 2);
    assert.equal(await page.locator('#memberSchedulesBranchTabs button').allTextContents().then((labels) => labels.filter((label) => label === 'آموزشگاه نمونه').length), 0);

    await page.evaluate(async () => {
      window.testOrganizations.push({ user_id: 11, name: 'شعبه واقعی', kind: 'branch' });
      await window.renderMemberSchedulesBranchTabs();
      window.academyHasNoBranches = false;
      window.applyAcademyOrganizationTabs();
    });
    assert.equal(await page.locator('#dashboardTabsShell.hidden, #scheduleTabsShell.hidden').count(), 0);
    assert.deepEqual(await page.locator('#memberSchedulesBranchTabs button').allTextContents(), ['همه', 'آموزشگاه', 'شعبه واقعی']);
    await page.evaluate(() => { window.branchAccount = true; window.applyAcademyOrganizationTabs(); });
    assert.equal(await page.locator('#dashboardTabsShell.hidden, #scheduleTabsShell.hidden').count(), 2);
    await page.evaluate(() => {
      window.branchAccount = false;
      window.renderPanelOrganizationTabs('dashboardBranchTabs', 'all', () => {}, {branches:[
        {id:30,name:'Third',createdAt:'2026-03-01'},
        {id:10,name:'First',createdAt:'2026-01-01'},
        {id:20,name:'Second',createdAt:'2026-02-01'}
      ]});
    });
    assert.deepEqual(await page.locator('#dashboardBranchTabs button').allTextContents().then(v=>v.slice(-3)),['First','Second','Third']);
    assert.deepEqual(errors, []);
    const legacy = await browser.newPage();
    legacy.on('pageerror', (error) => errors.push(error.message));
    await legacy.route('**/*', async (route) => {
      const path = new URL(route.request().url()).pathname;
      if (path === '/branches.js') return route.fulfill({ path: './assets/Analytics/js/branches.js' });
      if (path === '/assets/Analytics/js/panel-organization-filters.js') {
        await new Promise((resolve) => setTimeout(resolve, 100));
        return route.fulfill({ path: './assets/Analytics/js/panel-organization-filters.js' });
      }
      return route.fulfill({ contentType: 'text/html; charset=utf-8', body: `<div><div id="termsBranchTabs"></div></div><script>window.academyPanelBranches=[{id:101,name:'شعبه واقعی'}];window.academyHasNoBranches=false;window.filterTermsByBranch=(value)=>window.selectedScope=value;</script><script src="/branches.js"></script><script>renderPanelOrganizationTabs('termsBranchTabs','all',filterTermsByBranch);</script>` });
    });
    await legacy.goto('http://sornaz.test/legacy-panel');
    await legacy.waitForSelector('#termsBranchTabs [data-value="101"]');
    assert.deepEqual(await legacy.locator('#termsBranchTabs button').allTextContents(), ['همه', 'آموزشگاه', 'شعبه واقعی']);
    await legacy.locator('#termsBranchTabs [data-value="academy"]').click();
    assert.equal(await legacy.evaluate(() => window.selectedScope), 'academy');
    assert.deepEqual(errors, []);
    console.log('Organization tabs: branchless academy hides filters; first real branch restores them without duplicate academy name.');
  } finally {
    await browser.close();
  }
})().catch((error) => { console.error(error); process.exitCode = 1; });
