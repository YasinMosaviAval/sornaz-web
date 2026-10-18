const assert = require('node:assert/strict');
const fs = require('node:fs');
const {spawnSync} = require('node:child_process');
const {chromium} = require(process.env.PLAYWRIGHT_CORE_PATH || '../storage/notation-browser-check/package');
function render(language) {
  const php = `<?php
function locale(){return '${language}';} function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');} function csrf_token(){return 'fixture';} function base_path($p){return getcwd().'/'.$p;}
$enrollmentData=['academy'=>['id'=>1,'title'=>'Academy'],'selectedTerm'=>7,'terms'=>[['id'=>7,'title'=>'Piano','levels'=>[['id'=>3,'title'=>'Level']]]], 'profile'=>['name'=>'Applicant','national_id'=>'1234567890','father_name'=>'','birth_date'=>'1990-01-01','phone'=>'09121234567','address'=>'<script>unsafe</script>'], 'addressDetails'=>['province_id'=>1,'county_id'=>2], 'provinces'=>[['province_id'=>1,'province_name'=>'Province']], 'counties'=>[['county_id'=>2,'province_id'=>1,'county_name'=>'City'],['county_id'=>3,'province_id'=>9,'county_name'=>'Foreign city']]];
require 'Modules/Analytics/Resources/Views/academy-enroll.php';`;
  const result = spawnSync('php', [], {input:php, encoding:'utf8'});
  assert.equal(result.status, 0, result.stderr);
  return result.stdout;
}
(async () => {
  const browser = await chromium.launch({channel:'msedge',headless:true});
  try {
    for (const language of ['fa','en']) {
      const page = await browser.newPage();
      await page.route('http://sornaz.test/**', route => route.fulfill({body:'<html></html>',contentType:'text/html'}));
      await page.goto('http://sornaz.test/academy/academy?id=1');
      const html = render(language);
      const form = html.match(/<form\b[\s\S]*?<\/form>/)[0];
      const data = JSON.parse(html.match(/id="siteEnrollmentData">([\s\S]*?)<\/script>/)[1]);
      await page.setContent(`<html lang="${language}"><style>.hidden{display:none}</style>${form}</html>`);
      await page.addScriptTag({path:'assets/Page/js/enrollment.js'});
      await page.evaluate(data => initSiteEnrollment(document, data, true), data);
      assert.equal(await page.locator('[name=name]').isVisible(), false);
      assert.equal(await page.locator('[name=father_name]').isVisible(), true);
      assert.equal(await page.locator('[name=address]').inputValue(), '<script>unsafe</script>');
      assert.equal(await page.locator('[name=county_id]').inputValue(), '2');
      assert.equal(await page.locator('[name=county_id] option').count(), 2);
      assert.equal(await page.locator('[name=term_id]').isVisible(), false);
      assert.equal(await page.locator('[name=level_id]').inputValue(), '3');
      await page.locator('[name=father_name]').fill('Father');
      await page.evaluate(() => { window.fetch = async () => ({ok:false,json:async()=>({success:false,message:'Rejected fixture'})}); });
      await page.locator('[type=submit]').click();
      await page.waitForFunction(() => document.getElementById('siteEnrollMessage').textContent === 'Rejected fixture');
      assert.equal(await page.locator('[type=submit]').isEnabled(), true);
      assert.equal(await page.locator('[name=father_name]').inputValue(), 'Father');
      await page.evaluate(() => { window.fetch = async () => ({ok:true,json:async()=>({success:true,redirect:'/analytics/admin-panel?conversation=12#chat',data:{conversationId:12}})}); });
      await page.locator('[type=submit]').click();
      await page.waitForURL('**/analytics/admin-panel?conversation=12#chat');
      await page.close();
    }
    const page = await browser.newPage();
    await page.setContent('<div id="account"><div class="bg-white" id="cover"></div><div class="bg-white"><h3>Account</h3><input value="retained"></div></div>');
    const source = fs.readFileSync('assets/Analytics/js/account.js','utf8');
    await page.addScriptTag({content:source.slice(source.indexOf('  function initAccountSections()'),source.indexOf('  // ===================='))});
    assert.equal(await page.locator('#cover details').count(),0);
    assert.equal(await page.locator('details').getAttribute('open'),null);
    await page.locator('summary').click();
    assert.equal(await page.locator('input').isVisible(),true);
    await page.locator('input').fill('unchanged across toggles');
    await page.locator('summary').click();
    await page.locator('summary').click();
    assert.equal(await page.locator('input').inputValue(),'unchanged across toggles');
    console.log('Enrollment UI: Persian/English, missing fields, saved address, scoped cities, escaped values, retry, chat redirect and account collapse passed.');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode=1; });
