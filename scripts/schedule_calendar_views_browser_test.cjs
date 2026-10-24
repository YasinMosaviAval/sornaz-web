const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {renderPanelView, tableRuntime} = require('./panel-table-fixture.cjs');
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    for (const language of ['fa','en']) {
      const page = await browser.newPage(), errors = [], requests = [];
      page.on('request', request => requests.push(request.url()));
      page.on('pageerror', error => errors.push(error.message));
      const session = {id:12,termId:2,title:'Fixture lesson',date:'2026-10-17',day:'شنبه',startTime:'09:00',endTime:'10:30',branchId:1,classroomId:4,classroom:'Piano room',lesson:'Piano',mode:'in_person',status:'در انتظار برگزاری',canCancel:true};
      await page.route('**/*', route => route.fulfill({json:{success:true,data:
        route.request().url().includes('week-bounds') ? {firstDate:'2026-10-17',lastDate:'2026-10-24'} : {
          schedules:[session,{...session,id:13,branchId:2,classroomId:5},{...session,id:14,branchId:0,academyId:100,classroomId:6,title:'Academy lesson'}],organizations:[{id:0,academyId:100,name:'Academy root'},{id:1,name:'Fixture branch'},{id:2,name:'Second branch'}],branches:[{id:1,name:'Fixture branch'},{id:2,name:'Second branch'}],classrooms:[{id:4,branchId:1,name:'Piano room'},{id:5,branchId:2,name:'Second room'},{id:6,branchId:0,name:'Academy room'}],lessons:[],timeRanges:[],total:3,page:1,perPage:Number(new URL(route.request().url()).searchParams.get('perPage') || 10),
        }}}));
      await page.goto('http://calendar.test/');
      await page.clock.install({time:new Date('2026-10-18T12:00:00Z')});
      await page.setContent(`<html lang="${language}"><style>.hidden{display:none} [data-calendar-control-row]{display:flex;flex-wrap:nowrap;overflow-x:auto;white-space:nowrap;gap:8px} [data-calendar-control-row]>button{flex-shrink:0}</style>${renderPanelView('schedules',language)}<div id="modalContainer"></div>${tableRuntime()}</html>`);
      await page.evaluate(() => {document.getElementById('schedules').classList.remove('hidden');window.alert=()=>{};});
      await page.addScriptTag({path:'assets/Analytics/js/schedule-templates.js'});
      await page.addScriptTag({path:'assets/Analytics/js/schedules.js'});
      await page.waitForFunction(()=>document.querySelector('#schedulesTable tbody').textContent.includes('Fixture lesson'));
      assert.equal(await page.locator('[data-schedule-view="weekly"]').getAttribute('aria-current'),'page');
      assert.equal(await page.locator('#schedulesWeeklyView').isVisible(),true);
      assert.equal(await page.locator('#schedulesListView').isVisible(),false);
      assert.equal(await page.locator('[data-schedule-view="list"]').textContent(),language==='fa'?'نمایش جدولی':'Table view');
      assert.equal(await page.locator('[data-schedule-view="weekly"]').textContent(),language==='fa'?'نمایش برنامه هفتگی':'Weekly schedule view');
      await page.waitForSelector('#schedulesWeeklyControls');
      assert.equal(await page.locator('#schedulesWeeklyControls').count(),1);
      assert.equal(await page.locator('#schedulesWeeklyControls [data-calendar-control-row]').count(),4);
      assert.equal(await page.locator('#schedulesWeeklyTables section').count(),3);
      assert.equal(await page.locator('#schedulesWeeklyTables button[onclick*=WeeklySchedule]').count(),0);
      await page.locator('#schedulesWeeklyControls button[onclick*=class-time]').click();
      assert.equal(await page.locator('#schedulesWeeklyTables thead th:first-child').allTextContents().then(values => values.every(v => v.includes('\u0628\u0627\u0632\u0647 \u0632\u0645\u0627\u0646\u06cc'))), true);
      assert.equal(await page.locator('#schedulesWeeklyControls button[onclick*=class-time]').evaluate(el=>el.classList.contains('bg-indigo-600')),true);
      await page.setViewportSize({width:390,height:800});
      assert.equal(await page.locator('#schedulesWeeklyControls [data-calendar-control-row]').first().evaluate(el=>el.scrollWidth>el.clientWidth),true);
      await page.setViewportSize({width:1280,height:800});
      const beforeFilters = requests.length;
      await page.evaluate(() => filterSchedulesByBranch('academy'));
      assert.equal(await page.locator('#schedulesWeeklyTables section').count(),1);
      assert.equal(await page.locator('#schedulesWeeklyTables h3').textContent(),'Academy root');
      assert((await page.locator('#schedulesWeeklyTables').textContent()).includes('Academy lesson'));
      await page.evaluate(() => filterSchedulesByBranch('all'));
      assert.equal(await page.locator('#schedulesWeeklyTables section').count(),3);
      assert.equal(requests.length,beforeFilters,'Weekly branch filters must reuse loaded authorized data');
      assert.equal(requests.filter(url=>url.includes('dateFrom=')).length,1,'One weekly range request replaces seven daily requests');
      const beforeViews = requests.length;
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
      assert.equal(requests.length,beforeViews,'Switching loaded views must not fetch again');
      assert.deepEqual(errors,[]);await page.close();
    }
    console.log('Calendar views: fa/en shared tabs, exclusive active state, panels and exports passed.');
  } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
