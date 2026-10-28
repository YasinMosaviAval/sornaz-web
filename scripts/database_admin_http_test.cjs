const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const net = require('node:net');
const {spawn} = require('node:child_process');

// Keep the synthetic router with its test; write it only into the private fixture.
const routerSource = String.raw`<?php
$fixture = getenv('SORNAZ_DATABASE_HTTP_FIXTURE');
$root = getenv('SORNAZ_DATABASE_HTTP_PROJECT');
if (PHP_SAPI !== 'cli-server' || !$fixture || !is_dir($fixture) || !str_starts_with(basename($fixture), 'sornaz-db-http-') || !$root) { http_response_code(500); exit; }
if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/fixture-health') { http_response_code(204); exit; }
$map = require $root.'/vendor/composer/autoload_classmap.php';
spl_autoload_register(static function ($class) use ($map, $root) {
    if (isset($map[$class])) require_once $map[$class];
    else { $file = $root.'/'.str_replace(chr(92), '/', $class).'.php'; if (is_file($file)) require_once $file; }
});
function base_path($path = '') { return $GLOBALS['root'].($path === '' ? '' : '/'.$path); }
function auth() { return new class {
    function id() { return (int) ($_SERVER['HTTP_X_FIXTURE_ACTOR'] ?? 0); }
    function check() { return $this->id() > 0; }
    function user() { return $this->check() ? ['user_id' => $this->id(), 'type' => 'superadmin'] : null; }
}; }
function db() {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO('sqlite:'.$GLOBALS['fixture'].'/fixture.sqlite', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
        $pdo->exec("CREATE TABLE IF NOT EXISTS fixture_records(id INTEGER PRIMARY KEY, content TEXT);");
        $pdo->exec("INSERT OR IGNORE INTO fixture_records VALUES(1, 'synthetic fixture only');");
        $pdo->exec('CREATE VIEW IF NOT EXISTS broken_fixture AS SELECT * FROM missing_fixture_table');
    }
    return $pdo;
}
require $root.'/Modules/Analytics/Routes/routes.php';
$route = Core\router\Router::dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (!$route || $route['action'][0] !== Modules\Analytics\Controllers\Web\AdminDatabaseController::class || $route['middlewares'] !== ['auth']) { http_response_code(404); exit; }
$response = (new Core\middleware\AuthMiddleware())->handle(new Core\http\Request(), static function () use ($route) {
    $controller = new Modules\Analytics\Controllers\Web\AdminDatabaseController(new Modules\Analytics\Services\AdminDatabaseService());
    return $controller->{$route['action'][1]}();
});
$response->send();
`;

async function run() {
  const fixture = fs.mkdtempSync(path.join(os.tmpdir(),'sornaz-db-http-'));
  const router = path.join(fixture,'router.php');
  fs.writeFileSync(router,routerSource,{mode:0o600});
  const listener = net.createServer();
  await new Promise(resolve=>listener.listen(0,'127.0.0.1',resolve));
  const port = listener.address().port;
  await new Promise(resolve=>listener.close(resolve));
  const child = spawn('php',['-S',`127.0.0.1:${port}`,router],{
    cwd:path.resolve(__dirname,'..'),windowsHide:true,stdio:['ignore','ignore','pipe'],
    env:{...process.env,SORNAZ_DATABASE_HTTP_FIXTURE:fixture,SORNAZ_DATABASE_HTTP_PROJECT:path.resolve(__dirname,'..')},
  });
  let failures=''; child.stderr.on('data',chunk=>{failures+=chunk.toString();});
  const url = `http://127.0.0.1:${port}`;
  const request = (suffix,actor=0)=>fetch(url+suffix,{headers:{Accept:'application/json','X-Fixture-Actor':String(actor)}});
  try {
    let ready=false;
    for(let attempt=0;attempt<70;attempt++){
      try{if((await request('/fixture-health')).status===204){ready=true;break;}}catch{}
      await new Promise(resolve=>setTimeout(resolve,50));
    }
    assert.equal(ready,true,'Local PHP fixture must start');
    for(const actor of [0,2,19]) for(const suffix of ['/analytics/admin-database/tables','/analytics/admin-database/rows?table=fixture_records','/analytics/admin-database/download?mode=data']){
      const response=await request(suffix,actor);assert.equal(response.status,actor?403:401);
      assert.equal((await response.json()).data.success,false);
    }
    assert.equal(fs.existsSync(path.join(fixture,'fixture.sqlite')),false,'Denied requests never open the database');
    const tables=await request('/analytics/admin-database/tables',1);
    assert.match(tables.headers.get('cache-control'),/private.*no-store/);
    assert.equal((await tables.json()).data.data.tables.length,2);
    const rows=await request('/analytics/admin-database/rows?table=fixture_records',1);
    assert.equal((await rows.json()).data.data.rows[0].content,'synthetic fixture only');
    for(const mode of ['schema','data']){
      const response=await request(`/analytics/admin-database/download?table=fixture_records&mode=${mode}`,1);
      assert.equal(response.status,200);assert.match(response.headers.get('content-type'),/^application\/sql/);
      assert.match(response.headers.get('content-disposition'),/^attachment; filename="sornaz-db-fixture_records-.*\.sql"$/);
      assert.match(response.headers.get('cache-control'),/private.*no-store/);
      assert.equal(response.headers.get('x-content-type-options'),'nosniff');
      assert.match(response.headers.get('content-security-policy'),/sandbox/);
      assert.equal(response.headers.get('x-sornaz-export-consistency'),'transactional-snapshot');
      const body=await response.text();assert.equal(Buffer.byteLength(body),Number(response.headers.get('content-length')));
      assert.ok(body.endsWith('-- Sornaz export complete.\n'));
      assert.equal(body.includes('INSERT INTO `fixture_records`'),mode==='data');
      assert.equal(body.includes(fixture),false);
    }
    const invalid=await request('/analytics/admin-database/rows?table=other_database.users',1);assert.equal(invalid.status,404);
    const broken=await request('/analytics/admin-database/rows?table=broken_fixture',1);assert.equal(broken.status,500);
    const message=JSON.stringify(await broken.json());assert.doesNotMatch(message,/PDOException|missing_fixture_table|SELECT|sqlite|Stack trace|[A-Z]:\\/);
    console.log('Database HTTP: actual routes/controller/middleware, founder-only access before database use, private SQL attachments, exact lengths, no-cache headers and sanitized errors passed.');
  } finally {
    if(child.exitCode===null){const exit=new Promise(resolve=>child.once('exit',resolve));child.kill();await exit;}
    const resolved=path.resolve(fixture),temporary=path.resolve(os.tmpdir())+path.sep;
    if(!resolved.startsWith(temporary)||!path.basename(resolved).startsWith('sornaz-db-http-'))throw Error('Unsafe fixture cleanup');
    fs.rmSync(resolved,{recursive:true,force:true,maxRetries:6,retryDelay:100});
  }
}
if(require.main===module)run().catch(error=>{console.error(error);process.exitCode=1;});
module.exports={run};
