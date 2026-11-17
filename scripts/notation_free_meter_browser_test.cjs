const assert=require('node:assert/strict'),path=require('node:path');
const {pathToFileURL}=require('node:url');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 for(const locale of ['fa','en'])for(const width of [390,1280]){
  const page=await browser.newPage({viewport:{width,height:850}}),errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.addInitScript(({locale,dark})=>{
   window.NOTATION_BOOT={locale,dark,userId:1,embedded:true};
   window.SornazNotation={postMessage(raw){const d=JSON.parse(raw);if(d.action==='route'){window.lastRoute=d;return;}queueMicrotask(()=>{
    let data=true;if(d.action==='list')data={items:window.savedSheet?[{...window.savedSheet,title:window.savedSheet.metadata.title}]:[],has_more:false};
    if(d.action==='instruments')data=[{id:42,fa:'تار',en:'Tar'}];
    if(d.action==='save'){window.savedPayload=d.payload;data=window.savedSheet={...d.payload,id:1,version:1,editable:true};}
    if(d.action==='get')data=window.savedSheet;
    window.Notation.receive(d.id,data,null);
   });}};
  },{locale,dark:width===1280});
  await page.goto(pathToFileURL(path.resolve(__dirname,'../assets/notation/index.html')).href);
  await page.locator('[data-action=new]').click();await page.locator('[name=title]').fill('Free phrase');
  await page.locator('[name=instrument]').selectOption('42');await page.locator('[name=scale_type]').selectOption('major');
  await page.locator('#metadata .notation-symbol-picker[data-control-key=time]').click();
  const popup=page.locator('.notation-form-options[data-control-key=time]'),free=popup.locator('[data-notation-option=free]');
  assert.equal(await free.locator('.notation-signature-name').textContent(),locale==='fa'?'میزان آزاد (بدون میزان)':'Free meter');
  assert.equal(await free.isVisible(),true,'the visible picker must expose free meter');
  assert.notEqual(await free.locator('svg').innerHTML(),await popup.locator('[data-notation-option="4/4"] svg').innerHTML(),'free meter must not look like 4/4');
  assert.ok(await free.evaluate(node=>{const box=node.getBoundingClientRect();return box.left>=0&&box.right<=innerWidth&&box.top>=0&&box.bottom<=innerHeight;}),'the free-meter choice fits the viewport');
  await page.screenshot({path:path.resolve(__dirname,`../storage/notation-browser-check/free-meter-picker-${locale}-${width}.png`)});
  await free.click();
  assert.equal(await page.locator('[name=time]').inputValue(),'free');
  assert.equal(await page.locator('#metadata .notation-symbol-picker[data-control-key=time]').textContent(),locale==='fa'?'میزان آزاد (بدون میزان)':'Free meter');
  assert.equal(await page.locator('.tempo-row').isVisible(),false);
  await page.locator('#metadata').evaluate(form=>form.requestSubmit());await page.locator('#score svg').waitFor();
  const enter=async(value,rest=false)=>{
   if(rest)await page.locator('[data-action=rest]').click();else await page.locator('[data-pitch=C4]').first().click({force:true});
   await page.locator('[data-duration-seconds]').fill(value);await page.locator('.free-duration-picker button[type=submit]').click();
  };
  await enter('0.01');await enter('5');await enter('0.29',true);
  assert.equal(await page.locator('#score .note-hit[data-note]').count(),3);
  assert.equal(await page.locator('#score .notation-measure-rest').count(),0);
  assert.equal(await page.locator('#score [data-signature-field=time]').count(),0);
  assert.ok((await page.locator('#score').textContent()).includes('0.01 s'));
  assert.ok((await page.locator('#score').textContent()).includes('5.00 s'));
  await enter('5.01');assert.equal(await page.locator('.free-duration-picker').count(),1);assert.equal(await page.locator('#score .note-hit[data-note]').count(),3);
  await page.locator('.free-duration-picker [data-cancel]').click();
  // The original note indices survive render-only line wrapping.
  await page.locator('#score .note-hit[data-note="1"]').click({force:true});
  await page.locator('.selection-note-tools [data-action=edit-seconds]').click();
  await page.locator('[data-duration-seconds]').fill(locale==='fa'?'۲٫۵۰':'2.50');await page.locator('.free-duration-picker button[type=submit]').click();
  await page.evaluate(()=>Notation.command('save'));await page.locator('dialog [data-action=yes]').click();await page.waitForFunction(()=>!!window.savedPayload);
  const saved=await page.evaluate(()=>window.savedPayload);assert.equal(saved.metadata.time,'free');
  assert.deepEqual(saved.score.measures[0].notes.map(n=>n.durationSeconds),[.01,2.5,.29]);
  await page.screenshot({path:path.resolve(__dirname,`../storage/notation-browser-check/free-meter-${locale}-${width}.png`)});
  assert.ok(Math.abs(await page.evaluate(()=>NotationModel.timeline(window.savedPayload).duration)-2.8)<1e-10);
  await page.evaluate(()=>Notation.command('undo'));await page.evaluate(()=>Notation.command('redo'));
  await page.evaluate(()=>{window.audioDurations=[];const proto=AudioContext.prototype,create=proto.createOscillator;proto.createOscillator=function(){const osc=create.call(this),start=osc.start.bind(osc),stop=osc.stop.bind(osc);let at;osc.start=time=>{at=time;start(time);};osc.stop=time=>{if(time!==undefined)window.audioDurations.push(time-at);stop(time);};return osc;};Notation.command('play');});
  await page.waitForFunction(()=>window.audioDurations.length>0);assert.ok(Math.abs((await page.evaluate(()=>window.audioDurations[0]))-.01)<1e-10,'audio keeps a 0.01-second note');
  await page.evaluate(()=>Notation.dispose());
  const printed=await page.evaluate(()=>NotationRenderer.printDocument(window.savedPayload,document.documentElement.lang));
  assert.ok(printed.includes('0.01 s')&&printed.includes('2.50 s'));
  const long=await page.evaluate(()=>{const s=NotationModel.clone(window.savedPayload);s.editable=true;for(let i=0;i<29;i++)NotationModel.insert(s,0,{pitch:'C4',duration:'q',dots:0,rest:false,durationSeconds:.01});NotationRenderer.draw(document.querySelector('#score'),s,{});return {hits:document.querySelectorAll('#score .note-hit[data-note]').length,rows:document.querySelectorAll('#score .staff-row').length,last:document.querySelector('#score .note-hit[data-note="31"]')?.dataset.bar};});
  assert.equal(long.hits,32);assert.ok(long.rows>1);assert.equal(long.last,'0');
  assert.deepEqual(errors,[]);await page.close();console.log(`Free meter browser ${locale} ${width}: creation, seconds, invalid input, editing, save, print and wrapping passed.`);
 }
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
