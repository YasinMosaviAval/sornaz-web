const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {tableRuntime}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const language of ['fa','en']){
  const page=await browser.newPage();const errors=[];page.on('pageerror',error=>errors.push(error.message));
  await page.setContent('<html lang="'+language+'"><main id="mainContent"><section class="section" id="generic"><section class="admin-ui-table"><table id="genericTable"><thead><tr><th>Title</th><th>Amount</th><th>Future column</th><th>Actions</th></tr></thead><tbody></tbody></table><footer data-panel-pagination></footer></section></section></main>'+tableRuntime());
  await page.evaluate(()=>{
   const rows=Array.from({length:13},(_,i)=>({cells:[{value:'Row '+(13-i)},{value:(13-i).toLocaleString(document.documentElement.lang==='fa'?'fa-IR':'en')},{value:'Added '+(i+1)},{html:'<button>Details</button>'}]}));
   AdminPanelTable.paginate('#genericTable',{rows,perPage:10});
  });
  assert.equal(await page.locator('#genericTable [data-panel-auto-sort]').count(),3,'A new column needs no sorting handler');
  const values=()=>page.locator('#genericTable tbody tr').evaluateAll(rows=>rows.map(row=>row.cells[row.cells.length===5?2:1].textContent));
  await page.locator('[data-panel-auto-sort="1"]').click();
  const expected=Array.from({length:13},(_,i)=>(i+1).toLocaleString(language==='fa'?'fa-IR':'en'));
  assert.deepEqual(await values(),expected.slice(0,10),'Sorts complete dataset before page slicing');
  await page.locator('[data-panel-pagination] button').last().click();
  assert.deepEqual(await values(),expected.slice(10));
  await page.locator('[data-panel-auto-sort="1"]').click();
  assert.deepEqual(await values(),expected.slice().reverse().slice(10),'Descending persists on the second page');
  await page.locator('[data-panel-pagination] button').first().click();
  await page.locator('[data-panel-auto-sort="2"]').click();
  assert.equal(await page.locator('#genericTable tbody tr').first().textContent().then(text=>text.includes('Added 1')),true);
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Shared automatic sorting: all/new columns, fa/en numbers, ascending/descending and complete-data pagination passed.');
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
