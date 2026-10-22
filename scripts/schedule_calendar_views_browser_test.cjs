const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {renderPanelView, tableRuntime} = require('./panel-table-fixture.cjs');
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    for (const language of ['fa','en']) {
      const page = await browser.newPage(), errors = [];
      page.on('pageerror', error => errors.push(error.message));
      const session = {id:12,termId:2,title:'Fixture lesson',date:'2026-10-17',day:'شنبه',startTime:'09:00',endTime:'10:30',branchId:1,classroomId:4,classroom:'Piano room',lesson:'Piano',mode:'in_person',status:'در انتظار برگزاری',canCancel:true};
      await page.route('**/*', route => route.fulfill({json:{success:true,data:
        route.request().url().includes('week-bounds') ? {firstDate:'2026-10-17',lastDate:'2026-10-24'} : {
          schedules:[session],branches:[{id:1,name:'Fixture branch'}],classrooms:[{id:4,branchId:1,name:'Piano room'}],lessons:[],timeRanges:[],total:1,page:1,perPage:100,
        }}}));
      await page.goto('http://calendar.test/');
      await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style>${renderPanelView('schedules',language)}<div id="modalContainer"></div>${tableRuntime()}</html>`);
      await page.evaluate(() => {document.getElementById('schedules').classList.remove('hidden');window.alert=()=>{};});
      await page.addScriptTag({path:'assets/Analytics/js/schedule-templates.js'});
      await page.addScriptTag({path:'assets/Analytics/js/schedules.js'});
      await page.waitForFunction(()=>document.querySelector('#schedulesTable tbody').textContent.includes('Fixture lesson'));
      assert.equal(await page.locator('[data-schedule-view="weekly"]').getAttribute('aria-current'),'page');
      assert.equal(await page.locator('#schedulesWeeklyView').isVisible(),true);
      assert.equal(await page.locator('#schedulesListView').isVisible(),false);
      assert.equal(await page.locator('[data-schedule-view="list"]').textContent(),language==='fa'?'نمایش جدولی':'Table view');
      assert.equal(await page.locator('[data-schedule-view="weekly"]').textContent(),language==='fa'?'نمایش برنامه هفتگی':'Weekly schedule view');
      for (const view of ['weekly','list','weekly','list']) {
        await page.locator(`[data-schedule-view="${view}"]`).click();
        assert.equal(await page.locator('#schedules > nav button[aria-current="page"]').count(),1);
        assert.equal(await page.locator('#schedules > nav button.bg-indigo-600').count(),1);
        const active=page.locator(`[data-schedule-view="${view}"]`);
        assert.equal(await active.getAttribute('aria-current'),'page');
        const inactive=page.locator(`[data-schedule-view="${view==='list'?'weekly':'list'}"]`);
        assert(await inactive.evaluate(node=>node.classList.contains('bg-white')&&!node.classList.contains('bg-indigo-600')));
        assert.equal(await page.locator('#schedulesListView').isVisible(),view==='list');
        assert.equal(await page.locator('#schedulesWeeklyView').isVisible(),view==='weekly');
        assert.equal(await page.locator('button[onclick="exportSchedulesToExcel()"]').isVisible(),true);
        assert.equal(await page.locator('button[onclick="exportSchedulesToPDF()"]').isVisible(),true);
      }
      assert.deepEqual(errors,[]);await page.close();
    }
    console.log('Calendar views: fa/en shared tabs, exclusive active state, panels and exports passed.');
  } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
