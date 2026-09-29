const assert = require('node:assert/strict');
const fs = require('node:fs');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
 const browser = await chromium.launch({channel:'msedge',headless:true});
 try {
  const page=await browser.newPage();const errors=[];page.on('pageerror',e=>errors.push(e.message));
  let delayed=null, edited=false, contactOK=false, contactCalls=0,addMessage=false;
  const message=(id,body,extra={})=>({id,body,sender:'Member',createdAt:'2026-09-28T10:00:00+03:30',mine:false,...extra});
  const first=message(21,'<script>window.injected=true</script>',{reply:{body:'Quoted reply'},reference:{id:9,kind:'post',available:false}});
  await page.route('**/*',async route=>{
   const url=new URL(route.request().url());
   if(url.pathname==='/assets/Analytics/js/chat.js')return route.fulfill({path:'assets/Analytics/js/chat.js'});
   if(url.pathname==='/analytics/chat')return route.fulfill({json:{data:{success:true,data:{conversations:[{id:1,type:'direct',title:'First'},{id:2,type:'group',title:'Second'}],users:[]}}}});
   if(url.pathname==='/analytics/chat/1/messages'){delayed=route;return;}
   if(url.pathname==='/analytics/chat/2/messages'){
    const after=Number(url.searchParams.get('after'));
    const data=after===0?{messages:[first],lastId:21,hasMore:true}:after===21?{messages:[message(22,'Group renamed',{system:true})],lastId:22,hasMore:false}:edited?{messages:[],updated:[message(21,'Edited content',{likes:2})],deletedIds:[22],lastId:22}:{messages:[],updated:[first,message(22,'Group renamed',{system:true})],lastId:22};
    if(addMessage&&after>=22){data.messages=[message(23,'New arrival')];data.lastId=23;}
    return route.fulfill({json:{data:{success:true,data}}});
   }
   if(url.pathname==='/contact'){contactCalls++;return route.fulfill({status:contactOK?201:503,json:{data:{success:contactOK,message:contactOK?'Saved':'Storage unavailable'}}});}
   return route.fulfill({contentType:'text/html; charset=utf-8',body:`<html lang="en"><body><div id="chat"><div id="chatConversationList"></div><div id="chatSidebar"></div><div id="chatRoom"><div id="chatRoomAvatar"></div><div id="chatRoomTitle"></div><div id="chatRoomMeta"></div><div id="chatMessages"></div></div></div><form id="contactPublicForm"><input id="cName" value="Visitor"><input id="cEmail" value="visitor@example.test"><input id="cSubject" value="Question"><textarea id="cMessage">Keep this message</textarea><button type="submit">Send</button></form><script>window.alert=()=>{};window.setInterval=fn=>{window.poll=fn;return 1;};</script><script src="/assets/Analytics/js/chat.js"></script></body></html>`});
  });
  await page.goto('http://sornaz.test/');await page.evaluate(()=>window.chatReady);
  await page.evaluate(()=>{window.pendingFirst=window.openChat(1);});
  await page.waitForFunction(()=>document.getElementById('chatRoomTitle').textContent==='First');
  // A second room must load even while the previous request remains in flight.
  await page.evaluate(()=>window.openChat(2));
  await page.waitForSelector('#chatMessage-22');
  await delayed.fulfill({json:{data:{success:true,data:{messages:[message(11,'Wrong room')],lastId:11}}}});
  await page.evaluate(()=>window.pendingFirst);
  assert.equal(await page.locator('#chatMessage-11').count(),0);
  assert.equal(await page.locator('#chatMessage-21').count(),1);
  assert.equal(await page.evaluate(()=>window.injected),undefined);
  assert.match(await page.locator('#chatMessage-21').innerText(),/Quoted reply/);
  assert.match(await page.locator('#chatMessage-21').innerText(),/no longer available/);
  assert.equal(await page.locator('#chatMessage-22').getAttribute('data-system-enhanced'),'1');
  edited=true;await page.evaluate(()=>window.poll());
  await page.waitForFunction(()=>document.getElementById('chatMessage-21')?.textContent.includes('Edited content'));
  assert.equal(await page.locator('#chatMessage-22').count(),0);
  assert.equal(await page.locator('#chatMessage-21 [data-chat-like-count]').innerText(),'2');
  await page.evaluate(()=>{window.preservedMessage=document.getElementById('chatMessage-21');});
  addMessage=true;await page.evaluate(()=>window.poll());await page.waitForSelector('#chatMessage-23');
  assert.equal(await page.evaluate(()=>window.preservedMessage===document.getElementById('chatMessage-21')),true);
  const source=fs.readFileSync('assets/Page/js/main.js','utf8');
  const match=source.match(/window\.submitPublicContact\s*=\s*async\s+function\s*\(e\)\s*\{[\s\S]*?^\};/m);
  assert.ok(match,'Contact handler was not found in the public page script');
  const contact=match[0];
  await page.addScriptTag({content:contact});
  await page.evaluate(()=>window.submitPublicContact({preventDefault(){}}));
  assert.equal(await page.locator('#cMessage').inputValue(),'Keep this message');
  assert.equal(await page.locator('button[type=submit]').isEnabled(),true);
  contactOK=true;await page.locator('#cMessage').fill('New message');
  await page.evaluate(()=>Promise.all([window.submitPublicContact({preventDefault(){}}),window.submitPublicContact({preventDefault(){}})]));
  assert.equal(contactCalls,2);assert.equal(await page.locator('#cMessage').inputValue(),'Keep this message');
  assert.deepEqual(errors,[]);
  console.log('Browser checks passed: room-switch race, backlog, edits/deletes, reactions, escaped content, replies, references, system messages and contact failure/success.');
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
