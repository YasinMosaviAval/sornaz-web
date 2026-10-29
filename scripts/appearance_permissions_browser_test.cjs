const assert=require('node:assert/strict'),fs=require('node:fs');
const {execFileSync}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const render=(name,user)=>execFileSync('php',['scripts/panel-table-view-fixture.php',name,'fa','',String(user),user===1?'admin':'user'],{encoding:'utf8'});
const css=fs.readFileSync('assets/theme/theme.css','utf8');
const layout=fs.readFileSync('Modules/Analytics/Resources/Views/layouts/admin.php','utf8').match(/<style>([\s\S]*?)<\/style>/)[1];
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const mode of ['light','dark'])for(const user of [1,30]){
  const page=await browser.newPage({viewport:{width:1280,height:900}}),errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.route('**/*',route=>route.fulfill({json:{data:{colorTheme:'original',themeMode:mode,fonts:[]}}}));
  await page.goto('http://appearance.test/');
  await page.setContent(`<html lang="fa" data-theme="original" data-mode="${mode}"><style>${css}\n${layout}</style>`+render('sidebar',user)+render('settings',user)+'</html>');
  await page.evaluate(()=>document.getElementById('settings').classList.remove('hidden'));
  assert.equal(await page.locator('#siteFontWeight').count(),user===1?1:0);
  assert.equal(await page.locator('#siteCornerRadius').count(),user===1?1:0);
  assert.equal(await page.locator('#siteColorTheme option[value=original]').count(),1);
  const keys=user===1?['educations','polls','tracking','national-holidays']:['educations','polls'];
  for(const key of keys){
   const link=page.locator(`#sidebar a[onclick="showSection('${key}')"]`);
   assert.equal(await link.count(),1);
   assert.equal(await link.evaluate(node=>node.classList.contains('text-yellow-300')),false);
   assert.equal(await link.evaluate(node=>getComputedStyle(node).color),mode==='dark'?'rgb(243, 244, 246)':'rgb(31, 41, 55)');
  }
  if(user===1){
   const lesson=page.locator('#sidebar button[onclick="showSection(\'lessons\')"]');
   await lesson.hover();
   await page.waitForFunction(()=>getComputedStyle(document.querySelector('#sidebar button.nav-link')).color==='rgb(255, 255, 255)');
   assert.equal(await lesson.evaluate(node=>getComputedStyle(node).color),'rgb(255, 255, 255)','Lesson button must share link hover color');
   assert.equal(await lesson.locator('span').first().evaluate(node=>getComputedStyle(node).color),'rgb(255, 255, 255)');
   await page.mouse.move(1200,0);
  }
  await page.addScriptTag({path:'assets/theme/theme.js'});
  await page.evaluate(()=>setSiteTheme('original'));
  assert.equal(await page.evaluate(()=>getComputedStyle(document.documentElement).getPropertyValue('--theme-600').trim()),mode==='dark'?'#c19e32':'#0064fb');
  assert.equal(await page.evaluate(()=>document.documentElement.dataset.theme),'original');
  assert.deepEqual(errors,[]);await page.close();
 }
 console.log('Appearance browser: original blue/gold palette, role-limited controls and standard sidebar colors in both modes passed.');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
