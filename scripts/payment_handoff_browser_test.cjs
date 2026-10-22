const assert = require('node:assert/strict');
const {execFileSync} = require('node:child_process');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const render = language => execFileSync('php', ['-r',
  'function e($s){return htmlspecialchars($s,ENT_QUOTES,"UTF-8");} function locale(){return $GLOBALS["argv"][1];} $gatewayUrl="https://sandbox.zarinpal.com/pg/StartPay/TEST_AUTHORITY"; require "Modules/Academy/Resources/Views/payments/continue.php";',language], {encoding:'utf8'});
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    for (const javaScriptEnabled of [true,false]) {
      const context = await browser.newContext({javaScriptEnabled});
      for (const language of ['fa','en']) {
        const page = await context.newPage(), errors = [], requests = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.route('**/*', async route => {
          const request = route.request(), url = new URL(request.url());
          if (url.hostname === 'sandbox.zarinpal.com') {
            requests.push(request.headers());
            return route.fulfill({contentType:'text/html',body:'<h1>Simulated gateway</h1>'});
          }
          if (url.pathname === '/assets/Analytics/js/payment-continue.js') return route.fulfill({path:'assets/Analytics/js/payment-continue.js'});
          return route.fulfill({contentType:'text/html; charset=utf-8',headers:{'Referrer-Policy':'origin','Cache-Control':'no-store, private','Content-Security-Policy':"default-src 'none'; script-src 'self'; base-uri 'none'; frame-ancestors 'none'; form-action 'none'"},body:render(language)});
        });
        // An external-app launch has no Referer; the merchant document must create it.
        await page.goto('https://merchant.test/academy/payments/zarinpal/continue?kind=invoice&token='+ 'a'.repeat(64));
        if (!javaScriptEnabled) await page.locator('#merchantPaymentContinue').click();
        await page.waitForURL('https://sandbox.zarinpal.com/pg/StartPay/TEST_AUTHORITY');
        assert.equal(requests.length,1);
        assert.equal(requests[0].referer,'https://merchant.test/');
        assert(!requests[0].referer.includes('token'));
        assert.deepEqual(errors,[]);
        await page.close();
      }
      await context.close();
    }
    console.log('Payment handoff: merchant-origin Referer, token privacy, automatic navigation and no-JavaScript fallback passed in fa/en.');
  } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
