const assert=require('node:assert/strict');
const {execFileSync}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const {tableRuntime}=require('./panel-table-fixture.cjs');
const keys=['educations','certificates','awards','experiences','events','publications','badges'];
const render=(name,lang,user)=>execFileSync('php',['scripts/panel-table-view-fixture.php',name,lang,'',String(user)],{encoding:'utf8'});
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{for(const lang of ['fa','en'])for(const user of [1,10]){
  const page=await browser.newPage({viewport:{width:user===1?1280:390,height:900}}),errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.route('http://achievements.test/**',route=>{
   const url=new URL(route.request().url());
   if(!url.pathname.startsWith('/analytics/admin-profile-content/'))return route.fulfill({contentType:'text/html',body:'<html></html>'});
   const entity=url.pathname.split('/').pop();
   return route.fulfill({json:{success:true,data:{entity,canCreate:entity!=='badges'||user===1,hideOrganizationField:true,organizations:[{id:user,name:'Fixture',kind:'user',writable:true}],items:entity==='badges'?[{id:7,user_id:user,user_name:'Fixture',verification_level_id:1,approved_at:'2030-01-01',canManage:user===1,canChangeStatus:user===1}]:[],levels:[{id:1,title:'Received badge'}],version:'1'}}});
  });
  await page.goto('http://achievements.test/');
  await page.setContent(`<html lang="${lang}"><style>.hidden,[hidden]{display:none!important}</style>`+[...keys,'polls'].map(key=>render(key,lang,user)).join('')+tableRuntime()+'</html>');
  const tabs=await page.locator('#educations .panel-page-tabs button').evaluateAll(nodes=>nodes.map(node=>JSON.parse(node.getAttribute('onclick').slice('showSection('.length,-1))));
  assert.deepEqual(tabs,keys);assert.equal(await page.locator('#polls .panel-page-tabs').count(),0);
  await page.evaluate(()=>{document.getElementById('badges').classList.remove('hidden');});
  await page.addScriptTag({path:'assets/Analytics/js/profile-content.js'});
  await page.waitForFunction(()=>document.querySelector('#badges [data-profile-body]').textContent.includes('Received badge'));
  assert.equal(await page.locator('#badges [data-profile-add]').isVisible(),user===1);
  assert.equal(await page.locator('#badges [data-inline-toggle]:visible').count(),user===1?1:0);
  if(user!==1){
   await page.evaluate(()=>ProfileContent.inline('badges',7));assert.equal(await page.locator('#badges .profile-inline-editor').count(),0);
   await page.evaluate(()=>ProfileContent.editDialog('badges',7));assert.equal(await page.locator('#profileContentModal').count(),0);
  }
  assert.deepEqual(errors,[]);await page.close();
 }}finally{await browser.close();}
 console.log('Achievements browser: seven ordered tabs, independent polls, ordinary/admin badge actions and direct UI guard passed (fa/en, mobile/desktop).');
})().catch(e=>{console.error(e);process.exitCode=1;});
