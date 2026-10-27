const assert=require('node:assert/strict'),fs=require('node:fs');
const {execFileSync}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const render=(name,lang)=>execFileSync('php',['scripts/panel-table-view-fixture.php',name,lang,'','1'],{encoding:'utf8'});
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const lang of ['fa','en']){
  const page=await browser.newPage({viewport:{width:390,height:900}}),errors=[];page.on('pageerror',error=>errors.push(error.message));
  await page.route('**/*',route=>route.fulfill({body:'',contentType:'text/html'}));await page.goto('http://panel.test/');
  const css=fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php','utf8').match(/<style>([\s\S]*?)<\/style>/)[1];
  const names=['posts','post-categories','media','teachers','terms','branches','permissions','roles','national-holidays'];
  const add=lang==='en'?'Add lesson':'افزودن درس';
  await page.setContent(`<html lang="${lang}" data-mode="light" data-theme="emerald"><style>.hidden{display:none}${css}</style><aside id="sidebar" class="bg-indigo-900 text-white"><a id="normalMenu" class="nav-link text-white hover:bg-indigo-800">Dashboard</a><a id="yellowMenu" class="nav-link text-yellow-300 hover:bg-indigo-800">Posts</a></aside><main id="mainContent">${names.map(name=>render(name,lang)).join('')}<section id="personal" class="section"><button id="personalAdd" type="button" data-personal-open="lesson" class="bg-indigo-600">${add}</button><button id="rangeAdd" type="button" data-personal-add-range>${add}</button><button id="excel" data-personal-export="lesson">Export Excel</button><button id="pdf" data-personal-print="lesson">خروجی PDF</button><button id="pencil" data-notation-new>✎</button><button id="listAdd" data-notation-create-list>New list</button><table><tr><td><a id="postTitle" data-post-title href="#post-editor">Add rhythm</a><a id="ordinaryTitle" href="/posts/new-theory">New theory</a></td></tr></table><article><button id="storedContent">Add an artistic title</button></article><form><button id="submitAdd">${add}</button><button id="rowAdd" type="button" onclick="addFixtureRow()">${add}</button></form></section></main><div id="modalContainer"><div role="dialog"><button id="saveAdd" type="button" data-branch-save="save-add">${add}</button><button id="saveClick" onclick="saveFixture()">${add}</button></div></div></html>`);
  await page.addStyleTag({path:'assets/theme/theme.css'});
  await page.evaluate(lang=>{window.adminLocale=lang;window.adminUiMap={'افزودن نوشته':'Add post','افزودن':'Add'};window.clicks=[];window.addFixtureRow=()=>clicks.push('row');window.saveFixture=()=>clicks.push('save');document.getElementById('personalAdd').onclick=()=>clicks.push('personal');document.getElementById('rangeAdd').onclick=()=>clicks.push('range');},lang);
  await page.addScriptTag({path:'assets/Analytics/js/admin.js'});
  await page.evaluate(()=>{watchPanelAddActions();document.querySelectorAll('#mainContent>.section').forEach(alignPanelPageActions);});
  for(const name of names)assert.ok(await page.locator('#'+name+' .panel-add-icon').count()>0,'Static '+name+' has a plus add action');
  for(const id of ['personalAdd','rangeAdd','rowAdd','listAdd']){
   const button=page.locator('#'+id);assert.equal(await button.locator(':scope>svg[data-panel-add-symbol]').count(),1,id);assert.ok((await button.getAttribute('aria-label')).length>2);assert.equal(await button.getAttribute('aria-label'),await button.getAttribute('title'));
   assert.deepEqual(await button.evaluate(element=>{const r=element.getBoundingClientRect();return [r.width,r.height];}),[44,44]);assert.equal(await button.locator('.panel-add-label').evaluate(element=>getComputedStyle(element).width),'1px');
  }
  assert.equal(await page.locator('#posts [data-panel-add-icon]').first().getAttribute('aria-label'),lang==='en'?'Add post':'افزودن نوشته');
  for(const id of ['excel','pdf','pencil','postTitle','ordinaryTitle','storedContent','submitAdd','saveAdd','saveClick'])assert.equal(await page.locator('#'+id).getAttribute('data-panel-add-icon'),null,id+' keeps its meaning');
  for(const id of ['personalAdd','rangeAdd','rowAdd'])await page.locator('#'+id).click();assert.deepEqual(await page.evaluate(()=>clicks),['personal','range','row']);
  assert.equal(await page.locator('#personalAdd').evaluate(element=>getComputedStyle(element).backgroundColor),'rgb(5, 150, 105)');
  assert.equal(await page.locator('#sidebar').evaluate(element=>getComputedStyle(element).backgroundColor),'rgb(255, 253, 247)');assert.equal(await page.locator('#normalMenu').evaluate(element=>getComputedStyle(element).color),'rgb(31, 41, 55)');assert.equal(await page.locator('#yellowMenu').evaluate(element=>getComputedStyle(element).color),'rgb(161, 98, 7)');
  await page.evaluate(()=>document.getElementById('yellowMenu').classList.add('bg-indigo-700'));await page.waitForFunction(()=>{const style=getComputedStyle(document.getElementById('yellowMenu'));return style.color==='rgb(253, 224, 71)'&&style.backgroundColor==='rgb(4, 120, 87)';});assert.equal(await page.locator('#yellowMenu').evaluate(element=>getComputedStyle(element).backgroundColor),'rgb(4, 120, 87)');
  await page.evaluate(()=>{document.documentElement.dataset.mode='dark';document.documentElement.dataset.theme='rose';});
  await page.waitForFunction(()=>getComputedStyle(document.getElementById('sidebar')).backgroundColor==='rgb(8, 11, 16)'&&getComputedStyle(document.getElementById('yellowMenu')).color==='rgb(253, 224, 71)');
  assert.equal(await page.locator('#personalAdd').evaluate(element=>getComputedStyle(element).backgroundColor),'rgb(225, 29, 72)');assert.equal(await page.locator('#sidebar').evaluate(element=>getComputedStyle(element).backgroundColor),'rgb(8, 11, 16)');assert.equal(await page.locator('#yellowMenu').evaluate(element=>getComputedStyle(element).color),'rgb(253, 224, 71)');
  await page.evaluate(()=>{const button=document.createElement('button');button.id='dynamicAdd';button.textContent='Add new item';button.onclick=()=>clicks.push('dynamic');document.getElementById('personal').append(button);});
  await page.locator('#dynamicAdd[data-panel-add-icon]').waitFor();await page.locator('#dynamicAdd').click();assert.equal((await page.evaluate(()=>clicks)).at(-1),'dynamic');
  await page.evaluate(()=>document.querySelector('#dynamicAdd .panel-add-label').textContent='Add new category');await page.waitForFunction(()=>document.getElementById('dynamicAdd').title==='Add new category');
  await page.evaluate(()=>{window.changes=0;new MutationObserver(records=>changes+=records.length).observe(document.getElementById('personal'),{subtree:true,childList:true,attributes:true,characterData:true});for(let i=0;i<4;i++)normalizePanelAddActions(document.getElementById('mainContent'));});
  await page.waitForTimeout(60);assert.equal(await page.evaluate(()=>changes),0,'Repeated normalization must not mutate or loop');assert.equal(await page.locator('#dynamicAdd>svg').count(),1);assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Panel add icons and sidebar: static/dynamic/personal/nested controls, fa/en accessible names, preserved handlers, stable normalization, theme/dark/mobile and protected content/save controls passed.');
}finally{await browser.close();}})().catch(error=>{console.error(error);process.exitCode=1;});
