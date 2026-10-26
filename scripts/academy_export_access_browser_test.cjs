const assert=require('node:assert/strict');
const fs=require('node:fs');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 const page=await browser.newPage();
 for(const lang of ['fa','en']){
  await page.unrouteAll({behavior:'wait'});
  const errors=[],checks=[];page.on('pageerror',e=>errors.push(e.message));
  let mode='trial',unavailable=false,confirmPurchase=false;
  await page.route('**/*',async route=>{
   const url=new URL(route.request().url());
   if(url.pathname==='/academy/exports/access'){
    checks.push(url);
    if(unavailable)return route.fulfill({status:503,json:{success:false,message:'Unavailable'}});
    const ids=url.searchParams.getAll('academyIds[]');
    const allowed=['trial','paid','personal'].includes(mode)||(mode==='mixed'&&ids.length===1&&ids[0]==='200');
    return route.fulfill({json:{success:true,data:{allowed,reason:mode,academies:[],subscriptionUrl:'/analytics/admin-panel#finance'}}});
   }
   return route.fulfill({body:'<html></html>',contentType:'text/html'});
  });
  await page.goto('http://exports.test/');
  await page.setContent(`<html lang="${lang}"><style>.hidden{display:none}</style><main id="mainContent"><section id="course-levels" class="section"><button id="excel" onclick="exportCourseLevelsToExcel()">Excel</button><button id="pdf" onclick="exportCourseLevelsToPDF()">PDF</button><button id="free" onclick="window.freeCalls++">Add record</button><button id="png" onclick="window.pngCalls++">PNG</button></section><section id="schedules" class="section hidden"></section><section id="students" class="section hidden"></section><section id="finance" class="section hidden"><span id="academySubscriptionPrice"></span></section></main><div id="modalContainer"></div></html>`);
  await page.evaluate(()=>{
   window.freeCalls=0;window.pngCalls=0;window.printCalls=0;window.alerts=[];window.planCalls=0;
   window.allCourseLevels=[{id:1,name:'Piano',summary:'Fixture',academyId:100}];
   window.alert=message=>alerts.push(message);window.print=()=>printCalls++;
   window.showSection=id=>document.querySelectorAll('.section').forEach(section=>section.classList.toggle('hidden',section.id!==id));
   window.loadAcademySubscriptions=()=>planCalls++;
   window.confirmResult=false;window.AppDialog={confirm:async message=>{window.confirmMessage=message;return confirmResult;}};
  });
  for(const script of ['academy-export-access','course-levels','schedules','students'])await page.addScriptTag({path:'assets/Analytics/js/'+script+'.js'});
  const download=page.waitForEvent('download');await page.locator('#excel').click();assert.match((await download).suggestedFilename(),/\.csv$/);
  assert.equal(checks.at(-1).searchParams.getAll('academyIds[]')[0],'100','Authorize the actual export records');
  mode='expired';await page.evaluate(()=>AcademyExports.refresh());
  assert.equal(await page.locator('#excel').getAttribute('aria-disabled'),'true');
  let downloads=0;page.on('download',()=>downloads++);
  await page.evaluate(()=>exportCourseLevelsToExcel());
  await page.evaluate(()=>exportCourseLevelsToPDF());
  await page.evaluate(()=>generateStudentsPDF());
  await page.evaluate(()=>exportSchedulesToPDF());
  assert.equal(downloads,0);assert.equal(await page.evaluate(()=>printCalls),0,'Direct PDF calls must not bypass entitlement checks');
  assert.ok(await page.evaluate(()=>confirmMessage.length>0));
  await page.locator('#free').click();await page.locator('#png').click();
  assert.equal(await page.evaluate(()=>freeCalls),1);assert.equal(await page.evaluate(()=>pngCalls),1,'Non-export features stay free');
  mode='paid';const paidDownload=page.waitForEvent('download');await page.evaluate(()=>exportCourseLevelsToExcel());await paidDownload;
  await page.evaluate(()=>exportSchedulesToPDF());assert.equal(await page.evaluate(()=>printCalls),1);
  unavailable=true;const before=downloads;await page.evaluate(()=>exportCourseLevelsToExcel());assert.equal(downloads,before);assert.ok(await page.evaluate(()=>alerts.length>0));unavailable=false;
  mode='mixed';await page.evaluate(()=>{allCourseLevels=[{id:1,name:'A',academyId:100},{id:2,name:'B',academyId:200}];});
  const mixedBefore=downloads;await page.evaluate(()=>exportCourseLevelsToExcel());assert.equal(downloads,mixedBefore,'A paid academy must not unlock an expired academy in the same export');
  await page.evaluate(()=>{allCourseLevels=[{id:2,name:'B',academyId:200}];});
  const scopedDownload=page.waitForEvent('download');await page.evaluate(()=>exportCourseLevelsToExcel());await scopedDownload;
  mode='expired';await page.evaluate(()=>{confirmResult=true;});await page.evaluate(()=>exportCourseLevelsToExcel());
  assert.equal(await page.evaluate(()=>planCalls),1);assert.equal(await page.locator('#finance').isVisible(),true,'Offer existing subscription checkout without starting payment automatically');
  assert.deepEqual(errors,[]);
 }
 const files=['availabilities','availability-exceptions','branches','classrooms','course-levels','courses','finance','instruments','lessons','member-schedules','messages','reports','schedules','scheduling-rules','students','teachers','terms'];
 let guarded=0;
 for(const file of files){const source=fs.readFileSync('assets/Analytics/js/'+file+'.js','utf8');for(const match of source.matchAll(/window\.(export\w*|generate\w*PDF|open\w*PDF\w*)\s*=\s*async\s+(?:function\s*\([^)]*\)|\([^)]*\)\s*=>)\s*\{([^\n]*\n[^\n]*)/g)){if(!/(?:Excel|PDF)/i.test(match[1])&&match[1]!=='exportFinance')continue;assert.match(match[2],/AcademyExports\.authorize/,file+' '+match[1]);guarded++;}}
 assert.ok(guarded>=50,'Cover every built-in management export and PDF generation entry point');
 console.log('Export policy browser: trial/expired/paid/mixed scopes, actual CSV and PDF entry points, direct-call blocking, fail-closed checks, free create/PNG actions, renewal navigation and 50+ export guards passed (fa/en).');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
