const assert = require('node:assert/strict');
const fs = require('node:fs');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
    const browser = await chromium.launch({channel:'msedge', headless:true});
    try {
        for (const user of [0,1,2,7]) for (const lang of ['fa','en']) {
            const page = await browser.newPage();
            const errors=[];page.on('pageerror',error=>errors.push(error.message));
            let publicRequests=0;
            await page.route('**/*', route => {
                const url=new URL(route.request().url());
                if (url.pathname === '/analytics/inline-translations') {
                    publicRequests++;
                    return route.fulfill({json:{success:true,translations:[{key:'admin.ui.original',fa:'عنوان جدید',en:'Updated title',aliases:['داشبورد','Dashboard']}]}});
                }
                if (url.pathname.startsWith('/assets/')) return route.fulfill({path:'.'+url.pathname});
                return route.fulfill({contentType:'text/html; charset=utf-8',body:`<!doctype html><html lang="${lang}" data-help-enabled="${user===1?1:0}" data-inline-can-edit="${user===1||user===7?1:0}"><body><main><section id="page-home" class="site-page active"><h1>${lang==='fa'?'داشبورد':'Dashboard'}</h1><input placeholder="Dashboard"><div id="later"></div></section></main><div id="modalContainer"></div><script src="/assets/Analytics/js/admin-inline-editor.js"></script><script src="/assets/theme/help-center.js"></script></body></html>`});
            });
            await page.goto('http://sornaz.test/');
            const expected=lang==='fa'?'عنوان جدید':'Updated title';
            await page.waitForFunction(value=>document.querySelector('h1').textContent===value,expected);
            assert.equal(await page.locator('input').getAttribute('placeholder'),expected);
            assert.equal(publicRequests,1);
            assert.equal(await page.locator('.context-help-button').count(),user===1?1:0);
            await page.evaluate(()=>{document.querySelector('#later').innerHTML='<p>Dashboard</p>';window.AdminInlineEditor.setMode(true);});
            await page.waitForFunction(value=>document.querySelector('#later p').textContent===value,expected);
            assert.equal(await page.evaluate(()=>document.documentElement.classList.contains('admin-inline-editing')),user===1||user===7);
            assert.deepEqual(errors,[]);
            await page.close();
        }
        console.log('Public copy: 8 browser scenarios passed (guest, founder, member, other admin; fa/en).');
    } finally { await browser.close(); }
})().catch(error=>{console.error(error);process.exitCode=1;});
