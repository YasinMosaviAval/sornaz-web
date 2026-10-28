const assert = require('node:assert/strict');
const fs = require('node:fs');
const {execFileSync} = require('node:child_process');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const {tableRuntime} = require('./panel-table-fixture.cjs');

const render = lang => execFileSync('php', ['scripts/panel-table-view-fixture.php', 'database-management', lang, '', '1'], {encoding:'utf8'});
const layoutCss = fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php', 'utf8').match(/<style>([\s\S]*?)<\/style>/)[1];
const specialTable = 'f_آزمون \'"<img src=x>&+?';
const privateColumn = 'private_<img src=x onerror="window.dbInjected=1">';
const richValue = '<img src=x onerror="window.dbInjected=1"> متن فارسی 🎼';
const bigInteger = '18446744073709551615';
const longValue = 'PRIVATE_LONG_' + 'توضیحات طولانی '.repeat(40) + '\nEND';
const tableNames = [specialTable, 'f_new_rows', ...Array.from({length:21}, (_,i)=>'f_table_' + (i+3))];
const tables = tableNames.map((name,index)=>({name,type:'BASE TABLE',engine:'InnoDB',estimatedRows:index===2?null:13,bytes:index===3?null:4096}));
const envelope = (data,status=200) => ({status,data:{success:status===200,...(status===200?{data}:{message:'Fixture database ' + status})}});
const columns = ['id',privateColumn,'null_value','binary_value','decimal_value','unicode_value','long_value'].map((name,index)=>({name,type:index===0?'bigint':'text',nullable:true,primary:index===0}));
const rowData = table => Array.from({length:13}, (_,index)=>({id:index?String(index+1):bigInteger,[privateColumn]:table==='f_new_rows'?'NEW_ROW_VALUE':richValue,null_value:null,binary_value:{type:'binary',encoding:'base64',value:'AP8nPD4='},decimal_value:'9007199254740993.123456',unicode_value:'سلام 🎵\nخط دوم',long_value:longValue}));

async function firstPage(page) {
  await page.locator('#databaseTableSearch').fill('');
  await page.evaluate(()=>loadDatabaseTables(1));
  await page.waitForTimeout(275);
  await page.waitForFunction(()=>document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===10);
}
async function openTable(page,index=0) {
  await page.locator('[data-database-tab="tables"]').click();
  await page.locator('#databaseTables [data-database-action="view-rows"]').nth(index).click();
  await page.waitForFunction(()=>document.querySelectorAll('#databaseRows tbody tr').length===10);
}
async function assertCleared(page) {
  const html = await page.locator('#database-management').innerHTML();
  for(const value of [specialTable,privateColumn,bigInteger,'PRIVATE_LONG_','NEW_ROW_VALUE','AP8nPD4=']) {
    assert.equal(await page.locator('#database-management').evaluate((node,value)=>node.textContent.includes(value),value),false,'Private value remains after access failure: ' + value);
  }
  assert.equal(await page.locator('#databaseSelectedTable').textContent(),'');
  assert.equal(await page.locator('#databaseTableSearch').inputValue(),'');
  assert.equal(await page.locator('[data-database-tab="rows"]').isDisabled(),true);
  assert.equal(await page.locator('[data-database-panel="tables"]').isVisible(),true);
  assert.equal(await page.locator('#databaseTablesPagination').isVisible(),false);
  assert.equal(await page.locator('#databaseRowsPagination').isVisible(),false);
  assert.equal(await page.locator('iframe').count(),0,'Download frame must be removed on authorization failure');
  for(const action of ['download-all-schema','download-all-data','download-table-schema','download-table-data']) assert.equal(await page.locator(`[data-database-action="${action}"]`).isDisabled(),true);
  assert.ok(!html.includes('window.dbInjected=1'),'Sensitive escaped column markup must also be removed');
}

(async()=>{
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    for(const lang of ['fa','en']) for(const width of [390,1280]) {
      const page = await browser.newPage({viewport:{width,height:900}}), errors=[], requests=[], downloads=[];
      page.on('pageerror',error=>errors.push(error.message));
      let tablesError=0, rowsError=0, downloadError=0, holdSearch='', holdRows='', releaseTables, releaseRows;
      const downloadResponses=[];
      await page.route('**/*',async route=>{
        const request=route.request(), url=new URL(request.url());
        if(url.pathname.startsWith('/analytics/admin-database/')) {
          requests.push({url,navigation:request.isNavigationRequest(),resource:request.resourceType()});
          if(url.pathname.endsWith('/download')) {
            downloads.push(url);
            if(downloadError)return route.fulfill({status:downloadError,json:envelope(null,downloadError)});
            return route.fulfill({status:200,headers:{'Content-Type':'application/sql','Content-Disposition':'attachment; filename="fixture.sql"','Cache-Control':'no-store, private'},body:'-- synthetic database export\n'});
          }
          if(url.pathname.endsWith('/tables')) {
            const search=url.searchParams.get('search')||'', current=Number(url.searchParams.get('page')||1);
            if(search===holdSearch && holdSearch)await new Promise(resolve=>{releaseTables=resolve;});
            if(tablesError)return route.fulfill({status:tablesError,json:envelope(null,tablesError)});
            const filtered=search==='slow'?[{...tables[0],name:'STALE_TABLE'}]:search==='fast'?[{...tables[0],name:'FAST_TABLE'}]:search==='ten'?tables.slice(0,10):tables.filter(table=>table.name.includes(search));
            return route.fulfill({json:envelope({tables:filtered.slice((current-1)*10,current*10),total:filtered.length,page:current,perPage:10})});
          }
          if(url.pathname.endsWith('/rows')) {
            const table=url.searchParams.get('table'), current=Number(url.searchParams.get('page')||1);
            if(table===holdRows && holdRows)await new Promise(resolve=>{releaseRows=resolve;});
            if(rowsError)return route.fulfill({status:rowsError,json:envelope(null,rowsError)});
            return route.fulfill({json:envelope({table,columns,rows:rowData(table).slice((current-1)*10,current*10),total:13,page:current,perPage:10})});
          }
        }
        return route.fulfill({body:'',contentType:'text/html'});
      });
      await page.goto('http://database-ui.test/');
      await page.setContent(`<html lang="${lang}" dir="${lang==='en'?'ltr':'rtl'}" data-mode="light" data-theme="emerald"><style>${layoutCss}.hidden,[hidden]{display:none!important}html,body{margin:0}body{padding:16px}#mainContent{min-width:0}</style><header><h1 id="panelPageTitle"></h1></header><main id="mainContent">${render(lang)}</main><div id="modalContainer"></div>${tableRuntime()}</html>`);
      await page.addScriptTag({path:'assets/vendor/tailwind/tailwindcss.js'});
      await page.addStyleTag({path:'assets/theme/theme.css'});
      await page.evaluate(()=>{
        localStorage.clear();sessionStorage.clear();localStorage.setItem('canary','keep');sessionStorage.setItem('canary','keep');
        window.dbFetches=[];window.dbIgnoreAbort=false;
        const fetch=window.fetch.bind(window);
        window.fetch=(url,options)=>{dbFetches.push({url:String(url),cache:options?.cache,credentials:options?.credentials});const actual={...options};if(dbIgnoreAbort)delete actual.signal;return fetch(url,actual);};
      });
      for(const file of ['admin','database-management'])await page.addScriptTag({path:`assets/Analytics/js/${file}.js`});
      await page.evaluate(()=>{watchPanelAddActions();showSection('database-management');});
      await page.waitForFunction(()=>document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===10);
      assert.equal(await page.locator('#databaseTables [data-admin-identity-cell]').count(),0);
      assert.equal(await page.locator('#databaseTables tbody tr').count(),10);
      assert.equal(await page.locator('#databaseTables tbody tr').first().locator('td').first().textContent(),specialTable);
      await page.waitForFunction(()=>document.querySelector('#databaseTables [data-database-action="view-rows"]')?.dataset.panelTableAction==='details');
      for(const mode of ['light','dark']) {
        await page.evaluate(mode=>document.documentElement.dataset.mode=mode,mode);
        assert.equal(await page.locator('#databaseTablesPagination').isVisible(),true);
        assert.equal(await page.locator('#databaseTablesPagination button').first().textContent(),lang==='en'?'First':'اول');
        assert.equal(await page.locator('#databaseTablesPagination button').last().textContent(),lang==='en'?'Last':'آخر');
        await page.mouse.move(5,5);
        await page.locator('[data-database-action="download-all-schema"]').hover();
        await page.locator('#panelActionTooltip:visible').waitFor();
        assert.ok((await page.locator('#panelActionTooltip').textContent()).includes(lang==='en'?'schema':'ساختار'));
        await page.keyboard.press('Escape');
      }
      if(lang==='fa'&&width===390)await page.screenshot({path:'storage/notation-browser-check/database-management-fa390.png'});
      await page.locator('#databaseTablesPagination button').last().click();
      await page.waitForFunction(()=>document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===3);
      await page.locator('#databaseTablesPagination button').first().click();
      await page.waitForFunction(()=>document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===10);
      for(const action of ['download-all-schema','download-all-data','table-schema','table-data']) {
        const download=page.waitForEvent('download');
        await page.locator(`[data-database-action="${action}"]`).first().click();await download;
      }
      assert.deepEqual(downloads.slice(-4).map(url=>[url.searchParams.get('mode'),url.searchParams.get('table')||'']),[['schema',''],['data',''],['schema',specialTable],['data',specialTable]]);
      assert.ok(requests.filter(item=>item.url.pathname.endsWith('/download')).every(item=>item.navigation&&item.resource==='document'),'Exports must navigate a download frame, not fetch a large blob');
      await page.locator('#databaseTableSearch').fill('ten');
      await page.waitForFunction(()=>document.getElementById('databaseTablesPagination').hidden&&document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===10);
      await page.locator('#databaseTableSearch').fill('not-present');
      await page.waitForFunction(()=>document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===0);
      assert.equal(await page.locator('#databaseTablesPagination').isVisible(),false);
      holdSearch='slow';await page.evaluate(()=>dbIgnoreAbort=true);
      await page.locator('#databaseTableSearch').fill('slow');
      await page.waitForRequest(request=>new URL(request.url()).searchParams.get('search')==='slow');
      await page.locator('#databaseTableSearch').fill('fast');
      await page.waitForFunction(()=>document.querySelector('#databaseTables tbody')?.textContent.includes('FAST_TABLE'));
      releaseTables();holdSearch='';await page.waitForTimeout(60);
      assert.ok((await page.locator('#databaseTables tbody').textContent()).includes('FAST_TABLE'));
      assert.ok(!(await page.locator('#databaseTables tbody').textContent()).includes('STALE_TABLE'));
      await firstPage(page);
      await openTable(page);
      assert.equal(await page.locator('#databaseSelectedTable').textContent(),specialTable);
      assert.equal(await page.locator('#databaseRows thead th').nth(1).textContent(),privateColumn);
      assert.equal(await page.locator('#databaseRows tbody tr').first().locator('td').nth(0).textContent(),bigInteger);
      assert.equal(await page.locator('#databaseRows tbody tr').first().locator('td').nth(1).textContent(),richValue);
      assert.equal(await page.locator('#databaseRows tbody tr').first().locator('td').nth(2).textContent(),'NULL');
      assert.equal(await page.locator('#databaseRows tbody tr').first().locator('td').nth(4).textContent(),'9007199254740993.123456');
      assert.equal(await page.locator('#databaseRows tbody tr').first().locator('td').nth(5).textContent(),'سلام 🎵\nخط دوم');
      await page.locator('#databaseRows tbody tr').first().locator('td').nth(3).locator('summary').click();
      assert.equal(await page.locator('#databaseRows tbody tr').first().locator('td').nth(3).locator('pre').textContent(),'AP8nPD4=');
      await page.locator('#databaseRows tbody tr').first().locator('td').nth(6).locator('summary').click();
      assert.equal(await page.locator('#databaseRows tbody tr').first().locator('td').nth(6).locator('pre').textContent(),longValue);
      assert.equal(await page.locator('#database-management img,#database-management script').count(),0);
      if(lang==='en'&&width===1280)await page.screenshot({path:'storage/notation-browser-check/database-management-en1280.png'});
      await page.locator('#databaseRowsPagination button').last().click();await page.waitForFunction(()=>document.querySelectorAll('#databaseRows tbody tr').length===3);
      await page.locator('#databaseRowsPagination button').first().click();await page.waitForFunction(()=>document.querySelectorAll('#databaseRows tbody tr').length===10);
      for(const action of ['download-table-schema','download-table-data']){const download=page.waitForEvent('download');await page.locator(`[data-database-action="${action}"]`).click();await download;assert.equal(downloads.at(-1).searchParams.get('table'),specialTable);}
      holdRows=specialTable;
      const heldRowsRequest=page.waitForRequest(request=>new URL(request.url()).pathname.endsWith('/rows')&&new URL(request.url()).searchParams.get('table')===specialTable);
      await page.locator('[data-database-action="refresh-rows"]').click();
      await heldRowsRequest;
      await page.locator('[data-database-tab="tables"]').click();
      await page.locator('#databaseTables [data-database-action="view-rows"]').nth(1).click();
      await page.waitForFunction(()=>document.querySelector('#databaseRows tbody')?.textContent.includes('NEW_ROW_VALUE'));
      releaseRows();holdRows='';await page.waitForTimeout(60);
      assert.equal(await page.locator('#databaseSelectedTable').textContent(),'f_new_rows');
      assert.ok((await page.locator('#databaseRows tbody').textContent()).includes('NEW_ROW_VALUE'));
      await page.evaluate(()=>dbIgnoreAbort=false);
      rowsError=500;await page.locator('[data-database-action="refresh-rows"]').click();
      await page.waitForFunction(()=>document.querySelector('[data-database-status]')?.textContent.includes('500'));
      rowsError=0;await page.locator('[data-database-action="refresh-rows"]').click();await page.waitForFunction(()=>document.querySelector('#databaseRows tbody')?.textContent.includes('NEW_ROW_VALUE'));
      for(const code of [401,403]) {
        tablesError=code;await page.evaluate(()=>loadDatabaseTables(1));await assertCleared(page);
        tablesError=0;await page.locator('[data-database-action="refresh-tables"]').click();
        await page.waitForFunction(()=>document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===10);
        assert.equal(await page.locator('[data-database-action="download-all-data"]').isEnabled(),true);
        await openTable(page);
      }
      downloadError=403;await page.locator('[data-database-action="download-all-data"]').click();
      await page.waitForFunction(()=>document.querySelector('[data-database-tab="rows"]').disabled);
      await assertCleared(page);
      downloadError=0;await page.locator('[data-database-action="refresh-tables"]').click();await page.waitForFunction(()=>document.querySelectorAll('#databaseTables [data-database-action="view-rows"]').length===10);
      assert.equal(await page.locator('[data-database-action="download-all-data"]').isEnabled(),true);
      assert.deepEqual(await page.evaluate(()=>[Object.keys(localStorage),Object.keys(sessionStorage)]),[['canary'],['canary']]);
      assert.ok((await page.evaluate(()=>dbFetches)).every(call=>call.cache==='no-store'&&call.credentials==='same-origin'));
      assert.equal(await page.evaluate(()=>window.dbInjected),undefined);
      assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1),false);
      assert.deepEqual(errors,[]);await page.close();
    }
    console.log('Database management browser: real PHP/shared tables, fa/en mobile/desktop themes, pagers, search, safe arbitrary cells, frame downloads, stale responses, retry, revoked-access cleanup and no persistent cache passed.');
  } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
