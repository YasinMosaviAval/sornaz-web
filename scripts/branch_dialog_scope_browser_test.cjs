const assert = require('node:assert/strict');
const { execFileSync } = require('node:child_process');
const { chromium } = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
const { renderPanelView } = require('./panel-table-fixture.cjs');
const viewFixture = String.raw`namespace Core\database { class DB {static function table($name){return new class{function __call($name,$args){return $name==='first'?null:$this;}};}} }
namespace Modules\System\Services {class SiteAdminAccess {static function allows($user){return false;}} class AccessControl {function allows($id,$permission){return false;}}}
namespace Modules\Academy\Services {class AcademyClassroomService {const CREATE_TYPE_PERMISSION='classroom_types.create';}}
namespace Modules\Analytics\Services {class MobilePanelAccess {function capabilities($user){return ['branchContext'=>true];}}}
namespace {function auth(){return new class {function user(){return ['user_id'=>12,'type'=>'human'];}};} function locale(){return 'fa';} function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');} function component($name,$data=[]){extract($data);require 'Modules/Analytics/Resources/Views/sections/'.$name.'.php';} require 'Modules/Analytics/Services/PanelTableRenderer.php'; component('classroom-types');}`;
(async () => {
 const browser = await chromium.launch({channel:'msedge',headless:true});
 try {
  const page=await browser.newPage();const errors=[];page.on('pageerror',error=>errors.push(error.message));
  const types=execFileSync('php',['-r',viewFixture],{encoding:'utf8'});
  assert(types.includes('id="classroom-types"'),'Branch employee receives the real classroom types section without create permission');
  const classroom=renderPanelView('classrooms');
  await page.setContent('<html lang="fa"><style>.hidden{display:none}.block{display:block}.grid{display:grid}</style>'+classroom+types+'<div id="modalContainer"></div></html>');
  await page.addScriptTag({content:'let allBranches=[{id:101,name:"Own branch"}]; window.panelBranchContext=true; window.branchAccount=false;'});
  await page.addScriptTag({path:'assets/Analytics/js/panel-organization-filters.js'});
  await page.addScriptTag({path:'assets/Analytics/js/finance-templates.js'});
  await page.addScriptTag({path:'assets/Analytics/js/finance.js'});
  for(const language of ['fa','en']){
   await page.evaluate(lang=>document.documentElement.lang=lang,language);
   for(const mode of ['add','edit','inline']){
    await page.evaluate(mode=>{
     const row={id:7,branchId:101,statusCode:'issued',amount:100,title:'Test'};
     document.getElementById('modalContainer').innerHTML=mode==='add'?getFinanceAddModalHTML():mode==='edit'?getFinanceEditModalHTML(row):getFinanceInlineEditRowHTML(row);
    },mode);
    const field=page.locator('#modalContainer [data-panel-organization-field]');
    await page.waitForFunction(()=>document.querySelector('#modalContainer [data-panel-organization-field]').parentElement.hidden);
    assert.equal(await field.isVisible(),false);
    assert.equal(await field.locator('..').locator('label').isVisible(),false,'Branch label is hidden with the control');
    assert.equal(await field.inputValue(),'101','Submission retains branch ID');
    assert.equal(await page.evaluate(mode=>readFinanceForm(mode==='add'?'':mode==='edit'?'editTrans':'inlineTrans7').branchId,mode),101,'Actual transaction reader retains fixed branch ID');
    assert.equal(await page.locator('#modalContainer input[id$="Title"]').isVisible(),true,'Other transaction fields remain usable');
   }
  }
  await page.evaluate(()=>{window.panelBranchContext=false;window.branchAccount=true;document.getElementById('modalContainer').innerHTML=getFinanceAddModalHTML();});
  await page.waitForFunction(()=>document.querySelector('#transBranch').parentElement.hidden);
  await page.evaluate(()=>document.getElementById('modalContainer').innerHTML='<label class="block">Organization<select data-panel-organization-field id="lateOrg"><option value="">Select</option></select></label><input id="keepField">');
  await page.evaluate(()=>document.getElementById('lateOrg').insertAdjacentHTML('beforeend','<option value="11">Own branch account</option>'));
  await page.waitForFunction(()=>document.getElementById('lateOrg').value==='11');
  assert.equal(await page.locator('#lateOrg').isVisible(),false);
  assert.equal(await page.locator('#keepField').isVisible(),true);
  await page.evaluate(()=>{
    window.academyPanelBranches=[{id:101,user_id:11,name:'Own branch'}];
    document.getElementById('modalContainer').innerHTML='<div><label>Organization</label><select data-panel-organization-field id="accountOrg"><option value="10">Academy</option><option value="11">Own branch</option></select></div><div><label>Branch</label><select data-panel-organization-field data-panel-organization-value="branch" id="branchOrg"><option value="102">Other branch</option><option value="101">Own branch</option></select></div>';
  });
  await page.waitForFunction(()=>document.getElementById('accountOrg').value==='11' && document.getElementById('branchOrg').value==='101');
  assert.equal(await page.locator('#accountOrg').isVisible(),false);
  assert.equal(await page.locator('#branchOrg').isVisible(),false);

  await page.evaluate(()=>{window.branchAccount=false;window.panelBranchContext=false;allBranches=[{id:101,name:'Own branch'},{id:102,name:'Other branch'}];document.getElementById('modalContainer').innerHTML=getFinanceAddModalHTML();});
  assert.equal(await page.locator('#transBranch').isVisible(),true,'Academy manager keeps organization selection');
  await page.selectOption('#transBranch','102');assert.equal(await page.locator('#transBranch').inputValue(),'102');
  await page.evaluate(()=>document.dispatchEvent(new Event('DOMContentLoaded')));
  // The page sections are normally switched by admin.js; inspect tab visibility independently.
  await page.evaluate(()=>document.getElementById('classrooms').classList.remove('hidden'));
  assert.equal(await page.locator('#classrooms [data-classroom-types-tab]').isVisible(),true,'Types tab is visible when branch section is present');
  assert.deepEqual(errors,[]);
  console.log('Branch dialog scope: add/edit/inline transactions, complete labels, fixed IDs, late catalogs, academy selection and classroom type tabs passed.');
 } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
