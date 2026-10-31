const assert=require('node:assert/strict'),fs=require('node:fs'),os=require('node:os'),path=require('node:path'),net=require('node:net');
const {spawn}=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const source=String.raw`<?php
$root=getenv('SORNAZ_GUIDE_PROJECT');$fixture=getenv('SORNAZ_GUIDE_FIXTURE');
if(PHP_SAPI!=='cli-server'||!$root||!$fixture||!str_starts_with(basename($fixture),'sornaz-guide-files-')){http_response_code(500);exit;}
$uri=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if($uri==='/health'){http_response_code(204);exit;}
$map=require $root.'/vendor/composer/autoload_classmap.php';spl_autoload_register(static function($class)use($root,$map){if(isset($map[$class]))require_once $map[$class];else{$p=$root.'/'.str_replace(chr(92),'/',$class).'.php';if(is_file($p))require_once $p;}});
function base_path($p=''){return $GLOBALS['fixture'].($p?'/'.$p:'');}
function storage_path($p=''){return base_path('storage'.($p?'/'.$p:''));}
function auth(){return new class{function id(){return (int)($_SERVER['HTTP_X_FIXTURE_ACTOR']??0);}function check(){return $this->id()>0;}};}
function e($value){return htmlspecialchars((string)$value,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function locale(){return ($_GET['lang']??'fa')==='en'?'en':'fa';}
function session(){return new class{function get($k,$default=null){return $k==='_csrf_token'?'fixture-token':$default;}};}
function app(){return new class{function container(){return new Core\container\Container;}};}
if($uri==='/assets/Analytics/js/guide-documents.js'){header('Content-Type: application/javascript');readfile($root.$uri);exit;}
if($uri==='/fixture-ui'){
 header('Content-Type: text/html; charset=utf-8');$guideDocuments=(new Modules\Analytics\Services\AdminGuideService)->documents(false);
 echo '<html lang="'.locale().'"><style>.hidden,[hidden]{display:none!important}</style><body>';
 require $root.'/Modules/Analytics/Resources/Views/sections/guides.php';
 echo '<script>window.adminCsrfToken="fixture-token";document.getElementById("guides").classList.remove("hidden");</script><script src="/assets/Analytics/js/guide-documents.js"></script></body></html>';exit;
}
require $root.'/Modules/Analytics/Routes/routes.php';
$route=Core\router\Router::dispatch($_SERVER['REQUEST_METHOD'],$uri);
if(!$route||$route['action'][0]!==Modules\Analytics\Controllers\Web\AdminGuideDocumentController::class){http_response_code(404);exit;}
$next=static fn()=> (new Core\container\Container)->make($route['action'][0])->{$route['action'][1]}();
foreach(array_reverse($route['middlewares'])as$alias){$previous=$next;$next=static fn()=> (new ($alias==='auth'?Core\middleware\AuthMiddleware::class:Core\csrf\CsrfMiddleware::class))->handle(new Core\http\Request,$previous);}
$next()->send();
`;
(async()=>{
 const fixture=fs.mkdtempSync(path.join(os.tmpdir(),'sornaz-guide-files-'));const project=path.resolve(__dirname,'..');
 for(const dir of ['docs/nested','Modules/Test/Lib'])fs.mkdirSync(path.join(fixture,dir),{recursive:true});
 fs.writeFileSync(path.join(fixture,'docs/lesson.md'),'# Lesson\r\n\r\nOriginal text.\r\n'.replaceAll('\\r','\r').replaceAll('\\n','\n'));
 fs.writeFileSync(path.join(fixture,'docs/nested/second.md'),'# Second\n\nSecond guide.');
 fs.writeFileSync(path.join(fixture,'Modules/Test/README.md'),'# Module\n\nModule guide.');
 fs.writeFileSync(path.join(fixture,'Modules/Test/Lib/vendor.md'),'# Protected vendor');
 fs.writeFileSync(path.join(fixture,'docs/not-markdown.php'),'<?php // protected fixture');
 fs.writeFileSync(path.join(fixture,'router.php'),source);
 const server=net.createServer();await new Promise(r=>server.listen(0,'127.0.0.1',r));const port=server.address().port;await new Promise(r=>server.close(r));
 const child=spawn('php',['-d','display_errors=0','-S',`127.0.0.1:${port}`,path.join(fixture,'router.php')],{cwd:project,windowsHide:true,stdio:['ignore','ignore','pipe'],env:{...process.env,SORNAZ_GUIDE_PROJECT:project,SORNAZ_GUIDE_FIXTURE:fixture}});
 let errors='';child.stderr.on('data',c=>errors+=c);
 const origin=`http://127.0.0.1:${port}`;
 const get=(relative,actor=1)=>fetch(origin+'/analytics/admin-guide-documents?'+new URLSearchParams({path:relative}),{headers:{Accept:'application/json','X-Fixture-Actor':String(actor)}});
 const post=(operation,data,actor=1,token='fixture-token')=>fetch(origin+'/analytics/admin-guide-documents/'+operation,{method:'POST',headers:{Accept:'application/json','X-Fixture-Actor':String(actor)},body:new URLSearchParams({_token:token,...data})});
 const data=async response=>(await response.json()).data.data;
 let browser;
 try{
  let ready=false;for(let i=0;i<70;i++){try{if((await fetch(origin+'/health')).status===204){ready=true;break;}}catch{}await new Promise(r=>setTimeout(r,50));}assert.ok(ready,errors);
  const initial=fs.readFileSync(path.join(fixture,'docs/lesson.md'),'utf8');
  for(const actor of [0,2,19]){
   assert.equal((await get('docs/lesson.md',actor)).status,actor?403:401);
   for(const operation of ['update','delete'])assert.equal((await post(operation,{path:'docs/lesson.md',version:'0'.repeat(64),content:'denied'},actor)).status,actor?403:401);
  }
  assert.equal(fs.existsSync(path.join(fixture,'storage')),false,'Denied actors must not create locks');assert.equal(fs.readFileSync(path.join(fixture,'docs/lesson.md'),'utf8'),initial);
  for(const name of ['../docs/lesson.md','docs/../docs/lesson.md','/docs/lesson.md','docs\\lesson.md','Modules/Test/Lib/vendor.md','docs/not-markdown.php','docs/no.md'])assert.equal((await get(name)).status,404,name);
  fs.mkdirSync(path.join(fixture,'outside-docs'));fs.writeFileSync(path.join(fixture,'outside-docs/secret.md'),'private fixture');
  let linked=false;try{fs.symlinkSync(path.join(fixture,'outside-docs'),path.join(fixture,'docs/alias'),process.platform==='win32'?'junction':'dir');linked=true;}catch{}
  if(linked)assert.equal((await get('docs/alias/secret.md')).status,404,'Directory links must not escape the library');
  const module=await data(await get('Modules/Test/README.md'));assert.equal((await post('update',{path:module.path,version:module.version,content:'# Updated module'})).status,200);assert.equal(fs.readFileSync(path.join(fixture,module.path),'utf8'),'# Updated module');
  const response=await get('docs/lesson.md');assert.match(response.headers.get('cache-control'),/private.*no-store/);const original=await data(response);assert.equal(original.content,initial);
  assert.equal((await post('update',{path:original.path,content:'changed',version:original.version},1,'wrong')).status,419);
  const updated=await data(await post('update',{path:original.path,content:'# Changed\n\n<script>window.injected=1</script>',version:original.version}));
  assert.equal(fs.readFileSync(path.join(fixture,'docs/lesson.md'),'utf8'),'# Changed\r\n\r\n<script>window.injected=1</script>');assert.match(updated.html,/&lt;script&gt;/);
  assert.equal((await post('update',{path:original.path,content:'stale',version:original.version})).status,409);
  assert.equal((await post('delete',{path:original.path,version:original.version})).status,409);
  assert.equal((await post('delete',{path:original.path,version:updated.version})).status,200);assert.equal(fs.existsSync(path.join(fixture,'docs/lesson.md')),false);assert.equal((await get(original.path)).status,404);
  for(const lang of ['fa','en']){
   fs.writeFileSync(path.join(fixture,'docs/lesson.md'),'# Lesson\n\n</textarea><img src=x onerror="window.injected=1">');
   browser??=await chromium.launch({channel:'msedge',headless:true});const page=await browser.newPage({extraHTTPHeaders:{'X-Fixture-Actor':'1'},viewport:{width:lang==='fa'?390:1280,height:850}}),pageErrors=[];page.on('pageerror',e=>pageErrors.push(e.message));
   await page.goto(origin+'/fixture-ui?lang='+lang);
   const card=page.locator('[data-guide-path="docs/lesson.md"]');assert.equal(await card.getAttribute('open'),null);
   assert.equal(await card.locator('.guide-md').textContent(),'','Closed guides must not preload their full HTML');
   await card.locator('[data-guide-file-title]').click();
   await page.waitForFunction(()=>document.querySelector('[data-guide-path="docs/lesson.md"] .guide-md').dataset.guideLoaded==='1');
   assert.match(await card.locator('.guide-md').textContent(),/<img src=x/);
   assert.equal(await card.locator('.guide-md img').count(),0,'Lazy-loaded Markdown must stay escaped');
   await card.locator('[data-guide-file-title]').click();
   await card.locator('[data-guide-file-action=edit]').click();await page.waitForFunction(()=>!document.querySelector('[data-guide-file-submit]').disabled);
   assert.equal(await card.getAttribute('open'),null,'Action click must not expand its card');assert.equal(await page.locator('#guideDocumentDialog').evaluate(e=>getComputedStyle(e,'::backdrop').backgroundColor),'rgba(0, 0, 0, 0)');
   assert.match(await page.locator('[data-guide-file-content]').inputValue(),/<\/textarea>/);assert.equal(await page.evaluate(()=>window.injected),undefined);
   await page.locator('[data-guide-file-content]').fill('# Edited guide\n\nSaved from browser.');await page.locator('[data-guide-file-submit]').click();await page.waitForSelector('#guideDocumentDialog',{state:'detached'});
   assert.equal(fs.readFileSync(path.join(fixture,'docs/lesson.md'),'utf8'),'# Edited guide\n\nSaved from browser.');assert.equal(await card.locator('[data-guide-file-title]').textContent(),'Edited guide');
   await card.locator('[data-guide-file-action=delete]').click();await page.waitForFunction(()=>!document.querySelector('[data-guide-file-submit]').disabled);await page.locator('[data-guide-file-close]').first().click();assert.ok(fs.existsSync(path.join(fixture,'docs/lesson.md')),'Cancel must retain the file');
   await card.locator('[data-guide-file-action=delete]').click();await page.waitForFunction(()=>!document.querySelector('[data-guide-file-submit]').disabled);await page.locator('[data-guide-file-submit]').click();await card.waitFor({state:'detached'});assert.equal(fs.existsSync(path.join(fixture,'docs/lesson.md')),false);
   assert.deepEqual(pageErrors,[]);await page.close();
  }
  assert.equal(fs.readFileSync(path.join(fixture,'Modules/Test/Lib/vendor.md'),'utf8'),'# Protected vendor');assert.ok(fs.existsSync(path.join(fixture,'docs/not-markdown.php')));
  console.log('Guide documents: real HTTP and filesystem editing/deletion, founder/CSRF/path guards, conflicts, line endings, escaped preview, browser actions and cancellation passed.');
 }finally{
  if(browser)await browser.close();if(child.exitCode===null){const done=new Promise(r=>child.once('exit',r));child.kill();await done;}
  if(!path.resolve(fixture).startsWith(path.resolve(os.tmpdir())+path.sep)||!path.basename(fixture).startsWith('sornaz-guide-files-'))throw Error('Unsafe cleanup');
  fs.rmSync(fixture,{recursive:true,force:true,maxRetries:6,retryDelay:100});
 }
})().catch(e=>{console.error(e);process.exitCode=1;});
