const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const net = require('node:net');
const { spawn, execFileSync } = require('node:child_process');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');

(async () => {
  const root = fs.mkdtempSync(path.join(os.tmpdir(), 'sornaz-post-media-'));
  const router = path.join(root, 'router.php');
  fs.copyFileSync('scripts/fixtures/post-media-http-router.txt', router);
  let server, browser;
  try {
    const port = await new Promise(resolve => { const listener = net.createServer(); listener.listen(0, '127.0.0.1', () => { const port = listener.address().port; listener.close(() => resolve(port)); }); });
    server = spawn('php', ['-d', 'upload_tmp_dir=' + root, '-S', `127.0.0.1:${port}`, router], { windowsHide: true, env: { ...process.env, SORNAZ_POST_MEDIA_FIXTURE_ROOT: root, SORNAZ_POST_MEDIA_PROJECT_ROOT: process.cwd() }, stdio: 'ignore' });
    const base = `http://127.0.0.1:${port}`;
    for (let attempt = 0; ; attempt++) {
      try { if ((await fetch(base + '/state')).ok) break; } catch (_) {}
      if (attempt > 50) throw Error('Local PHP upload fixture did not start');
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    browser = await chromium.launch({ channel: 'msedge', headless: true });
    const page = await browser.newPage();
    const images = await page.evaluate(() => {
      const canvas = document.createElement('canvas'); canvas.width = canvas.height = 2;
      canvas.getContext('2d').fillRect(0, 0, 2, 2);
      return Object.fromEntries([['png', 'image/png'], ['jpg', 'image/jpeg'], ['webp', 'image/webp']].map(([ext, mime]) => [ext, canvas.toDataURL(mime).split(',')[1]]));
    });
    const upload = async (name, mime, bytes, role = 'admin', csrf = 'fixture') => {
      const form = new FormData(); form.append('file', new Blob([bytes], { type: mime }), name);
      return fetch(base + '/upload', { method: 'POST', headers: { 'X-Fixture-Role': role, 'X-CSRF-TOKEN': csrf }, body: form });
    };
    const uploaded = [];
    for (const [extension, encoded] of Object.entries(images)) {
      const bytes = Buffer.from(encoded, 'base64');
      const response = await upload('image.' + extension.toUpperCase(), 'image/' + extension, bytes);
      const raw = await response.text();
      let result; try { result = JSON.parse(raw); } catch (_) { throw Error(raw.slice(0, 2000)); }
      assert.equal(response.status, 200, JSON.stringify(result));
      assert.equal(result.status, 200);
      assert.equal(result.data.success, true);
      uploaded.push(result.data.data);
      const saved = path.resolve(root, result.data.data.url.replace(/^\/+/, ''));
      assert.ok(saved.startsWith(path.resolve(root) + path.sep));
      assert.deepEqual(fs.readFileSync(saved), bytes, 'Actual multipart upload must persist identical bytes');
    }
    const png = Buffer.from(images.png, 'base64');
    for (const [name, mime, bytes] of [['image.gif', 'image/gif', Buffer.from('GIF89a')], ['image.php', 'image/png', png], ['spoof.jpg', 'image/jpeg', png], ['fake.png', 'image/png', Buffer.from('<script>bad</script>')]]) {
      assert.equal((await upload(name, mime, bytes)).status, 422, name);
    }
    assert.equal((await upload('image.png', 'image/png', png, 'member')).status, 403);
    assert.equal((await upload('image.png', 'image/png', png, 'admin', 'bad')).status, 403);
    assert.equal((await (await fetch(base + '/state')).json()).count, 3, 'Rejected uploads must not create media records');
    const seed = String.raw`
require 'core/database/TableNames.php'; require 'core/database/PrefixedPDO.php';
$db = new Core\database\PrefixedPDO('sqlite:'.$argv[1].'/fixture.sqlite');
$items=json_decode($argv[2],true);
$db->exec("INSERT INTO users VALUES(1,'fixture');");
$insert=$db->prepare('INSERT INTO posts(post_id,cover,cover_media_id,type,deleted_at) VALUES(?,?,?,?,?)');
$insert->execute([1,'',$items[0]['id'],'post',null]);
$insert->execute([2,'',4,'post',null]);
$insert->execute([3,'assets/deleted.png',7,'post','2026-01-01']);
$insert->execute([4,'assets/page.png',8,'page',null]);
$insertMedia=$db->prepare('INSERT INTO media_files(media_file_id,user_id,type,collection,fileable_type,path,filename,original_filename,created_at) VALUES(?,1,?,?,?,?,?,?,?)');
foreach([[4,'gallery','gallery','assets/gallery.png'],[5,'profile','users','assets/profile.png'],[6,null,null,'assets/responsive.png'],[7,null,null,'assets/deleted.png'],[8,null,null,'assets/page.png']] as [$id,$collection,$owner,$path]) $insertMedia->execute([$id,'image',$collection,$owner,$path,basename($path),basename($path),'2026-10-23']);
$html='<img src="'.$items[1]['url'].'"><a href="'.$items[2]['url'].'">link only</a><img src="https://other.test'.$items[2]['url'].'"><img src="/assets/gallery.png"><img src="/assets/profile.png"><picture><source srcset="/assets/responsive.png 2x"></picture>';
$db->prepare("INSERT INTO translations(table_name,table_id,locale,field,value) VALUES('posts',1,'fa','content',?)")->execute([$html]);
`;
    execFileSync('php', ['-r', seed, root, JSON.stringify(uploaded)]);
    const scopeRaw = await (await fetch(base + '/media', { headers: { 'X-Fixture-Role': 'admin', 'X-CSRF-TOKEN': 'fixture' } })).text();
    let scope; try { scope = JSON.parse(scopeRaw); } catch (_) { throw Error(scopeRaw.slice(0, 3000)); }
    assert.equal(scope.success, true, JSON.stringify(scope));
    assert.deepEqual(scope.data.items.map(item => item.id), [6, uploaded[1].id, uploaded[0].id]);
    assert.equal(scope.data.total, 3);
    assert.equal(scope.data.months.reduce((sum, item) => sum + Number(item.total), 0), 3);
    assert.equal((await fetch(base + '/media', { headers: { 'X-CSRF-TOKEN': 'fixture' } })).status, 403);
    console.log('Real local multipart upload: PNG/JPG/WebP bytes and SQLite records, spoofed formats, forbidden extensions, denied role and CSRF fixture checks passed.');
  } finally {
    if (browser) await browser.close();
    if (server) { server.kill(); await new Promise(resolve => { if (server.exitCode !== null) resolve(); else { server.once('exit', resolve); setTimeout(resolve, 3000); } }); }
    if (path.dirname(root) !== os.tmpdir() || !path.basename(root).startsWith('sornaz-post-media-')) throw Error('Unexpected fixture cleanup path');
    fs.rmSync(root, { recursive: true, force: true });
  }
})().catch(error => { console.error(error); process.exitCode = 1; });
