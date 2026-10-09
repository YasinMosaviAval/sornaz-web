const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    const page = await browser.newPage();
    const calls = [], errors = [];
    page.on('pageerror', e => errors.push(e.message));
    page.on('dialog', () => errors.push('Native dialog opened'));
    await page.route('**/*', async route => {
      const pathname = new URL(route.request().url()).pathname;
      calls.push(pathname);
      if (pathname.startsWith('/assets/Analytics/js/')) return route.fulfill({path:'.'+pathname});
      if (pathname === '/analytics/admin-account') return route.fulfill({json:{success:true,data:{profile:{name:'Tester',accountType:'human',email:'test@example.com',phone:'09123456789',privacy:{}},documents:[],devices:[],loginHistory:[],securityAlerts:[]}}});
      if (pathname === '/analytics/admin-account/profile' || pathname === '/analytics/admin-account/security') return route.fulfill({json:{success:true}});
      if (pathname.includes('/contact/')) { errors.push('Unchanged contact verification requested'); return route.fulfill({status:422,json:{success:false}}); }
      return route.fulfill({contentType:'text/html; charset=utf-8',body:`<div id="account"></div><input id="editAcademyName" value="Updated"><input id="editProfileEmail" value=" TEST@EXAMPLE.COM "><input id="editProfilePhone" value="0912 345 6789"><input id="accountEmail" value="test@example.com"><input id="accountPhone" value="09123456789"><input id="accountPassword"><input id="accountPasswordConfirm"><script>window.messages=[];window.AppDialog={alert:m=>messages.push(m),prompt:()=>{throw Error('Unexpected OTP prompt')}};window.closeModal=()=>{};</script><script src="/assets/Analytics/js/account.js"></script><script src="/assets/Analytics/js/gallery.js"></script>`});
    });
    await page.goto('http://sornaz.test/');
    await page.evaluate(() => { window.renderAccountInfo=()=>{}; });
    await page.evaluate(() => window.reloadAdminAccount());
    await page.evaluate(() => window.saveProfile());
    await page.evaluate(() => window.saveAccountSettings());
    await page.evaluate(() => window.dispatchEvent(new CustomEvent('admin-media-changed',{detail:{source:'account'}})));
    assert(calls.includes('/analytics/admin-account/profile'));
    assert(calls.includes('/analytics/admin-account/security'));
    assert(!calls.includes('/analytics/admin-gallery'), 'Personal account loaded restricted gallery');
    assert.equal(await page.evaluate(() => messages.length), 2);
    assert.deepEqual(errors, []);
    console.log('Account browser: normalized unchanged contacts, profile/security saves, project dialogs and gallery access passed.');
  } finally { await browser.close(); }
})().catch(error => {console.error(error);process.exitCode=1;});
