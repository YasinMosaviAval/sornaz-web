const assert=require('node:assert/strict'),fs=require('node:fs'),os=require('node:os'),path=require('node:path'),net=require('node:net');
const {spawn,execFileSync}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const source=String.raw`<?php
$root=getenv('SORNAZ_MUSIC_PROJECT');$fixture=getenv('SORNAZ_MUSIC_FIXTURE');
if(PHP_SAPI!=='cli-server'||!$root||!$fixture||!str_starts_with(basename($fixture),'sornaz-music-browser-')){http_response_code(404);exit;}
$map=require $root.'/vendor/composer/autoload_classmap.php';spl_autoload_register(static function($class)use($root,$map){$file=$map[$class]??$root.'/'.str_replace(chr(92),'/',$class).'.php';if(is_file($file))require_once $file;});
function base_path($p=''){return $GLOBALS['root'].'/'.$p;}
function storage_path($p=''){return $GLOBALS['fixture'].'/storage/'.$p;}
function config($key,$default=null){return $key==='app.key'?str_repeat('fixture-key-',4):$default;}
function env($key,$default=null){return match($key){'ZARINPAL_MERCHANT_ID'=>'fixture-merchant','APP_URL'=>'https://fixture.test','ZARINPAL_SANDBOX'=>true,default=>$default};}
function locale(){return ($_GET['lang']??'fa')==='en'?'en':'fa';}
function e($value){return htmlspecialchars((string)$value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function auth(){return new class{function id(){return (int)($_SERVER['HTTP_X_FIXTURE_ACTOR']??0);}function check(){return $this->id()>0;}};}
function session(){return new class{function get($key,$default=null){return $key==='_csrf_token'?'fixture-token':$default;}};}
function app(){return new class{function container(){return new Core\container\Container;}};}
$pdo=new Core\database\PrefixedPDO('sqlite:'.$fixture.'/database.sqlite',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);$pdo->sqliteCreateFunction('NOW',fn()=>gmdate('Y-m-d H:i:s'));
function db(){return $GLOBALS['pdo'];}
class FixtureMusicGateway extends Modules\System\Services\ZarinpalGateway{function request(string $action,array $payload):array{return match($action){'request'=>['data'=>['code'=>100,'authority'=>'fixture-'.substr(hash('sha256',$payload['callback_url']),0,24)]],'verify'=>['data'=>['code'=>100,'ref_id'=>'ref-'.$payload['authority']]],default=>['data'=>['code'=>100,'status'=>'IN_BANK']]};}}
$uri=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if($uri==='/health'){http_response_code(204);exit;}
if(in_array($uri,['/assets/Education/js/music-practice.js','/assets/vendor/tailwind/tailwindcss.js','/assets/theme/theme.css'],true)){header('Content-Type: '.(str_ends_with($uri,'.css')?'text/css':'application/javascript'));readfile($root.$uri);exit;}
if($uri==='/exams'){$practiceBoot=['api'=>'/exams/api','userId'=>auth()->id(),'csrf'=>'fixture-token','locale'=>locale()];echo '<!doctype html><html lang="'.locale().'" dir="'.(locale()==='en'?'ltr':'rtl').'" data-theme="emerald" data-mode="'.(($_GET['mode']??'light')==='dark'?'dark':'light').'"><head><style>[hidden]{display:none!important}</style><script src="/assets/vendor/tailwind/tailwindcss.js"></script><link rel="stylesheet" href="/assets/theme/theme.css"></head><body>';require $root.'/Modules/Education/Resources/Views/music-practice.php';echo '</body></html>';exit;}
if($uri==='/fixture-orders'&&auth()->id()===1){header('Content-Type: application/json');echo json_encode($pdo->query('SELECT * FROM f_music_practice_orders')->fetchAll());exit;}
require $root.'/Modules/Education/Routes/web.php';require $root.'/Modules/Education/Routes/api.php';
$route=Core\router\Router::dispatch($_SERVER['REQUEST_METHOD'],$uri);
if(!$route||$route['action'][0]!==Modules\Education\Controllers\MusicPracticeController::class){http_response_code(404);exit;}
$next=static fn()=>(new Modules\Education\Controllers\MusicPracticeController(new FixtureMusicGateway))->{$route['action'][1]}();
foreach(array_reverse($route['middlewares'])as$alias){$previous=$next;$next=static fn()=>(new ($alias==='auth'?Core\middleware\AuthMiddleware::class:Core\csrf\CsrfMiddleware::class))->handle(new Core\http\Request,$previous);}
$next()->send();
`;
(async()=>{
 const project=path.resolve(__dirname,'..'),fixture=fs.mkdtempSync(path.join(os.tmpdir(),'sornaz-music-browser-'));
 let child,browser;try{
  execFileSync('php',['scripts/music-practice-integration-test.php','--export-browser='+fixture],{cwd:project,stdio:'pipe',timeout:30000});
  fs.writeFileSync(path.join(fixture,'router.php'),source);
  const tokens=JSON.parse(fs.readFileSync(path.join(fixture,'tokens.json'),'utf8'));
  const socket=net.createServer();await new Promise(r=>socket.listen(0,'127.0.0.1',r));const port=socket.address().port;await new Promise(r=>socket.close(r));
  child=spawn('php',['-d','display_errors=0','-S',`127.0.0.1:${port}`,path.join(fixture,'router.php')],{cwd:project,windowsHide:true,stdio:['ignore','ignore','pipe'],env:{...process.env,SORNAZ_MUSIC_PROJECT:project,SORNAZ_MUSIC_FIXTURE:fixture}});
  let errors='';child.stderr.on('data',chunk=>errors+=chunk);const origin=`http://127.0.0.1:${port}`;
  for(let i=0;i<80;i++){try{if((await fetch(origin+'/health',{signal:AbortSignal.timeout(2000)})).status===204)break;}catch{}await new Promise(r=>setTimeout(r,50));if(i===79)throw Error(errors);}
  console.log('Music practice: local HTTP fixture ready');
  const request=async(actor,url,payload,csrf='fixture-token',bearer=false)=>{const response=await fetch(origin+url,{signal:AbortSignal.timeout(20000),method:payload?'POST':'GET',headers:{Accept:'application/json','X-Fixture-Actor':String(actor),...(bearer?{Authorization:'Bearer '+tokens[actor]}:{})},...(payload?{body:new URLSearchParams({_token:csrf,payload:JSON.stringify(payload)})}:{})});const json=await response.json();return {status:response.status,data:json.data?.data??json.data??json};};
  assert.equal((await request(1,'/api/sornaz/v1/music-practice/progress')).status,401,'Cookie alone cannot authenticate mobile');
  assert.equal((await request(3,'/api/sornaz/v1/music-practice/progress',null,'',true)).status,200);
  assert.equal((await request(3,'/exams/api/nodes',{titleFa:'forbidden'})).status,403);
  assert.equal((await request(1,'/exams/api/nodes',{titleFa:'csrf'},'bad')).status,419);
  assert.equal((await request(3,'/exams/api/people?q=st')).status,403);
  assert.deepEqual((await request(2,'/exams/api/people?q=st')).data.users,[{id:3,username:'student'}]);
  const catalog=(await request(1,'/exams/api/catalog?manage=1')).data,rhythm=catalog.nodes.find(n=>n.slug==='rhythm');
  const paid=await request(2,'/exams/api/exams',{nodeId:rhythm.id,titleFa:'آزمون خرید','titleEn':'Checkout test',position:900,published:true,audience:'subscribers',stageCount:5,requiredWins:2,config:{kind:'interval',quarterSteps:[14,24],direction:'ascending',variableRoot:false}});assert.equal(paid.status,200);
  const proposal=await request(2,'/exams/api/plans/propose',{examId:paid.data.id,title:'Individual test offer',amount:12001,days:0});assert.equal(proposal.status,200);
  assert.equal((await request(1,'/exams/api/plans/approve',{id:proposal.data.id,version:1,status:'approved'})).status,200);
  const checkoutKey='abcdef0123456789abcdef0123456789';
  const checkout=await request(3,'/api/sornaz/v1/music-practice/purchase',{planId:proposal.data.id,planVersion:2,checkoutKey},'',true);assert.equal(checkout.status,200);assert.ok(checkout.data.redirectUrl.startsWith('https://sandbox.zarinpal.com/'));
  const orders=await(await fetch(origin+'/fixture-orders',{headers:{'X-Fixture-Actor':'1'}})).json(),order=orders.find(o=>o.checkout_key===checkoutKey);
  const callback=origin+'/exams/payment/callback?'+new URLSearchParams({token:order.token,Authority:order.authority,Status:'OK'});
  const complete=await fetch(callback,{redirect:'manual'});assert.ok([302,303].includes(complete.status));assert.equal(complete.headers.get('location'),'/exams?payment=complete');await fetch(callback,{redirect:'manual'});
  assert.equal((await request(3,'/exams/api/catalog')).data.exams.find(e=>e.id===paid.data.id).access,true,'HTTP verified purchase did not grant access');
  const revenue=(await request(2,'/exams/api/plans')).data.revenue.filter(r=>r.order_id==order.id);assert.equal(revenue.length,1);assert.equal(Number(revenue[0].amount),9600,'HTTP share was not default 80 percent rounded down');
  console.log('Music practice: HTTP auth, payment and ledger passed');
  browser=await chromium.launch({channel:'msedge',headless:true});
  const buyerContext=await browser.newContext({extraHTTPHeaders:{'X-Fixture-Actor':'4'}}),buyer=await buyerContext.newPage();
  await buyer.goto(origin+'/exams?lang=en');await buyer.getByRole('button',{name:'Rhythm practice',exact:true}).click();
  const paidCard=buyer.locator('article').filter({has:buyer.getByRole('heading',{name:'Checkout test',exact:true})});
  await paidCard.getByRole('button',{name:'Buy test or subscription',exact:true}).click();
  await buyer.locator('[data-practice-editor]').getByText(/Individual test offer/).waitFor();
  await buyerContext.close();
  for(const lang of ['fa','en'])for(const width of [390,1280]){
   const suffix=lang+'-'+width,definition={nodeId:rhythm.id,titleFa:'آزمون رابط '+suffix,titleEn:'UI test '+suffix,position:0,published:true,audience:'public',stageCount:2,requiredWins:1,config:{kind:'choice',questions:[{promptFa:'<img src=x onerror=window.injected=1> پرسش',promptEn:'<img src=x onerror=window.injected=1> Question',choices:['Correct','Wrong'],correct:0}]}};
   const created=await request(1,'/exams/api/exams',definition);assert.equal(created.status,200);
   const context=await browser.newContext({viewport:{width,height:900},extraHTTPHeaders:{'X-Fixture-Actor':'3'}}),page=await context.newPage(),pageErrors=[];page.on('pageerror',e=>pageErrors.push(e.message));
   await page.goto(origin+'/exams?lang='+lang+'&mode='+(width===390?'dark':'light'));
   try{await page.getByRole('button',{name:lang==='en'?'Solfege practice':'تمرین سولفژ',exact:true}).waitFor();}catch(e){throw Error('Catalog UI failed '+suffix+': '+await page.locator('[data-practice-status]').textContent()+'; '+pageErrors.join('; ')+'; '+errors.slice(-1800));}
   await page.getByRole('button',{name:lang==='en'?'Solfege practice':'تمرین سولفژ',exact:true}).click();
   await page.getByRole('button',{name:lang==='en'?'Interval recognition':'تشخیص فاصله',exact:true}).click();
   await page.getByRole('button',{name:lang==='en'?'Iranian music':'موسیقی ایرانی',exact:true}).click();
   await page.getByRole('heading',{name:lang==='en'?'Level 180':'سطح 180',exact:true}).waitFor();
   await page.locator('[data-practice-breadcrumb]').getByRole('button',{name:lang==='en'?'Interval recognition':'تشخیص فاصله',exact:true}).click();
   await page.getByRole('button',{name:lang==='en'?'Western music':'موسیقی جهانی',exact:true}).click();
   assert.equal(await page.locator('[data-practice-list] article').count(),44);
   await page.locator('[data-practice-breadcrumb] button').first().click();
   await page.getByRole('button',{name:lang==='en'?'Rhythm practice':'تمرین ریتم',exact:true}).click();
   const card=page.locator('[data-practice-list] article').filter({has:page.getByRole('heading',{name:lang==='en'?definition.titleEn:definition.titleFa,exact:true})});await card.waitFor();
   await context.setOffline(true);
   for(let stage=1;stage<=2;stage++){
    await card.getByRole('button',{name:lang==='en'?'Start practice':'شروع تمرین',exact:true}).click();
    assert.equal(await page.locator('[data-practice-player] img').count(),0);assert.equal(await page.evaluate(()=>window.injected),undefined);
    await page.getByRole('button',{name:'Correct',exact:true}).click();await page.getByText(lang==='en'?'Stage completed.':'مرحله کامل شد.',{exact:true}).waitFor();
    await page.getByRole('button',{name:lang==='en'?'Back':'بازگشت',exact:true}).click();await card.waitFor();
   }
   assert.ok((await card.textContent()).includes('★★★★★'));
   await context.setOffline(false);await page.locator('[data-practice-upload]').click();
   await page.waitForFunction(()=>document.querySelector('[data-practice-status]').textContent===''||/uploaded|بارگذاری شدند/.test(document.querySelector('[data-practice-status]').textContent));
   for(let retry=0;retry<60;retry++){const data=(await request(3,'/exams/api/progress')).data;const p=data.progress.find(p=>p.examId===created.data.id);if(p?.stars===5)break;if(retry===59)assert.fail('Offline stages did not upload');await new Promise(r=>setTimeout(r,50));}
   assert.deepEqual(pageErrors,[]);assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true,'No mobile horizontal page overflow');
   await context.setExtraHTTPHeaders({'X-Fixture-Actor':'4'});await page.locator('[data-practice-refresh]').click();await page.waitForFunction(()=>document.querySelector('[data-practice-list]').children.length===0);
   assert.equal(await page.evaluate(()=>new Promise((resolve,reject)=>{const request=indexedDB.open('sornaz-music-practice-v1',1);request.onsuccess=()=>{const r=request.result.transaction('records').objectStore('records').get('catalog:3');r.onsuccess=()=>resolve(r.result);r.onerror=()=>reject(r.error);};})),null,'Changed account retained another user’s cached catalog');
   await context.close();console.log('Music practice browser passed: '+suffix);
  }
  const teacherContext=await browser.newContext({extraHTTPHeaders:{'X-Fixture-Actor':'1'}}),author=await teacherContext.newPage(),uiErrors=[];author.on('pageerror',e=>uiErrors.push(e.message));
  await author.goto(origin+'/exams?lang=en');await author.locator('[data-practice-manage]').click();await author.getByRole('button',{name:'Rhythm practice',exact:true}).click();
  await author.getByRole('button',{name:'Add test',exact:true}).click();await author.locator('[data-practice-editor] [name=titleFa]').fill('آزمون فرم');await author.locator('[data-practice-editor] [name=titleEn]').fill('Form test');await author.locator('[name=kind]').selectOption('choice');await author.getByRole('button',{name:'Add question',exact:true}).click();await author.locator('[name=promptFa]').fill('پرسش');await author.locator('[name=promptEn]').fill('Question');await author.locator('[name=choices]').fill('Right\nWrong');await author.locator('[name=stageCount]').fill('3');await author.locator('[name=published]').selectOption('1');await author.locator('[data-practice-editor] button[type=submit]').click();
  await author.getByRole('heading',{name:'Form test',exact:true}).waitFor();
  const formCard=author.locator('article').filter({has:author.getByRole('heading',{name:'Form test',exact:true})});await formCard.getByRole('button',{name:'Revenue share',exact:true}).click();await author.locator('[name=teacherPercent]').fill('72.5');await author.locator('[data-practice-editor] button[type=submit]').click();await author.locator('[data-practice-editor]').waitFor({state:'hidden'});
  const updated=(await request(1,'/exams/api/catalog?manage=1')).data.exams.find(e=>e.titleEn==='Form test');assert.equal(updated.teacherBps,7250);assert.equal(updated.version,1,'Changing split must preserve exam progress/version');
  await formCard.getByRole('button',{name:'Edit test',exact:true}).click();
  await author.locator('[name=audience]').selectOption('users');
  await author.getByRole('searchbox',{name:'Search student username'}).fill('st');
  await author.getByRole('button',{name:'@student',exact:true}).click();
  assert.equal(await author.locator('[name=userIds]').inputValue(),'3');
  await author.locator('[data-practice-editor] button[type=submit]').click();
  await author.locator('[data-practice-editor]').waitFor({state:'hidden'});
  const selected=(await request(1,'/exams/api/catalog?manage=1')).data.exams.find(e=>e.id===updated.id);
  assert.deepEqual(selected.users,[{id:3,username:'student'}]);
  assert.equal((await request(4,'/exams/api/catalog')).data.exams.some(e=>e.id===updated.id),false);
  assert.equal((await request(3,'/exams/api/catalog')).data.exams.find(e=>e.id===updated.id).access,true);
  assert.deepEqual(uiErrors,[]);await teacherContext.close();
  console.log('Music practice browser/HTTP: real PHP routes, signed bearer, CSRF, three-level navigation, fa/en dark/mobile, escaped questions, offline stages/upload, live server progress, author forms and per-exam revenue editing passed.');
 }finally{if(browser)await browser.close();if(child&&child.exitCode===null){const done=new Promise(r=>child.once('exit',r));child.kill();await done;}if(!path.resolve(fixture).startsWith(path.resolve(os.tmpdir())+path.sep)||!path.basename(fixture).startsWith('sornaz-music-browser-'))throw Error('Unsafe cleanup');fs.rmSync(fixture,{recursive:true,force:true,maxRetries:6,retryDelay:100});}
})().catch(e=>{console.error(e);process.exitCode=1;});
