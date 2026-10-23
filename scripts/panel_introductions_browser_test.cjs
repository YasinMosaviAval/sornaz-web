const assert = require('node:assert/strict');
const fs = require('node:fs');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try {
  for(const lang of ['fa','en']) {
   const page=await browser.newPage(),errors=[];
   page.on('pageerror',error=>errors.push(error.message));
   await page.route('**/*',route=>route.fulfill({body:'<html></html>',contentType:'text/html'}));
   await page.goto('http://panel.test/');
   const css=fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php','utf8').match(/#mainContent > \.section h1[\s\S]*?\{ display:none; \}/)[0];
   await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}${css}</style><h1 id="panelPageTitle"></h1><main id="mainContent">
    <section id="example" class="section"><nav>Tabs</nav><div id="intro"><div><h1>Page title</h1><p id="description">Introduction</p></div><button id="add">Add</button></div><div id="filter">Holiday filter</div><form><label>Field<input></label><p id="help">Field guidance</p></form><div id="exampleModal" role="dialog"><h1>Dialog title</h1><p id="dialogHelp">Dialog guidance</p></div></section>
    <section id="account" class="section"><div id="accountIntro"><h1 id="accountPageTitle">Account title</h1><p data-panel-description id="accountDescription">Account intro</p></div><p id="accountMessage">Account data</p></section>
    <section id="orphan" class="section"><h1>Orphan title</h1><nav>Tabs</nav><p data-panel-description id="orphanDescription">Detached introduction</p><button id="orphanAction">Action</button></section>
    <section id="learning" class="section" data-admin-ui-native><header id="learningIntro"><h1>Learning title</h1><p>Learning intro</p></header><h2 id="contentHeading">Course content</h2></section>
   <section id="editor" class="section"><div contenteditable="true"><h1 id="articleTitle">Article title</h1><p id="articleText">Article text</p></div></section></main></html>`);
   assert.equal(await page.locator('#description').isVisible(),false,'No initial introduction flash');
   assert.equal(await page.locator('#accountDescription').isVisible(),false);
   await page.evaluate(()=>{
    const page=document.getElementById('example');
    const actions=document.createElement('div');actions.id='pageExports';
    actions.innerHTML='<button data-action="add">Add</button><button data-action="excel">خروجی اکسل</button><button data-action="pdf">خروجی PDF</button>';
    page.append(actions);window.actionClicks=[];
    page.addEventListener('click',event=>{if(event.target.dataset.action)window.actionClicks.push(event.target.dataset.action);});
    const slot=document.createElement('div');slot.id='panelPageActions';document.body.prepend(slot);
   });
   await page.addScriptTag({path:'assets/Analytics/js/admin.js'});
   for(const [id,title] of [['example','Page title'],['account','Account title'],['orphan','Orphan title'],['learning','Learning title']]) {
    await page.evaluate(id=>showSection(id),id);
    assert.equal(await page.locator('#panelPageTitle').textContent(),title);
    assert.equal(await page.locator('#'+id+' h1').first().isVisible(),false);
   }
   await page.evaluate(()=>showSection('example'));
   for(const action of ['add','excel','pdf']) await page.locator(`#example [data-action="${action}"]`).click();
   assert.deepEqual(await page.evaluate(()=>window.actionClicks),['add','excel','pdf']);
   assert.equal(await page.locator('#panelPageActions button').count(),0);
   for(const id of ['add','filter','help','dialogHelp']) assert.equal(await page.locator('#'+id).isVisible(),true,id+' must survive');
   assert.equal(await page.locator('#exampleModal h1').isVisible(),true);
   await page.evaluate(()=>showSection('editor'));
   assert.equal(await page.locator('#articleTitle').isVisible(),true);
   assert.equal(await page.locator('#articleText').isVisible(),true);
   await page.evaluate(()=>showSection('example'));
   await page.evaluate(()=>{const header=document.createElement('header');header.className='admin-ui-header';header.innerHTML='<div data-slot="heading"><h1>Late title</h1><p>Late intro</p></div><div data-slot="actions"><button id="lateAction">Late action</button></div>';document.getElementById('example').prepend(header);document.getElementById('example').dispatchEvent(new CustomEvent('admin-ui:ready',{bubbles:true}));});
   assert.equal(await page.locator('#lateAction').isVisible(),true);
   assert.equal(await page.locator('#example .admin-ui-header p').isVisible(),false);
   assert.deepEqual(errors,[]);
   await page.close();
  }
  console.log('Panel introductions: fa/en, initial render, detached and account descriptions, native/dynamic sections, header titles, actions, filters, field help and dialogs passed.');
 } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
