const assert = require('node:assert/strict');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    for (const language of ['fa','en']) {
      const page = await browser.newPage(), errors=[];
      page.on('pageerror', error=>errors.push(error.message));
      await page.setContent(`<html lang="${language}"><div id="modalContainer"></div></html>`);
      for (const name of ['student-templates','students']) await page.addScriptTag({path:`assets/Analytics/js/${name}.js`});
      await page.evaluate(() => {
        window.studentOrganizations=[{user_id:10,id:100,kind:'academy',name:'Academy'}];
        window.studentTermGroups=['open','ongoing','pending'].map((status,index)=>({id:7+index,key:String(7+index),name:status,organizationUserId:10,status,levels:[]}));
        document.getElementById('modalContainer').innerHTML=getStudentAddModalHTML();
      });
      assert.deepEqual(await page.locator('#stuInstrument option').evaluateAll(rows=>rows.map(row=>row.value)),['','7']);
      await page.evaluate(()=>refreshStudentTerms('stu'));
      assert.deepEqual(await page.locator('#stuInstrument option').allTextContents(),['ترم آموزشی را انتخاب کنید','open']);
      await page.evaluate(()=>document.getElementById('modalContainer').innerHTML=getStudentEditModalHTML({id:1,organizationUserId:10,termKey:'8',instrument:'ongoing'}));
      assert.equal(await page.locator('#editStuInstrument').inputValue(),'8');
      for (const name of ['term-templates','terms']) await page.addScriptTag({path:`assets/Analytics/js/${name}.js`});
      const eligible=await page.evaluate(()=>{
        window.termCourses=[{id:500,student_capacity:2,teacher_capacity:2}];
        const term={id:1,courseId:500,status_code:'open',students:[{id:10}]};
        return [canAssignTermStudent(term),canAssignTermStudent({...term,students:[{id:10},{id:11}]}),canAssignTermStudent({...term,status_code:'ongoing'})];
      });
      assert.deepEqual(eligible,[true,false,false]);
      assert.deepEqual(errors,[]);
      await page.close();
    }
    console.log('Student term dropdown: open-only add/refresh, current ongoing edit preserved and multi-capacity assignment passed (fa/en).');
  } finally {await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
