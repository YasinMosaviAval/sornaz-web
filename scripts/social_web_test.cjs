const assert=require('node:assert/strict'),fs=require('node:fs'),cp=require('node:child_process');
const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');
const svg='<svg xmlns="http://www.w3.org/2000/svg" width="600" height="720"><rect width="600" height="720" fill="#eadbcf"/><circle cx="300" cy="310" r="130" fill="#915946"/><path d="M230 110v420M180 220h260" stroke="#fff" stroke-width="12"/><text x="50" y="650" font-size="42" fill="#fff">Sornaz music</text></svg>';
const user={id:2,name:'نوازنده سرناز',username:'musician',avatar:'/fixture-avatar.jpg',posts:2,followers:12,following:4,bio:'Music',isFollowing:false};
const post={id:9,owner_id:2,kind:'post',body:'تمرین امروز <img src=x onerror=alert(1)>',media:'/fixture-post.jpg',mime:'image/jpeg',author:user,likes:7,liked:false,saved:false,created_at:'2026-09-14 09:00:00'};
const stories=[{...post,id:12,kind:'story',body:'داستان دوم'},{...post,id:11,kind:'story',body:'داستان اول'}];
async function setup(browser,lang,width,guest=false,fail=false,video=false){
 const page=await browser.newPage({viewport:{width,height:900},hasTouch:true});
 if(video)await page.addInitScript(()=>{
  document.addEventListener('error',e=>{if(e.target instanceof HTMLVideoElement)e.stopImmediatePropagation();},true);
  Object.defineProperty(HTMLMediaElement.prototype,'duration',{get(){return 125;}});
  Object.defineProperty(HTMLMediaElement.prototype,'currentTime',{get(){return this.fixtureTime||0;},set(v){this.fixtureTime=v;}});
  HTMLMediaElement.prototype.play=function(){return Promise.resolve();};
  HTMLMediaElement.prototype.pause=function(){};
 });
 const errors=[],requests=[];page.on('pageerror',e=>errors.push(e.message));
 const html=cp.execFileSync('php',['scripts/social_web_fixture.php',lang,guest?'0':'1'],{encoding:'utf8'});
 await page.route('**/*',async route=>{
  const req=route.request(),url=new URL(req.url());
  if(url.pathname.startsWith('/community/api')){
   requests.push({path:url.pathname,query:url.search,method:req.method(),body:req.postData()||''});
   if(fail)return route.fulfill({status:429,headers:{'Retry-After':'120'},contentType:'application/json',body:'{}'});
   let data=[];const p=url.pathname.replace('/community/api','');
   if(p==='/posts')data=url.searchParams.get('kind')==='story'?stories.map(p=>video&&p.id===11?{...p,mime:'video/webm',media:'/fixture-story.webm'}:p):[post];
   if(p==='/posts/9')data=post;
   if(p==='/people')data=[user,{...user,id:3,name:'عضو دوم'}];
   if(p==='/posts/9/comments')data=req.method()==='POST'?{id:42,body:'نظر من',author:user,canDelete:true}:[{id:41,body:'عالی بود',author:user,canDelete:false}];
   if(p==='/posts/9/share')data={sent:[2]};
   if(p.endsWith('/reply'))data={conversation_id:7};
   if(p==='/notifications')data=[{id:50,actor:user,actor_id:2,kind:'like',target_id:9,body:'پست شما را پسندید.',read_at:null}];
   if(p==='/conversations')data=req.method()==='POST'?{id:7}:[{id:7,title:'نوازنده سرناز',image:user.avatar,lastMessage:'سلام',unread:1}];
   if(p==='/conversations/7/messages')data=req.method()==='POST'?{id:101}:{messages:[{id:100,body:'/community/posts/9',mine:false},{id:101,body:'سلام',mine:true}],lastId:101};
   if(p==='/users/2')data=user;
   if(p.endsWith('/react'))data={...post,liked:true,likes:8};
   return route.fulfill({contentType:'application/json',body:JSON.stringify({status:200,data:{success:true,data}})});
  }
  if(req.resourceType()==='document')return route.fulfill({contentType:'text/html; charset=utf-8',body:html});
  if(url.pathname==='/assets/social/community.js'||url.pathname==='/assets/social/community.css'||url.pathname.startsWith('/assets/vendor/vazirmatn/'))return route.fulfill({path:'.'+url.pathname});
  if(req.resourceType()==='image')return route.fulfill({contentType:'image/svg+xml',body:svg});
  return route.abort();
 });
 await page.goto('http://community.test/community');
 return {page,requests,errors};
}
(async()=>{
 const browser=await chromium.launch({channel:'msedge',headless:true});
 fs.mkdirSync('storage/social-web-check',{recursive:true});
 try{
  for(const lang of ['fa','en'])for(const width of [390,1400]){
   const {page,requests,errors}=await setup(browser,lang,width);
   await page.locator('.post-card').waitFor();
   assert.equal(await page.locator('[data-action=story]').count(),1,'group all stories by author');
   assert.equal(await page.locator('.caption img').count(),0,'escape user HTML');
   assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth),false,'no horizontal overflow');
   const initial=requests.length;await page.waitForTimeout(700);assert.equal(requests.length,initial,'no idle polling');
   if(lang==='fa')await page.screenshot({path:`storage/social-web-check/feed-${width}.png`,fullPage:true});
   await page.locator('[data-action=share]').click();await page.locator('dialog input[type=checkbox]').first().check();await page.locator('[data-send-selected]').click();
   await page.locator('dialog').waitFor({state:'detached'});assert.ok(requests.some(r=>r.path.endsWith('/share')&&r.body.includes('user_ids[]')));
   await page.locator('.comments-link').click();await page.locator('#comment-rows .comment').waitFor();
   await page.locator('[data-form=comment] textarea').fill('نظر من');await page.locator('[data-form=comment] [type=submit]').click();await page.locator('[data-comment="42"]').waitFor();
   await page.locator('[data-action=back]').first().click();await page.locator('[data-action=story]').click();
   assert.equal(await page.locator('.story-progress > span').count(),2);
   const stage=await page.locator('.story-stage').boundingBox(),photo=await page.locator('.story-media').boundingBox();assert.equal(Math.round(stage.height),Math.round(photo.height),'edge-to-edge story');
   await page.locator('.story-stage').evaluate(el=>{
    const start=new Touch({identifier:1,target:el,clientX:150,clientY:650});const end=new Touch({identifier:1,target:el,clientX:150,clientY:350});
    el.dispatchEvent(new TouchEvent('touchstart',{bubbles:true,touches:[start],changedTouches:[start]}));
    el.dispatchEvent(new TouchEvent('touchend',{bubbles:true,touches:[],changedTouches:[end]}));
   });
   await page.locator('.story-bottom.open').waitFor();assert.equal(await page.locator('.emoji-row button').count(),6);
   if(lang==='fa')await page.screenshot({path:`storage/social-web-check/story-${width}.png`});
   await page.locator('.emoji-row button').first().click();await page.locator('.story-bottom.open').waitFor({state:'detached'});
   assert.ok(requests.some(r=>r.path==='/community/api/stories/11/reply'&&r.body.includes('❤️')));
   await page.locator('[data-action=close-story]').click();
   await page.goto('http://community.test/community/direct/7');await page.locator('.shared-post').waitFor();
   await page.locator('[data-form=message] textarea').fill('سلام');await page.locator('[data-form=message] [type=submit]').click();
   await page.waitForFunction(()=>document.querySelector('[data-form=message] textarea').value==='');
   await page.goto('http://community.test/community/notifications');await page.locator('[data-action=notification]').click();await page.locator('#comment-rows').waitFor();
   assert.ok(requests.some(r=>r.path.endsWith('/50/read')));
   assert.deepEqual(errors,[]);await page.close();
  }
  const video=await setup(browser,'fa',390,false,false,true);
  await video.page.locator('[data-action=story]').click();
  await video.page.locator('video.story-media').evaluate(v=>{window.testStoryVideo=v;v.dispatchEvent(new Event('loadedmetadata'));});
  assert.equal(await video.page.locator('.story-progress > span').count(),4,'125-second video needs three segments, plus the next image');
  assert.equal(await video.page.locator('video.story-media[controls]').count(),0,'no separate video slider');
  await video.page.evaluate(()=>window.testStoryVideo.currentTime=61);
  await video.page.waitForFunction(()=>window.testStoryVideo.currentTime===60);
  await video.page.evaluate(()=>window.testStoryVideo.currentTime=121);
  await video.page.waitForFunction(()=>window.testStoryVideo.currentTime===120);
  assert.equal(await video.page.evaluate(()=>document.querySelector('video.story-media')===window.testStoryVideo),true,'segment changes must reuse the video');
  assert.deepEqual(video.errors,[]);await video.page.close();
  const guest=await setup(browser,'fa',390,true);await guest.page.locator('.post-card').waitFor();await guest.page.locator('.comments-link').click();await guest.page.locator('#comments').waitFor();assert.equal(await guest.page.locator('[data-form=comment]').count(),0);assert.equal(await guest.page.locator('a[href="/login"]').count(),1);await guest.page.close();
  const failed=await setup(browser,'fa',390,false,true);await failed.page.locator('[data-action=refresh]').waitFor();const count=failed.requests.length;await failed.page.locator('[data-action=refresh]').click();await failed.page.waitForTimeout(200);assert.equal(failed.requests.length,count,'rate limit stops retries');await failed.page.close();
 }finally{await browser.close();}
 console.log('Social web: responsive FA/EN feed, grouped full-screen stories, swipe reply and six emoji, sharing, comments, direct, notifications, guest access, XSS escaping and request cooldown passed.');
})().catch(e=>{console.error(e);process.exitCode=1;});
