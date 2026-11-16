const assert=require('node:assert/strict');
const {execFileSync}=require('node:child_process');
const path=require('node:path');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const php=String.raw`<?php
$root=getenv('SORNAZ_TEST_ROOT');
spl_autoload_register(function($class)use($root){$file=$root.'/'.str_replace('\\','/',$class).'.php';if(is_file($file))require_once $file;});
function base_path($p=''){return $GLOBALS['root'].'/'.$p;}
function locale(){return $GLOBALS['language']??'fa';}
function config($k,$d=null){return $d;}
function env($k,$d=null){return $k==='MUSIC_PRACTICE_SIGNING_KEY'?($GLOBALS['signing']??''):$d;}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function db(){return $GLOBALS['pdo'];}
use Modules\Education\Services\MusicPracticeStore;
use Modules\Education\Services\MusicPracticeCatalog;
use Modules\Education\Services\MusicPracticeResults;
$pdo=new Core\database\PrefixedPDO('sqlite::memory:',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$pdo->sqliteCreateFunction('NOW',fn()=>gmdate('Y-m-d H:i:s'));
$pdo->exec('CREATE TABLE f_translations(translation_id INTEGER PRIMARY KEY AUTOINCREMENT,table_name TEXT,table_id INTEGER,locale TEXT,field TEXT,value TEXT,version INTEGER,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,approved_at TEXT,approved_by INTEGER,deleted_at TEXT)');
$pdo->exec('CREATE TABLE f_users(user_id INTEGER PRIMARY KEY,username TEXT,deleted_at TEXT)');
$pdo->exec("INSERT INTO f_users VALUES(1,'admin',NULL)");
$sql=preg_replace('/^--.*$/m','',file_get_contents(base_path('docs/database/migrations/2026_10_29_music_practice_catalog.sql')));
foreach(explode(';',$sql)as$s){$s=trim($s);if(!str_starts_with($s,'CREATE TABLE'))continue;
 $s=str_replace(['BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY','CHARACTER SET ascii COLLATE ascii_bin'],['INTEGER PRIMARY KEY AUTOINCREMENT',''],$s);
 $s=preg_replace('/,\s*UNIQUE KEY \w+\(([^)]+)\)/',', UNIQUE($1)',$s);
 $s=preg_replace('/,\s*KEY \w+\([^)]+\)/','',$s);$s=preg_replace('/\) ENGINE=InnoDB.*$/s',')',$s);$pdo->exec($s);
}
function verify($ok){if(!$ok)throw new RuntimeException('Assertion failed');}
function reject($code,$work){try{$work();}catch(RuntimeException $e){verify($e->getCode()===$code);return;}throw new RuntimeException('Expected rejection');}
$store=new MusicPracticeStore($pdo,true);$catalog=new MusicPracticeCatalog($store);
$node=$catalog->saveNode(1,['titleFa'=>'بخش','titleEn'=>'Section','slug'=>'section','published'=>true])['id'];
$input=['nodeId'=>$node,'titleFa'=>'آزمون','titleEn'=>'Test','published'=>true,'audience'=>'public','stageCount'=>1,'requiredWins'=>1,'config'=>['kind'=>'choice','questions'=>[['promptFa'=>'پرسش','promptEn'=>'Question','choices'=>['Yes','No'],'correct'=>0]]]];
$exam=$catalog->saveExam(1,$input)['id'];$listing=$catalog->listing(1,true);
verify(count($listing['exams'])===1&&!isset($listing['exams'][0]['downloadPermit']));
$payload=['runId'=>str_repeat('a',32),'examId'=>$exam,'version'=>1,'stage'=>1,'closed'=>true,'answers'=>[['question'=>0,'selected'=>0,'sequence'=>1,'elapsedMs'=>10,'answeredAt'=>gmdate('Y-m-d\TH:i:s\Z')]]];
$results=new MusicPracticeResults($store);verify($results->store(3,$payload)['status']==='passed');
verify($results->store(3,$payload)['acceptedThrough']===1);
reject(401,fn()=>$results->store(0,$payload));
$pdo->exec('UPDATE f_music_practice_exams SET published=0');reject(403,fn()=>$results->store(3,$payload));
$pdo->exec('UPDATE f_music_practice_exams SET published=1,version=2');reject(409,fn()=>$results->store(3,$payload));
$pdo->exec('UPDATE f_music_practice_exams SET version=1');
$mobile=new MusicPracticeStore($pdo);$unsigned=(new MusicPracticeCatalog($mobile))->listing(1);verify(count($unsigned['exams'])===1&&!isset($unsigned['exams'][0]['downloadPermit'])&&!empty($unsigned['offlineMessage']));
$signing=str_repeat('test-signing-key-',3);$mobileExam=(new MusicPracticeCatalog($mobile))->listing(1)['exams'][0];verify(isset($mobileExam['downloadPermit']));
$signed=$payload+array_intersect_key($mobileExam,array_flip(['downloadPermit','permitExpiresAt']));$signed['runId']=str_repeat('b',32);
verify((new MusicPracticeResults($mobile))->store(1,$signed)['status']==='passed');
$signed['downloadPermit']=str_repeat('0',64);reject(403,fn()=>(new MusicPracticeResults($mobile))->store(1,$signed));
$html=[];
foreach(['fa','en'] as $language){
 $practiceBoot=['api'=>'/exams/api','userId'=>1,'csrf'=>'test','locale'=>$language];
 ob_start();require base_path('Modules/Education/Resources/Views/music-practice.php');$html[$language]=ob_get_clean();
}
$breadcrumbItems=[['label'=>'<img src=x onerror=alert(1)>']];
ob_start();require base_path('resources/views/partials/breadcrumb.php');$escaped=ob_get_clean();verify(!str_contains($escaped,'<img'));
echo json_encode(['html'=>$html,'catalog'=>$listing],JSON_THROW_ON_ERROR);
`;
(async()=>{
 const project=path.resolve(__dirname,'..');
 const fixture=JSON.parse(execFileSync('php',[],{input:php,cwd:project,env:{...process.env,SORNAZ_TEST_ROOT:project},encoding:'utf8'}));
 console.log('Online PHP/SQLite: missing key, catalog management, result persistence, retry, access revocation, stale version and mobile permit isolation passed.');
 const browser=await chromium.launch({channel:'msedge',headless:true});
 try{for(const language of ['fa','en'])for(const width of [390,1280]){
  const t=(fa,en)=>language==='en'?en:fa;
  const context=await browser.newContext({viewport:{width,height:850}}),page=await context.newPage();const errors=[],writes=[],currentCatalog=structuredClone(fixture.catalog);page.on('pageerror',e=>errors.push(e.message));
  for(const depth of [2,3])currentCatalog.nodes.push({id:9000+depth,parentId:depth===2?fixture.catalog.nodes[0].id:9002,depth,titleFa:'زیرسطح '+depth,titleEn:'Nested '+depth,slug:'fixture-level-'+depth,position:0,published:true,canManage:true,version:1});
  await page.route('https://fixture.test/**',async route=>{
   const url=new URL(route.request().url());
   if(url.pathname==='/exams')return route.fulfill({contentType:'text/html; charset=utf-8',body:'<!doctype html><html lang="'+language+'" dir="'+t('rtl','ltr')+'"><head><meta charset="utf-8"><link rel="stylesheet" href="/assets/theme/theme.css"></head><body>'+fixture.html[language]+'</body></html>'});
   if(url.pathname==='/assets/theme/dialog.js')return route.fulfill({path:path.join(project,'assets/theme/dialog.js'),contentType:'application/javascript'});
   if(url.pathname==='/assets/theme/theme.css')return route.fulfill({path:path.join(project,'assets/theme/theme.css'),contentType:'text/css'});
   if(url.pathname.endsWith('/music-practice.js'))return route.fulfill({path:path.join(project,'assets/Education/js/music-practice.js'),contentType:'application/javascript'});
   if(url.pathname.endsWith('/practice-notation.js'))return route.fulfill({path:path.join(project,'assets/Education/js/practice-notation.js'),contentType:'application/javascript'});
   if(url.pathname.endsWith('/music-practice.css'))return route.fulfill({path:path.join(project,'assets/Education/css/music-practice.css'),contentType:'text/css'});
   if(url.pathname==='/exams/api/catalog')return route.fulfill({json:{success:true,data:currentCatalog}});
   if(url.pathname==='/exams/api/progress')return route.fulfill({json:{success:true,data:{progress:[]}}});
   if(['/exams/api/nodes','/exams/api/exams','/exams/api/publish','/exams/api/nodes/delete','/exams/api/exams/delete'].includes(url.pathname)){
    assert.equal(route.request().headers()['x-csrf-token'],'test');
    writes.push({path:url.pathname,...JSON.parse(new URLSearchParams(route.request().postData()).get('payload'))});
    if(url.pathname.endsWith('/nodes/delete')){
     const id=writes.at(-1).id;
     if(currentCatalog.nodes.some(n=>n.parentId===id)||currentCatalog.exams.some(e=>e.nodeId===id))return route.fulfill({status:409,json:{success:false,message:'Section has children'}});
     currentCatalog.nodes=currentCatalog.nodes.filter(n=>n.id!==id);
    }
    if(url.pathname.endsWith('/exams/delete'))currentCatalog.exams=currentCatalog.exams.filter(e=>e.id!==writes.at(-1).id);
    if(url.pathname.endsWith('/publish')){const payload=writes.at(-1),row=(payload.kind==='exam'?currentCatalog.exams:currentCatalog.nodes).find(n=>n.id===payload.id);row.published=payload.published;if(payload.kind!=='exam')row.version++;}
    if(url.pathname.endsWith('/exams')){const payload=writes.at(-1);currentCatalog.exams.push({...payload,id:9100,version:1,ownerId:1,titleFa:payload.titleFa||payload.titleEn,titleEn:payload.titleEn||payload.titleFa,published:false,access:true});}
    return route.fulfill({json:{success:true,data:{id:123}}});
   }
   if(url.pathname==='/exams/api/results'){
    const payload=JSON.parse(new URLSearchParams(route.request().postData()).get('payload'));
    return route.fulfill({json:{success:true,data:{acceptedThrough:payload.answers.at(-1)?.sequence??2}}});
   }
   return route.fulfill({json:{success:true,data:{acceptedThrough:1}}});
  });
  await page.goto('https://fixture.test/exams');await page.getByRole('button',{name:t('بخش','Section'),exact:true}).waitFor();
  assert.equal(await page.locator('[data-practice-refresh],[data-practice-upload]').count(),0);
  assert.ok(await page.locator('[data-site-breadcrumb]').evaluate(nav=>!!(nav.compareDocumentPosition(document.querySelector('h1'))&Node.DOCUMENT_POSITION_FOLLOWING)));
  assert.equal(await page.locator('[data-practice-manage]').textContent(),t('مدیریت آزمون‌ها','Manage tests'));
  await page.locator('[data-practice-manage]').click();await page.getByRole('button',{name:t('افزودن بخش','Add section'),exact:true}).waitFor();
  const rootCard=page.locator('article').filter({has:page.getByRole('button',{name:t('بخش','Section'),exact:true})});
  const rootPencil=rootCard.getByRole('button',{name:t('ویرایش بخش','Edit section'),exact:true});
  async function checkLevelGap(card){
   const caption=await card.locator(':scope>button').nth(0).boundingBox(),icon=await card.locator(':scope>button').nth(1).boundingBox();
   assert.ok(Math.abs((language==='en'?icon.x-caption.x-caption.width:caption.x-icon.x-icon.width)-8)<1,'Level text/icon gap must be 8px');
  }
  await checkLevelGap(rootCard);
  assert.equal(await rootPencil.innerText(),'');assert.equal(await rootPencil.locator('.fa-pen').count(),1);
  await rootPencil.click();await page.locator('[data-practice-editor]').getByRole('button',{name:t('بستن','Close'),exact:true}).click();
  const countBeforeDelete=writes.length,rootTrash=rootCard.getByRole('button',{name:t('حذف سطح','Delete level'),exact:true});
  assert.equal(await rootTrash.evaluate(el=>getComputedStyle(el).color),'rgb(220, 38, 38)');
  await rootTrash.click();await page.getByRole('dialog').getByRole('button',{name:t('انصراف','Cancel'),exact:true}).click();
  await page.getByRole('dialog').waitFor({state:'detached'});assert.equal(writes.length,countBeforeDelete);
  await rootTrash.click();await page.getByRole('dialog').getByRole('button',{name:t('حذف','Delete'),exact:true}).click();
  await page.getByRole('status').filter({hasText:'Section has children'}).waitFor();assert.equal(await rootCard.count(),1);
  await page.getByRole('button',{name:t('افزودن بخش','Add section'),exact:true}).click();
  const form=page.locator('[data-practice-editor]');
  await form.getByRole('heading',{name:t('افزودن سطح اول آزمون','Add first test level'),exact:true}).waitFor();
  const titleKey=t('titleFa','titleEn');
  assert.equal(await form.locator('[name=parentId],[name=published],[name=slug],[name='+t('titleEn','titleFa')+']').count(),0);
  await form.locator('[name='+titleKey+']').fill(t('سطح تازه','New first level'));
  await form.getByRole('button',{name:t('ذخیره','Save'),exact:true}).click();
  await form.waitFor({state:'hidden'});
  assert.deepEqual(writes.at(-1),{path:'/exams/api/nodes',firstLevel:true,parentId:0,[titleKey]:t('سطح تازه','New first level'),position:0});
  const eye=page.getByRole('button',{name:t('عدم نمایش عمومی','Hide from public'),exact:true});
  assert.equal(await eye.getAttribute('aria-pressed'),'true');
  await Promise.all([page.waitForResponse(r=>r.url().includes('/catalog')),eye.click()]);
  assert.deepEqual(writes.at(-1),{path:'/exams/api/publish',kind:'node',id:fixture.catalog.nodes[0].id,version:fixture.catalog.nodes[0].version,published:false});
  const hiddenEye=page.getByRole('button',{name:t('نمایش عمومی','Show publicly'),exact:true});
  assert.equal(await hiddenEye.getAttribute('aria-pressed'),'false');
  await Promise.all([page.waitForResponse(r=>r.url().includes('/catalog')),hiddenEye.click()]);
  assert.equal(writes.at(-1).published,true);assert.equal(writes.at(-1).version,fixture.catalog.nodes[0].version+1);
  await page.getByRole('button',{name:t('بخش','Section'),exact:true}).click();
  let currentParent=fixture.catalog.nodes[0].id;
  for(const depth of [2,3]){
   await page.locator('[data-practice-add]').click();
   await form.getByRole('heading',{name:depth===2?t('افزودن سطح دوم آزمون','Add second test level'):t('افزودن سطح سوم آزمون','Add third test level'),exact:true}).waitFor();
   assert.equal(await form.locator('[name=parentId],[name=published],[name=slug],[name='+t('titleEn','titleFa')+']').count(),0);
   assert.equal(await form.locator('[name=parentLabel]').count(),0);
   await form.locator('[name='+titleKey+']').fill(t('زیرسطح تازه','New nested level'));
   await form.getByRole('button',{name:t('ذخیره','Save'),exact:true}).click();
   await form.waitFor({state:'hidden'});
   assert.deepEqual(writes.at(-1),{path:'/exams/api/nodes',parentId:currentParent,level:depth,[titleKey]:t('زیرسطح تازه','New nested level'),position:0});
   const nextLabel=t('زیرسطح '+depth,'Nested '+depth),card=page.locator('article').filter({has:page.getByRole('button',{name:nextLabel,exact:true})});
   await checkLevelGap(card);
   await Promise.all([page.waitForResponse(r=>r.url().includes('/catalog')),card.getByRole('button',{name:t('عدم نمایش عمومی','Hide from public'),exact:true}).click()]);
   assert.deepEqual(writes.at(-1),{path:'/exams/api/publish',kind:'node',id:9000+depth,version:1,published:false});
   await Promise.all([page.waitForResponse(r=>r.url().includes('/catalog')),card.getByRole('button',{name:t('نمایش عمومی','Show publicly'),exact:true}).click()]);
   await card.getByRole('button',{name:nextLabel,exact:true}).click();
   currentParent=9000+depth;
  }
  assert.equal(await page.locator('[data-practice-add]').isHidden(),true,'Level four must not be offered');
  await page.locator('[data-site-breadcrumb]').getByRole('link',{name:t('بخش','Section'),exact:true}).click();
  await page.getByRole('button',{name:t('افزودن آزمون','Add test'),exact:true}).click();
  assert.equal(await form.locator('[name=nodeId],[name=published],[name='+t('titleEn','titleFa')+']').count(),0);
  await form.locator('[name='+titleKey+']').fill(t('آزمون تازه','New test'));
  const rectangles=[];
  for(const name of ['position','stageCount','requiredWins']){
   const input=form.locator('[name='+name+']');assert.equal(await input.getAttribute('type'),'number');assert.equal(await input.getAttribute('step'),'1');
   await input.fill('');await input.pressSequentially('x-2e.3');assert.equal(await input.inputValue(),'23');
   await input.evaluate(el=>{const data=new DataTransfer();data.setData('text','۱x٢.۳-');el.dispatchEvent(new ClipboardEvent('paste',{clipboardData:data,bubbles:true,cancelable:true}));});assert.equal(await input.inputValue(),'123');
   await input.fill('2');rectangles.push(await input.boundingBox());
   assert.equal(await input.locator('..').locator('small:visible').count(),0);
  }
  for(const rect of rectangles.slice(1)){assert.ok(Math.abs(rect.width-rectangles[0].width)<1);assert.ok(Math.abs(rect.height-rectangles[0].height)<1);assert.ok(Math.abs(rect.y-rectangles[0].y)<1);}
  assert.equal(await form.locator('input[type=search]').evaluate(el=>getComputedStyle(el).fontSize),await form.locator('[name='+titleKey+']').evaluate(el=>getComputedStyle(el).fontSize));
  const searchRect=await form.locator('input[type=search]').boundingBox(),modeRect=await form.locator('[name=levelMode]').locator('..').boundingBox();
  const spacing=await form.locator('form').evaluate(el=>parseFloat(getComputedStyle(el).rowGap));
  assert.ok(Math.abs(modeRect.y-(searchRect.y+searchRect.height)-spacing)<1);
  const typeRect=await form.locator('[name=kind]').boundingBox(),distanceRect=await form.locator('[name=quarterSteps]').boundingBox(),directionRect=await form.locator('[name=direction]').boundingBox(),variableRect=await form.locator('[name=variableRoot]').boundingBox();
  assert.ok(Math.abs(typeRect.y-distanceRect.y)<1&&Math.abs(typeRect.width-distanceRect.width)<1);
  assert.ok(Math.abs(directionRect.y-variableRect.y)<1&&Math.abs(directionRect.width-variableRect.width)<1);
  await form.locator('[name=kind]').selectOption('rhythm');await form.getByRole('button',{name:t('افزودن پرسش','Add question'),exact:true}).click();
  const rhythm=form.locator('[data-question]');
  for(const names of [['promptFa','promptEn'],['choices','correct'],['beats','bpm']]){
   const first=await rhythm.locator('[name='+names[0]+']').locator('..').boundingBox(),second=await rhythm.locator('[name='+names[1]+']').locator('..').boundingBox();
   assert.ok(Math.abs(first.y-second.y)<1&&Math.abs(first.width-second.width)<1);
  }
  await form.locator('[name=kind]').selectOption('choice');
  const promptKey=language==='en'?'promptEn':'promptFa',hiddenPrompt=language==='en'?'promptFa':'promptEn';
  assert.equal(await rhythm.locator('[name='+hiddenPrompt+']').isVisible(),false);
  assert.equal(await rhythm.locator('[name='+hiddenPrompt+']').isDisabled(),true);
  await rhythm.locator('[name='+promptKey+']').fill('Single locale question');
  await rhythm.locator('[name=choices]').fill('A\nB');await rhythm.locator('[name=correct]').fill('0');
  const configRect=await form.locator('.practice-exam-config').boundingBox(),writtenTypeRect=await form.locator('[name=kind]').boundingBox();
  assert.ok(Math.abs(configRect.width-writtenTypeRect.width)<1);
  const saveButton=form.locator('button[type=submit]');
  assert.equal(await saveButton.evaluate(el=>getComputedStyle(el).fontSize),'14px');
  await page.route('**/exams/api/exams',route=>{
   const payload=JSON.parse(new URLSearchParams(route.request().postData()).get('payload'));
   assert.equal(payload.config.questions[0][promptKey],'Single locale question');assert.ok(!(hiddenPrompt in payload.config.questions[0]));
   return route.fulfill({status:422,json:{success:false,message:'Fixture validation error'}});
  });
  await saveButton.click();const inlineError=form.locator('[data-practice-form-error]');await inlineError.getByText('Fixture validation error',{exact:true}).waitFor();
  assert.equal(await inlineError.evaluate(el=>getComputedStyle(el).color),'rgb(220, 38, 38)');
  assert.equal(await inlineError.evaluate(el=>getComputedStyle(el).fontSize),'12px');
  assert.ok((await inlineError.boundingBox()).y+(await inlineError.boundingBox()).height<=(await saveButton.boundingBox()).y);
  assert.equal(await page.locator('[data-practice-status]').textContent(),'');
  await page.unroute('**/exams/api/exams');
  await rhythm.getByRole('button',{name:t('حذف پرسش','Remove question'),exact:true}).click();await form.locator('[name=kind]').selectOption('interval');
  await form.getByRole('button',{name:t('ذخیره','Save'),exact:true}).click();await form.waitFor({state:'hidden'});
  const saved=writes.at(-1);assert.equal(saved.nodeId,fixture.catalog.nodes[0].id);assert.equal(saved[titleKey],t('آزمون تازه','New test'));assert.ok(!('published' in saved)&&!(t('titleEn','titleFa') in saved));
  for(const name of ['position','stageCount','requiredWins'])assert.equal(saved[name],2);
  const newCard=page.locator('article').filter({has:page.getByRole('heading',{name:t('آزمون تازه','New test'),exact:true})});
  await Promise.all([page.waitForResponse(r=>r.url().includes('/catalog')),newCard.getByRole('button',{name:t('نمایش عمومی','Show publicly'),exact:true}).click()]);
  assert.deepEqual(writes.at(-1),{path:'/exams/api/publish',kind:'exam',id:9100,version:1,published:true});
  await Promise.all([page.waitForResponse(r=>r.url().includes('/catalog')),newCard.getByRole('button',{name:t('عدم نمایش عمومی','Hide from public'),exact:true}).click()]);
  const pencil=newCard.getByRole('button',{name:t('ویرایش آزمون','Edit test'),exact:true});assert.equal(await pencil.locator('.fa-pen').count(),1);
  await pencil.click();await form.getByRole('button',{name:t('بستن','Close'),exact:true}).click();
  const trash=newCard.getByRole('button',{name:t('حذف سطح','Delete level'),exact:true}),beforeExamDelete=writes.length;
  await trash.click();assert.equal(await trash.isDisabled(),true);
  await page.getByRole('dialog').getByRole('button',{name:t('انصراف','Cancel'),exact:true}).click();await page.getByRole('dialog').waitFor({state:'detached'});assert.equal(writes.length,beforeExamDelete);
  await trash.click();await page.getByRole('dialog').getByRole('button',{name:t('حذف','Delete'),exact:true}).click();await newCard.waitFor({state:'detached'});
  assert.deepEqual(writes.at(-1),{path:'/exams/api/exams/delete',id:9100,version:1});
  await page.getByRole('button',{name:t('شروع تمرین','Start practice'),exact:true}).click();
  assert.equal(await page.locator('article.practice-exam-active [data-practice-player]').count(),1);
  assert.equal(await page.locator('[data-practice-list]').isVisible(),true);
  const check=page.getByRole('button',{name:t('بررسی پاسخ','Check answer'),exact:true});
  assert.equal(await check.isDisabled(),true);
  await page.getByRole('button',{name:'No',exact:true}).click();await check.click();
  await page.getByText(t('دوباره تلاش کنید.','Try again.'),{exact:true}).waitFor();
  assert.equal(await check.isDisabled(),true);
  await page.getByRole('button',{name:'Yes',exact:true}).click();await check.click();
  await page.getByText(t('مرحله کامل شد.','Stage completed.'),{exact:true}).waitFor();
  await page.getByRole('button',{name:t('بازگشت','Back'),exact:true}).click();
  await page.getByRole('button',{name:t('شروع تمرین','Start practice'),exact:true}).waitFor();
  assert.equal(await page.locator('article [data-practice-player]').count(),0);
  await page.locator('[data-site-breadcrumb]').getByRole('link',{name:t('آزمون‌ها','Tests'),exact:true}).click();
  await page.getByRole('button',{name:t('بخش','Section'),exact:true}).waitFor();assert.deepEqual(errors,[]);
  await page.route('**/exams/api/catalog?**',route=>route.fulfill({status:503,json:{success:false,message:'Server unavailable'}}));
  await page.getByRole('button',{name:t('مدیریت آزمون‌ها','Manage tests'),exact:true}).click();await page.getByRole('status').filter({hasText:'Server unavailable'}).waitFor();
  assert.equal(await page.getByText('نمایش نسخهٔ ذخیره‌شده؛ اتصال برقرار نیست.').count(),0);
  await context.close();
 }
 const buyerContext=await browser.newContext(),buyer=await buyerContext.newPage();
 const restricted={...fixture.catalog,viewerId:3,capabilities:{},exams:fixture.catalog.exams.map(e=>({...e,ownerId:2,access:false,audience:'subscribers'}))};
 await buyer.route('https://fixture.test/**',async route=>{
  const url=new URL(route.request().url());
  if(url.pathname==='/exams')return route.fulfill({contentType:'text/html; charset=utf-8',body:'<!doctype html><meta charset="utf-8">'+fixture.html.en.replace('"userId":1','"userId":3')});
  if(url.pathname.endsWith('/music-practice.js'))return route.fulfill({path:path.join(project,'assets/Education/js/music-practice.js'),contentType:'application/javascript'});
  if(url.pathname==='/assets/theme/dialog.js')return route.fulfill({path:path.join(project,'assets/theme/dialog.js'),contentType:'application/javascript'});
  if(url.pathname.endsWith('/music-practice.css'))return route.fulfill({path:path.join(project,'assets/Education/css/music-practice.css'),contentType:'text/css'});
  if(url.pathname==='/exams/api/catalog')return route.fulfill({json:{success:true,data:restricted}});
  if(url.pathname==='/exams/api/plans'){
   assert.equal(Number(url.searchParams.get('examId')),restricted.exams[0].id);
   const offer={teacher_id:2,status:'approved',exam_id:null,amount:12001,days:30,version:2};
   return route.fulfill({json:{success:true,data:{plans:[{...offer,id:100,node_id:restricted.nodes[0].id,title:'Matching section'},{...offer,id:101,node_id:999,title:'Wrong section'},{...offer,id:102,node_id:null,title:'Legacy teacher subscription'},{...offer,id:103,teacher_id:5,node_id:restricted.nodes[0].id,title:'Wrong teacher'}]}}});
  }
  return route.fulfill({json:{success:true,data:{progress:[]}}});
 });
 await buyer.goto('https://fixture.test/exams');await buyer.getByRole('button',{name:'Section',exact:true}).waitFor();
 assert.equal(await buyer.locator('[aria-label="Edit section"],[aria-label="Edit test"],[aria-label="Delete level"]').count(),0);
 await buyer.getByRole('button',{name:'Section',exact:true}).click();
 await buyer.getByRole('button',{name:'Buy test or subscription',exact:true}).click();
 await buyer.getByText(/Matching section/).waitFor();assert.equal(await buyer.getByText(/Wrong section|Wrong teacher/).count(),0);
 await buyer.getByText(/Legacy teacher subscription/).waitFor();await buyer.getByRole('button',{name:'Close',exact:true}).click();
 restricted.capabilities={edit:true,delete:true};restricted.nodeDeletion=true;restricted.levelDeletion=true;
 restricted.nodes=restricted.nodes.map(n=>({...n,canManage:false}));restricted.exams=[];
 await buyer.goto('https://fixture.test/exams');await buyer.locator('[data-practice-manage]').click();await buyer.getByRole('button',{name:'Section',exact:true}).waitFor();
 assert.equal(await buyer.locator('[aria-label="Edit section"],[aria-label="Edit test"],[aria-label="Delete level"]').count(),0);
 await buyerContext.close();
 console.log('Web browser: mobile/desktop, shared breadcrumb, inline practice, management, visible server errors and section-specific offers passed.');}
 finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
