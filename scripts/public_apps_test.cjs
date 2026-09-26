const assert = require('node:assert/strict');
const fs = require('node:fs');
const cp = require('node:child_process');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
    const browser = await chromium.launch({channel:'msedge', headless:true});
    try {
        for (const lang of ['fa','en']) for (const width of [390,1400]) for (const app of ['course','social']) {
            const page = await browser.newPage({viewport:{width,height:900}, colorScheme:'dark'});
            const errors = [];
            page.on('pageerror', error => errors.push(error.message));
            let html = cp.execFileSync('php', ['-d','short_open_tag=1','scripts/public_apps_fixture.php',lang,app], {encoding:'utf8'});
            await page.route('**/*', route => {
                const url = new URL(route.request().url());
                if (url.pathname === '/analytics/site-settings') return route.fulfill({json:{data:{colorTheme:'emerald',themeMode:'light'}}});
                if (url.pathname === '/analytics/admin-settings') return route.fulfill({json:{success:true}});
                if (url.pathname.startsWith('/community/api')) return route.fulfill({json:{data:[]}});
                if (url.pathname.startsWith('/assets/') && fs.existsSync('.'+url.pathname)) return route.fulfill({path:'.'+url.pathname});
                if (route.request().resourceType() === 'document') return route.fulfill({contentType:'text/html; charset=utf-8',body:html});
                return route.abort();
            });
            await page.goto('http://public.test/'+(app === 'course' ? 'course-market' : 'community'));
            await page.waitForFunction(() => document.documentElement.dataset.theme === 'emerald');
            assert.equal(await page.locator('html').getAttribute('lang'),lang);
            assert.equal(await page.locator('html').getAttribute('dir'),lang === 'en' ? 'ltr' : 'rtl');
            assert.equal(await page.locator('body > header').count(),1);
            assert.equal(await page.locator('.side-bottom').count(),0);
            const card = app === 'course' ? '.course-market-app .empty' : '.community-app .side-nav';
            assert.equal(await page.locator(card).evaluate(el => getComputedStyle(el).backgroundColor),'rgb(255, 255, 255)','site light preference overrides OS dark mode');
            await page.evaluate(() => { setSiteTheme('rose'); toggleSiteThemeMode(); });
            assert.equal(await page.locator(card).evaluate(el => getComputedStyle(el).backgroundColor),'rgb(23, 32, 51)');
            if (app === 'course') {
                assert.equal(await page.locator('.hero h1').evaluate(el => getComputedStyle(el).fontSize),width < 520 ? '24px' : '30px','site CSS preserves course heading sizes');
                assert.equal(await page.locator('.empty .button').evaluate(el => getComputedStyle(el).color),'rgb(190, 18, 60)');
                if (lang === 'en') assert.match(await page.locator('.course-market-app').innerText(),/No courses have been published yet/);
            }
            if (width < 700) {
                await page.locator('body > header button[onclick="toggleMobileMenu()"]').click();
                assert.equal(await page.locator('#mobileMenu').isVisible(),true);
                await page.locator('#mobileMenu [data-theme-mode]').click();
                assert.equal(await page.locator('html').getAttribute('data-mode'),'light');
                await page.evaluate(() => closeMobileMenu());
            }
            if (app === 'social') {
                const header = await page.locator('body > header').boundingBox();
                const nav = await page.locator(width < 700 ? '.mobile-top' : '.side-nav').boundingBox();
                assert.ok(nav.y >= header.y + header.height - 1,'community navigation clears the site header');
            }
            assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth),false,'no horizontal overflow');
            fs.mkdirSync('storage/public-apps-check', {recursive:true});
            await page.screenshot({path:`storage/public-apps-check/${app}-${lang}-${width}.png`});
            if (app === 'course') {
                html = cp.execFileSync('php', ['-d','short_open_tag=1','scripts/public_apps_fixture.php',lang,app,'edit'], {encoding:'utf8'});
                await page.goto('http://public.test/course-market/create');
                await page.locator('#add-chapter').click();
                await page.locator('#chapters > .chapter > button').click();
                assert.equal(await page.locator('#chapters .lesson').count(),1);
                if (lang === 'en') {
                    assert.match(await page.locator('#chapters').innerText(),/Chapter title/);
                    assert.match(await page.locator('#chapters').innerText(),/Lesson password/);
                    assert.ok(!/[\u0600-\u06ff]/.test(await page.locator('.course-market-app').innerText()));
                }
                assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth),false,'editor has no horizontal overflow');
            }
            assert.deepEqual(errors,[]);
            await page.close();
        }
        // Render every course view in English, keeping user content untouched.
        for (const mode of ['catalog','manage','library','edit','show','receipt','error']) {
            const html = cp.execFileSync('php',['-d','short_open_tag=1','scripts/public_apps_fixture.php','en','course',mode],{encoding:'utf8'});
            const content = html.match(/<div class="course-market-app">([\s\S]*?)<script>window.COURSE_TRANSLATIONS/)[1];
            assert.ok(!/[\u0600-\u06ff]/.test(content), `English course view: ${mode}`);
        }
        console.log('Public apps: shared header, responsive navigation, site themes and both locales passed.');
    } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
