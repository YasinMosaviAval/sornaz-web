const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const {execFileSync} = require('node:child_process');
const {chromium} = require('../storage/notation-browser-check/package');
const root = path.resolve(__dirname, '..');
(async () => {
 const browser = await chromium.launch({channel:'chrome',headless:true});
 try {
  for (const locale of ['fa','en']) for (const pwa of [false,true]) {
   const page = await browser.newPage({viewport:{width:pwa?900:390,height:850}});
   const errors=[];page.on('pageerror',error=>errors.push(error.message));
   if(pwa) await page.addInitScript(()=>{
    const original=window.matchMedia.bind(window);
    window.matchMedia=query=>query==='(display-mode: standalone)'?{matches:true,addEventListener(){}}:original(query);
   });
   const html=execFileSync('php',['-r',`function base_path($p){return getcwd().'/'.$p;}$boot=['locale'=>'${locale}','userId'=>1,'webStorage'=>true,'websitePanel'=>true];include 'Modules/Notation/Resources/Views/index.php';`],{cwd:root,encoding:'utf8'});
   const sheet={id:91,owner_id:1,version:1,editable:true,visibility:'private',metadata:{title:'Mobile fixture',instrument:'Piano',key:'C',time:'4/4',bpm:100,tempo_note:'q',clef:'treble'},score:{measures:[{notes:[{pitch:'C4',duration:'q',dots:0,staff:1,voice:1,at:0}]}]}};
   await page.route('http://notation.test/**',route=>{
    const url=new URL(route.request().url());
    if(url.pathname==='/music-sheets')return route.fulfill({contentType:'text/html',body:html});
    if(url.pathname.startsWith('/music-sheets/api')){
     let data={items:[{...sheet,title:sheet.metadata.title}],has_more:false};
     if(url.pathname.endsWith('/instruments'))data=[{id:'Piano',fa:'Piano',en:'Piano'}];
     if(url.pathname.endsWith('/scales'))data=[];
     if(url.pathname.endsWith('/91'))data=sheet;
     return route.fulfill({json:{success:true,data}});
    }
    const file=path.resolve(root,'.'+url.pathname);
    return file.startsWith(root+path.sep)&&fs.existsSync(file)?route.fulfill({path:file}):route.fulfill({status:404,body:''});
   });
   await page.goto('http://notation.test/music-sheets');
   await page.locator('[data-action=open][data-id="91"]').first().click();
   await page.locator('#score svg').first().waitFor();
   await page.locator('[data-action=select-part][data-part="0"]').click();
   await page.locator('[data-action=select-section][data-section="0"]').click();
   await page.locator('[data-action=select-subsection][data-subsection="0"]').click();
   assert.equal(await page.locator('body').evaluate(el=>el.classList.contains('notation-mobile')),true);
   assert.equal(await page.locator('body.website-panel').count(),0);
   assert.equal(await page.locator('.notation-score-workspace').count(),0);
   assert.equal(await page.locator('.piano').count(),1);
   const footer=await page.locator('.editor-footer').boundingBox();
   assert.ok(Math.abs(footer.y+footer.height-850)<2,'Keyboard must meet the viewport bottom');
   assert.equal(await page.locator('.compact-editor-header').evaluate(el=>el.getBoundingClientRect().height),48);
   assert.equal(await page.locator('.compact-editor-header .editor-actions svg').first().evaluate(el=>el.getBoundingClientRect().width),22);
   await page.locator('.rest-key').click();
   await page.locator('.duration-picker').waitFor();
   await page.keyboard.press('Escape');
   assert.equal(await page.locator('.duration-picker').count(),0);
   const before=await page.locator('.note-hit').count();
   await page.locator('.rest-key').click();
   await page.locator('.duration-picker button.active').click();
   await page.evaluate(()=>new Promise(resolve=>requestAnimationFrame(()=>requestAnimationFrame(resolve))));
   const snapshot=()=>page.evaluate(async()=>{
    let content;const original=URL.createObjectURL;
    URL.createObjectURL=blob=>{content=blob.text();return original.call(URL,blob);};
    try{window.Notation.command('export');return JSON.parse(await content);}finally{URL.createObjectURL=original;}
   });
   const written=await snapshot();
   assert.ok(written.score.measures.some(bar=>bar.notes.some(note=>note.rest)));
   if(!pwa){
    await page.setViewportSize({width:1280,height:850});
    await page.locator('body.website-panel').waitFor();
    await page.setViewportSize({width:390,height:850});
    await page.locator('body.notation-mobile').waitFor();
    assert.deepEqual(await snapshot(),written,'Resize must preserve unsaved score');
   }
   assert.deepEqual(errors,[]);
   console.log(`${locale} ${pwa?'PWA':'mobile'} shared editor and duration dialog passed`);
   await page.close();
  }
 } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
