// Run against an isolated Apache instance, never the application or its database.
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const net = require('node:net');
const {spawn} = require('node:child_process');
const {once} = require('node:events');

(async () => {
  const binary = process.argv[2] || 'C:/xampp/apache/bin/httpd.exe';
  const apacheRoot = path.resolve(path.dirname(binary), '..').replaceAll('\\', '/');
  const temporary = fs.mkdtempSync(path.join(os.tmpdir(), 'sornaz-apache-'));
  const root = temporary.replaceAll('\\', '/');
  const probe = net.createServer();
  await new Promise(resolve => probe.listen(0, '127.0.0.1', resolve));
  const port = probe.address().port;
  await new Promise(resolve => probe.close(resolve));
  const put = (relative, body) => {
    const target = path.join(temporary, relative);
    fs.mkdirSync(path.dirname(target), {recursive: true});
    fs.writeFileSync(target, body);
  };
  put('site/.htaccess', fs.readFileSync(path.join(__dirname, '../.htaccess')));
  put('site/index.php', 'FRONT_CONTROLLER');
  const forbidden = ['.env', '.env.example', '.git/config', 'php-error.log', 'composer.json', 'composer.lock', 'sornaz',
    'docs/database/backup.sql', 'core/secret.txt', 'Modules/demo.txt', 'vendor/test.txt',
    'scripts/test.php', 'config/data.json', 'resources/test.txt', 'storage/sessions/sess_test',
    'storage/backups/backup.sql', 'storage/chat/1/test.jpg', 'storage/chat/1/test.php',
    'storage/social-media/test.jpg', 'storage/course-media/test.mp4', 'assets/media/evil.php', 'extra.php'];
  for (const name of forbidden) put('site/'+name, 'PRIVATE_FIXTURE');
  put('site/assets/test.css', 'PUBLIC_ASSET');
  put('site/.well-known/acme-challenge/probe', 'ACME');
  const account = 'storage/account-media/1/2026/09/'+'a'.repeat(32)+'.png';
  put('site/'+account, 'PRIVATE_FIXTURE');
  const library = 'assets/media/library/2026/09/'+'a'.repeat(32)+'.png';
  put('site/'+library, 'PRIVATE_FIXTURE');
  put('httpd.conf', `ServerRoot "${apacheRoot}"
Listen 127.0.0.1:${port}
ServerName 127.0.0.1
PidFile "${root}/httpd.pid"
ErrorLog "${root}/error.log"
LoadModule authz_core_module modules/mod_authz_core.so
LoadModule authz_host_module modules/mod_authz_host.so
LoadModule dir_module modules/mod_dir.so
LoadModule mime_module modules/mod_mime.so
LoadModule rewrite_module modules/mod_rewrite.so
TypesConfig "${apacheRoot}/conf/mime.types"
DocumentRoot "${root}/site"
<Directory "${root}/site">
    AllowOverride All
    Require all granted
</Directory>
`);
  const child = spawn(binary, ['-X', '-f', root+'/httpd.conf'], {windowsHide:true, stdio:['ignore','pipe','pipe']});
  let logs = '';
  child.stderr.on('data', chunk => logs += chunk);
  const stopped = once(child, 'exit');
  const request = relative => fetch(`http://127.0.0.1:${port}/${relative}`, {redirect:'manual'});
  try {
    let ready = false;
    for (let i = 0; i < 50; i++) {
      try { await request('index.php'); ready = true; break; } catch {}
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    if (!ready) throw Error('Apache did not start: '+logs);
    let checks = 0;
    for (const url of [...forbidden, '%2eenv', 'STORAGE/chat/1/test.jpg']) {
      const response = await request(url);
      if (response.status !== 403) throw Error(url+' returned '+response.status);
      checks++;
    }
    for (const [url, expected] of [['index.php','FRONT_CONTROLLER'], ['community','FRONT_CONTROLLER'],
      [account,'FRONT_CONTROLLER'], [library,'FRONT_CONTROLLER'], ['assets/test.css','PUBLIC_ASSET'], ['.well-known/acme-challenge/probe','ACME']]) {
      const response = await request(url);
      if (response.status !== 200 || await response.text() !== expected) throw Error('Unexpected public response: '+url);
      checks++;
    }
    console.log(`Apache release rules: ${checks} HTTP checks passed.`);
  } finally {
    child.kill();
    await stopped;
    // Only the unique temporary fixture tree created above may be removed.
    const resolved = path.resolve(temporary);
    if (path.dirname(resolved) !== path.resolve(os.tmpdir()) || !path.basename(resolved).startsWith('sornaz-apache-')) throw Error('Unsafe cleanup target');
    fs.rmSync(resolved, {recursive:true, force:true});
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
