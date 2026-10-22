const assert=require('node:assert/strict');const {chromium}=require(process.env.PLAYWRIGHT_CORE_PATH||'../storage/notation-browser-check/package');const {tableRuntime,renderPanelView}=require('./panel-table-fixture.cjs');
(async()=>{const browser=await chromium.launch({channel:'msedge',headless:true});try{
 const page=await browser.newPage(),errors=[],writes=[];page.on('pageerror',e=>errors.push(e.message));
 const organizations=[{id:100,user_id:10,kind:'academy',name:'Academy'},{id:101,user_id:11,kind:'branch',name:'Branch'}];
 const discounts=[{id:1,canManage:true,name:'Academy offer',type:'percentage',value:2,status:'active',organizationUserId:10,organizationKind:'academy',branchId:null,organizationName:'Academy'},{id:2,canManage:true,name:'<img src=x onerror=alert(1)>',type:'fixed',value:100,status:'active',organizationUserId:11,organizationKind:'branch',branchId:101,organizationName:'Branch'}];
 await page.route('**/*',async route=>{const path=new URL(route.request().url()).pathname;
 if(/term-discounts\/\d+\/(update|delete)$/.test(path)){
 const id=Number(path.split('/').at(-2)),index=discounts.findIndex(x=>x.id===id);const params=new URLSearchParams(route.request().postData());const data=JSON.parse(Buffer.from(params.get('payload_b64'),'base64').toString());writes.push({operation:path.split('/').at(-1),...data});
 if(path.endsWith('/delete'))discounts.splice(index,1);else Object.assign(discounts[index],data,{name:data.title});
 return route.fulfill({json:{success:true,data:{}}});
 }
 if(path==='/academy/admin/term-discounts'){
  if(route.request().method()==='POST'){const params=new URLSearchParams(route.request().postData());const data=JSON.parse(Buffer.from(params.get('payload_b64'),'base64').toString());writes.push(data);discounts.push({...data,id:3,name:data.title,status:'active',organizationKind:'branch',branchId:101,organizationName:'Branch'});return route.fulfill({json:{success:true,data:{...discounts.at(-1)}}});}
  return route.fulfill({json:{success:true,data:{discounts,organizations,ready:true}}});
 }
 if(path==='/academy/admin/terms')return route.fulfill({json:{success:true,data:{terms:[],currencies:[{id:1,name:'Toman'},{id:2,name:'US Dollar'},{id:3,name:'Euro'},{id:4,name:'British Pound'}],discounts,classrooms:[],members:[]}}});
 if(path==='/academy/admin/courses')return route.fulfill({json:{success:true,data:{organizations,courses:[]}}});
 return route.fulfill({json:{success:true,data:{version:'fixture'}}});});
 await page.goto('http://terms.test/');
 await page.setContent('<html lang="en"><style>.hidden{display:none}</style><main id="mainContent">'+renderPanelView('terms','en')+'</main><div id="modalContainer"></div>'+renderPanelView('panel-page-header-template')+renderPanelView('panel-organization-tabs-template')+renderPanelView('panel-filters-template')+tableRuntime()+'</html>');
 await page.locator('#terms').evaluate(x=>x.classList.remove('hidden'));
 await page.evaluate(()=>{window.alert=()=>{};window.closeModal=()=>document.getElementById('modalContainer').replaceChildren();});
 await page.addScriptTag({path:'assets/theme/dialog.js'});await page.addScriptTag({path:'assets/Analytics/js/panel-organization-filters.js'});await page.addScriptTag({path:'assets/Analytics/js/term-templates.js'});await page.addScriptTag({path:'assets/Analytics/js/terms.js'});
 assert.equal(await page.locator('[data-term-panel=discounts]').isHidden(),true);
 await page.locator('[data-term-tab=discounts]').click();await page.locator('#termDiscountsTable tbody tr[data-id]').first().waitFor();
 assert.equal(await page.locator('#termDiscountsTable tbody tr').count(),2);assert.equal(await page.locator('#termDiscountsTable img').count(),0);
 await page.addScriptTag({path:'assets/vendor/tailwind/tailwindcss.js'});
 for(const width of [390,1280]) {
  await page.setViewportSize({width,height:800});
  for(const dir of ['rtl','ltr']) {
   await page.locator('html').evaluate((el,dir)=>el.dir=dir,dir);
   await page.waitForFunction(()=>getComputedStyle(document.getElementById('termDiscountSearch').parentElement).display==='flex');
   const search=await page.locator('#termDiscountSearch').boundingBox(),type=await page.locator('#termDiscountTypeFilter').boundingBox();
   assert.ok(Math.abs(search.y-type.y)<2,'Discount search and type must share a row');
   assert.ok(Math.abs(search.width-type.width)<2,'Discount search and type must have equal widths');
   const row=await page.locator('#termDiscountSearch').evaluate(el=>{const rect=el.parentElement.getBoundingClientRect();return {left:rect.left,right:rect.right};});
   assert.ok(Math.abs(Math.min(search.x,type.x)-row.left)<2&&Math.abs(Math.max(search.x+search.width,type.x+type.width)-row.right)<2,'Filters must fill their row');
   assert.ok(search.width>0&&type.width>0&&Math.max(search.x+search.width,type.x+type.width)<=width,'Filters must fit the viewport');
  }
 }
 assert.equal(await page.locator('#termDiscountsBranchTabs button').count(),3);
 assert.equal(await page.locator('#termDiscountsTable [data-panel-auto-sort]').count(),4,'All four discount columns are sortable through shared template');
 for(const column of [0,1,2,3]){
  const heading=page.locator('#termDiscountsTable [data-panel-auto-sort="'+column+'"]');
  await heading.click();assert.equal(await heading.locator('..').getAttribute('aria-sort'),'ascending');
  if(column===2)assert.deepEqual(await page.locator('#termDiscountsTable tbody tr[data-id]').evaluateAll(rows=>rows.map(row=>Number(row.dataset.id))),[1,2],'Discount value sorts numerically');
  await heading.click();assert.equal(await heading.locator('..').getAttribute('aria-sort'),'descending');
  if(column===2)assert.deepEqual(await page.locator('#termDiscountsTable tbody tr[data-id]').evaluateAll(rows=>rows.map(row=>Number(row.dataset.id))),[2,1]);
 }

 await page.selectOption('#termDiscountTypeFilter','percentage');assert.equal(await page.locator('#termDiscountsTable tbody tr[data-id]').count(),1);
 await page.selectOption('#termDiscountTypeFilter','');await page.locator('#termDiscountsBranchTabs [data-value="101"]').click();await page.waitForFunction(()=>document.querySelector('#termDiscountsTable tbody tr')?.dataset.id==='2');
 await page.locator('#termDiscountSearch').fill('not found');assert.equal(await page.locator('#termDiscountsTable tbody tr[data-id]').count(),0);await page.locator('#termDiscountSearch').fill('');
 await page.evaluate(()=>openAddTermDiscountModal());await page.locator('#newTermDiscountTitle').waitFor();assert.equal(await page.locator('#newTermDiscountOrganization').inputValue(),'11');
 assert.deepEqual(await page.locator('#newTermDiscountType option').allTextContents(),['Percentage','Fixed amount']);
 await page.locator('#newTermDiscountTitle').fill('New offer');await page.selectOption('#newTermDiscountType','fixed');await page.locator('#newTermDiscountValue').fill('250');await page.locator('#saveTermDiscountButton').click();
 await page.waitForFunction(()=>Array.from(document.querySelectorAll('#termDiscountsTable tbody tr')).some(x=>x.textContent.includes('New offer')));assert.equal(writes[0].organizationUserId,11);assert.equal(writes[0].type,'fixed');
 await page.locator('#termDiscountsTable tr[data-id="2"] button').filter({hasText:'Details'}).click();
 assert.equal(await page.locator('#modalContainer [role=dialog] img').count(),0);assert((await page.locator('#modalContainer dl').textContent()).includes('<img'));await page.evaluate(()=>closeModal());
 await page.locator('#termDiscountsTable tr[data-id="2"] button').filter({hasText:'Edit'}).click();
 await page.locator('[data-discount-editor="2"] input[name=title]').fill('Edited offer');await page.locator('[data-discount-editor="2"] button[type=submit]').click();await page.waitForFunction(()=>document.querySelector('#termDiscountsTable tr[data-id="2"]')?.textContent.includes('Edited offer'));
 await page.locator('#termDiscountsTable tr[data-id="2"] button').filter({hasText:'Delete'}).click();await page.locator('#appDialogHost .app-dialog__button--cancel').click();await page.waitForTimeout(180);assert.equal(writes.filter(x=>x.operation==='delete').length,0);
 await page.locator('#termDiscountsTable tr[data-id="2"] button').filter({hasText:'Delete'}).click();await page.locator('#appDialogHost [data-dialog-result=confirm]').click();await page.waitForFunction(()=>!document.querySelector('#termDiscountsTable tr[data-id="2"]'));assert.equal(writes.filter(x=>x.operation==='delete').length,1);
 await page.evaluate(()=>loadTerms());
 const forms=await page.evaluate(()=>[getTermAddModalHTML(),getTermEditModalHTML({id:1,sessionCount:1}),getTermInlineEditRowHTML({id:1,sessionCount:1})]);
 forms.forEach(html=>{assert(!html.includes('openAddTermDiscountModal'));assert(!html.includes('promptAddTermDiscount'));assert(html.includes('British Pound'));});
 await page.locator('[data-term-tab=terms]').click();assert.equal(await page.locator('[data-term-panel=discounts]').isHidden(),true);
 assert.deepEqual(errors,[]);console.log('Term discounts UI: shared table, tabs, organization/type/search filters, escaped text, organization save, detail dialog, inline edit, confirmed/cancelled delete and term currency forms passed.');
}finally{await browser.close();}})().catch(e=>{console.error(e);process.exitCode=1;});
