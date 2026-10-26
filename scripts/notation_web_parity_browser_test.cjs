const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path');
const {execFileSync}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const root=path.resolve(__dirname,'..');
async function clearBackdrop(page){for(const dialog of await page.locator('dialog[open]').all()){assert.equal(await dialog.evaluate(el=>getComputedStyle(el,'::backdrop').backgroundColor),'rgba(0, 0, 0, 0)');assert.equal(await dialog.evaluate(el=>getComputedStyle(el,'::backdrop').backdropFilter),'none');}}
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{const page=await browser.newPage({viewport:{width:390,height:850}});const errors=[];page.on('pageerror',e=>errors.push(e.message));
 for(const locale of ['fa','en']){
  await page.unrouteAll({behavior:'wait'});let saves=0;
  const html=execFileSync('php',['-r',`function base_path($p){return getcwd().'/'.$p;} $boot=['locale'=>'${locale}','userId'=>7,'embedded'=>false,'webStorage'=>true,'omrEnabled'=>true,'websitePanel'=>false];include 'Modules/Notation/Resources/Views/index.php';`],{cwd:root,encoding:'utf8'});
  await page.route('http://notation.test/**',async route=>{const u=new URL(route.request().url());if(u.pathname==='/music-sheets')return route.fulfill({contentType:'text/html',body:html});if(u.pathname.startsWith('/music-sheets/api')){
   let data={items:[],has_more:false};if(u.pathname.endsWith('/instruments'))data=[{id:7,fa:'پیانو',en:'Piano'}];if(route.request().method()==='POST'){saves++;data={...route.request().postDataJSON(),id:91,version:saves,owner_id:7,editable:true};}return route.fulfill({json:{success:true,data}});
  }const file=path.resolve(root,'.'+u.pathname);if(file.startsWith(root+path.sep)&&fs.existsSync(file))return route.fulfill({path:file});return route.fulfill({status:404,body:''});});
  await page.goto('http://notation.test/music-sheets');await page.evaluate(()=>localStorage.clear());await page.reload();await page.locator('[data-action=web-list-view][data-view=cards]').click();
  await page.locator('.import-entry [data-action=web-settings]').click();await page.locator('.notation-settings-form [name=durationMode]').selectOption('2');await page.locator('.notation-settings-form [name=pianoLabelMode]').selectOption('2');await page.locator('.notation-settings-form button').click();assert.equal(await page.locator('dialog').count(),0);await page.locator('[data-action=back]').click();
  // Use the real importer, engraving and save path; only HTTP is a fixture.
  await page.evaluate(()=>{const sheet=NotationModel.fresh();sheet.metadata.title='Web melody';sheet.metadata.instrument='Piano';sheet.metadata.staves=[{clef:'treble'},{clef:'bass'}];sheet.score.measures[0].notes=[{pitch:'C4',duration:'w',staff:1,voice:1,at:0}];window.importPromise=Notation.importContent(JSON.stringify({format:'sornaz-notation',version:1,metadata:sheet.metadata,score:sheet.score}),'test.json');});
  await clearBackdrop(page);await page.locator('[data-action=confirm-import]').click();await page.evaluate(()=>window.importPromise);assert.ok(await page.locator('#score svg').count());
  await page.locator('[data-action=save]').click();await clearBackdrop(page);await page.locator('[data-action=yes]').click();await page.locator('.state.synced').waitFor();assert.equal(saves,0,'local save must not POST');
  assert.equal(await page.evaluate(()=>JSON.parse(localStorage.getItem('notation_web_preferences')).durationMode),2);
  await page.locator('[data-action=back]').click();await page.locator('[data-action=filter][data-mode=mine]').click();await page.locator('.sheet').first().waitFor();
  await page.locator('.sheet [data-action=upload]').click();await page.waitForFunction(()=>Object.values(JSON.parse(localStorage.getItem('notation_web_v1_7')).entries).some(s=>s.uploaded));assert.equal(saves,1);
  await page.locator('.sheet-menu summary').first().click();await page.locator('[data-action=rename]').first().click();await page.locator('dialog input').waitFor();await clearBackdrop(page);assert.equal(await page.locator('dialog input').inputValue(),'Web melody');await page.locator('dialog input').fill('Web melody renamed');await page.locator('dialog input').press('Enter');await page.locator('.sheet .open strong').filter({hasText:'Web melody renamed'}).waitFor();
  await page.locator('[data-action=filter][data-mode=lists]').click();await page.locator('[data-action=web-create-list]').click();assert.equal(await page.locator('dialog').evaluate(el=>getComputedStyle(el,'::backdrop').backgroundColor),'rgba(0, 0, 0, 0)');await page.locator('dialog input').fill('Practice');await page.locator('dialog button[type=submit]').click();await page.getByRole('button',{name:'Practice',exact:false}).waitFor();
  await page.locator('[data-action=filter][data-mode=mine]').click();await page.locator('.sheet-menu summary').first().click();await page.locator('[data-action=add-to-list]').first().click();await clearBackdrop(page);await page.getByRole('dialog').getByRole('button',{name:'Practice',exact:true}).click();
  await page.locator('[data-action=filter][data-mode=lists]').click();await page.locator('[data-action=toggle-list][data-list=Practice]').click();await page.locator('.nested-sheet').waitFor();await page.locator('[data-action=web-list-view][data-view=table]').click();await page.locator('.list-contents table[data-panel-table]').waitFor();assert.equal(await page.locator('.list-contents table tbody tr').count(),1);await page.locator('[data-action=web-list-view][data-view=cards]').click();
  await page.locator('[data-web-search]').fill('Web');await page.waitForTimeout(350);assert.equal(await page.locator('[data-web-search]').evaluate(e=>e===document.activeElement),true);await page.locator('[data-web-search]').fill('');await page.waitForTimeout(350);
  // Actual JSON download retains a portable score and the complete staff layout.
  const downloadPromise=page.waitForEvent('download');await page.locator('.nested-sheet [data-action=download]').click();const download=await downloadPromise;assert.ok(download.suggestedFilename().endsWith('.json'));
  await page.locator('[data-action=web-omr]').click();await clearBackdrop(page);assert.equal(await page.locator('dialog input[type=file]').first().getAttribute('accept'),'image/png,image/jpeg,image/webp');
  const image=await page.evaluate(()=>{const c=document.createElement('canvas');c.width=400;c.height=120;const ctx=c.getContext('2d');ctx.fillStyle='#fff';ctx.fillRect(0,0,400,120);ctx.fillStyle='#000';for(const y of [30,40,50,60,70])ctx.fillRect(10,y,380,1);ctx.beginPath();ctx.ellipse(150,65,6,4,0,0,Math.PI*2);ctx.fill();ctx.fillRect(155,35,2,31);return c.toDataURL('image/png').split(',')[1];});
  await page.locator('dialog input[type=file]').first().setInputFiles({name:'staff.png',mimeType:'image/png',buffer:Buffer.from(image,'base64')});
  await page.waitForFunction(()=>document.querySelector('.omr-image').width===400);
  await page.getByRole('button',{name:locale==='fa'?'تشخیص نت‌ها':'Detect notes',exact:true}).click();await page.locator('[data-omr-pitch]').first().waitFor();
  assert.ok(await page.locator('.omr-note').count());assert.equal(await page.locator('.omr-note select').first().inputValue(),'','OMR must not invent rhythm');assert.equal(saves,1,'image recognition never uploads');
  for(const input of await page.locator('[data-omr-pitch]').all())await input.fill('C4');
  for(const select of await page.locator('.omr-note select').all())await select.selectOption('w');
  await page.getByRole('button',{name:locale==='fa'?'تأیید و پیش‌نمایش نت':'Confirm and preview score',exact:true}).click();
  await page.locator('.import-score-preview svg').first().waitFor();await page.locator('[data-action=cancel-import]').click();
  assert.equal(await page.locator('.omr-review').count(),1,'cancel import returns to manual review');assert.equal(saves,1);
  await page.locator('dialog button').last().click();
  await page.context().setOffline(true);await page.locator('[data-action=filter][data-mode=mine]').click();await page.locator('.sheet [data-action=open]').first().click();await page.locator('#score svg').first().waitFor();await page.context().setOffline(false);
  await page.setViewportSize({width:1280,height:850});assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false);await page.setViewportSize({width:390,height:850});
 }
 assert.deepEqual(errors,[]);console.log('Notation web browser: fa/en, settings, real import/engraving, local save, explicit upload, lists, portable download, image dialog and offline reopen passed.');
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
