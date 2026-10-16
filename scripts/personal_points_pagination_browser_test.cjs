const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const { tableRuntime } = require('./panel-table-fixture.cjs');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try {
  const page=await browser.newPage();const errors=[];page.on('pageerror',e=>errors.push(e.message));
  for(const count of [0,10,11,23]){
   const views=JSON.parse(execFileSync(process.execPath,['scripts/panel_activity_text_test.cjs'],{encoding:'utf8',env:{...process.env,ACTIVITY_VIEWS:'1',ACTIVITY_POINT_COUNT:String(count)}}));
   await page.setContent('<html lang="en"><body><main id="mainContent">'+views.points+'</main>'+tableRuntime()+'</body></html>');
   await page.locator('#points').evaluate(x=>x.classList.remove('hidden'));
   const table=page.locator('#myPointsTable'),footer=page.locator('#my-points [data-panel-pagination]');
   assert.equal(await table.locator('tbody tr').count(),count?Math.min(10,count):1);
   assert.equal(await footer.isHidden(),count<=10);
   assert.equal(await table.locator('th').count(),6);
   assert.equal(await table.locator('tbody tr').first().locator('td').count(),count?6:1);
   assert.equal(await table.evaluate(x=>Boolean(x.closest('.admin-ui-table'))),true);
   if(count>10){
    assert.match(await footer.textContent(),new RegExp('Showing 1 to 10 of '+count));
    await footer.getByRole('button',{name:'Next',exact:true}).click();
    assert.equal(await table.locator('tbody tr').first().locator('td').first().textContent(),'11');
    assert.equal(await table.locator('tbody tr').count(),Math.min(10,count-10));
    const last=footer.getByRole('button',{name:'Last',exact:true});
    if(await last.isEnabled()) await last.click();
    assert.equal(await table.locator('tbody tr').count(),count%10||10);
    assert.equal(await table.locator('tbody tr').first().locator('td').first().textContent(),count===23?'21':'11');
    await footer.getByRole('button',{name:'First',exact:true}).click();
    assert.equal(await table.locator('tbody tr').first().locator('td').first().textContent(),'1');
   }
   if(count===23){
    const sort=table.locator('[data-panel-sort="2"]');
    await sort.click();
    assert.equal(await table.locator('tbody tr').first().locator('td').nth(2).textContent(),'5');
    await footer.getByRole('button',{name:'Last',exact:true}).click();
    assert.equal(await table.locator('tbody tr').last().locator('td').nth(2).textContent(),'12');
    await sort.click();
    assert.equal(await table.locator('tbody tr').first().locator('td').nth(2).textContent(),'12');
    await footer.getByRole('button',{name:'Next',exact:true}).click();
    assert.equal(await table.locator('tbody tr').first().locator('td').nth(2).textContent(),'5');
    const type=page.locator('#myPointsTypeFilter'),action=page.locator('#myPointsActionFilter');
    const types=await type.locator('option').evaluateAll(xs=>xs.map(x=>x.value));
    const actions=await action.locator('option').evaluateAll(xs=>xs.map(x=>x.value));
    await type.selectOption(types[2]);
    assert.equal(await table.locator('tbody tr').count(),1);
    assert.equal(await table.locator('tbody tr').first().locator('td').nth(2).textContent(),'12');
    assert.equal(await footer.isHidden(),true);
    await action.selectOption(actions[2]);
    await type.selectOption(types[1]);
    assert.equal(await table.locator('tbody tr td').count(),1,'Combined filters can return no results');
    await action.selectOption(actions[1]);
    assert.equal(await table.locator('tbody tr').count(),10);
    assert.match(await footer.textContent(),/of 22/);
    assert.equal(await sort.locator('..').getAttribute('aria-sort'),'descending');
    await type.selectOption('');await action.selectOption('');
    for(let index=0;index<6;index++){
     const button=table.locator('[data-panel-sort="'+index+'"]');
     await button.click();
     // Points was previously sorted descending, so its next click also starts ascending.
     assert.equal(await button.locator('..').getAttribute('aria-sort'),'ascending');
     await button.click();
     assert.equal(await button.locator('..').getAttribute('aria-sort'),'descending');
    }
   }
  }
  assert.deepEqual(errors,[]);console.log('Personal points pagination: empty, 10, 11 and 23 rows, shared wrapper, first/next/last and numbering passed.');
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
