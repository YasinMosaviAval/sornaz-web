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
  const context=await browser.newContext({viewport:{width,height:850}}),page=await context.newPage();const errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.route('https://fixture.test/**',async route=>{
   const url=new URL(route.request().url());
   if(url.pathname==='/exams')return route.fulfill({contentType:'text/html; charset=utf-8',body:'<!doctype html><html dir="'+t('rtl','ltr')+'"><head><meta charset="utf-8"></head><body>'+fixture.html[language]+'</body></html>'});
   if(url.pathname.endsWith('/music-practice.js'))return route.fulfill({path:path.join(project,'assets/Education/js/music-practice.js'),contentType:'application/javascript'});
   if(url.pathname.endsWith('/music-practice.css'))return route.fulfill({path:path.join(project,'assets/Education/css/music-practice.css'),contentType:'text/css'});
   if(url.pathname==='/exams/api/catalog')return route.fulfill({json:{success:true,data:fixture.catalog}});
   if(url.pathname==='/exams/api/progress')return route.fulfill({json:{success:true,data:{progress:[]}}});
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
  await page.getByRole('button',{name:t('بخش','Section'),exact:true}).click();
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
  await page.route('**/exams/api/catalog',route=>route.fulfill({status:503,json:{success:false,message:'Server unavailable'}}));
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
  if(url.pathname.endsWith('/music-practice.css'))return route.fulfill({path:path.join(project,'assets/Education/css/music-practice.css'),contentType:'text/css'});
  if(url.pathname==='/exams/api/catalog')return route.fulfill({json:{success:true,data:restricted}});
  if(url.pathname==='/exams/api/plans'){
   assert.equal(Number(url.searchParams.get('examId')),restricted.exams[0].id);
   const offer={teacher_id:2,status:'approved',exam_id:null,amount:12001,days:30,version:2};
   return route.fulfill({json:{success:true,data:{plans:[{...offer,id:100,node_id:restricted.nodes[0].id,title:'Matching section'},{...offer,id:101,node_id:999,title:'Wrong section'},{...offer,id:102,node_id:null,title:'Legacy teacher subscription'},{...offer,id:103,teacher_id:5,node_id:restricted.nodes[0].id,title:'Wrong teacher'}]}}});
  }
  return route.fulfill({json:{success:true,data:{progress:[]}}});
 });
 await buyer.goto('https://fixture.test/exams');await buyer.getByRole('button',{name:'Section',exact:true}).click();
 await buyer.getByRole('button',{name:'Buy test or subscription',exact:true}).click();
 await buyer.getByText(/Matching section/).waitFor();assert.equal(await buyer.getByText(/Wrong section|Wrong teacher/).count(),0);
 await buyer.getByText(/Legacy teacher subscription/).waitFor();await buyerContext.close();
 console.log('Web browser: mobile/desktop, shared breadcrumb, inline practice, management, visible server errors and section-specific offers passed.');}
 finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
