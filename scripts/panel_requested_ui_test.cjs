const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const source = name => fs.readFileSync(path.join(__dirname, '../assets/Analytics/js', name), 'utf8');

(async () => {
  const window = {AdminPanelTable: {cellsHTML: cells => cells.map(cell => cell.html).join('')}};
  vm.runInNewContext(source('lesson-templates.js'), {window, document: {documentElement: {lang: 'fa'}}});
  const row = {id: 7, title: 'درس', status: 'active', canInspectDependencies: true};
  assert(window.getLessonRowHTML(row).includes('viewLessonUsage(7)'));
  assert(!window.getLessonRowHTML({...row, canInspectDependencies: false}).includes('viewLessonUsage(7)'));
  const usage = window.getLessonDependenciesModalHTML({courses: [{title: '<script>', organization: 'آموزشگاه'}], contracts: []}, true);
  assert(usage.includes('محل‌های استفاده از درس') && usage.includes('&lt;script&gt;') && !usage.includes('<script>'));
  assert(window.getLessonDependenciesModalHTML({courses: [], contracts: []}, true).includes('این درس هنوز استفاده نشده است.'));

  let blob, filename, pdfName, removed = 0;
  const pages = [];
  const levels = {allCourseLevels: [{name: '=danger', summary: 'یک "خلاصه"'}, {name: '<img>', summary: 'شرح'}, {name: ' \t=SUM(1,2)', summary: ''}]};
  levels.AcademyExports = {authorize: async () => true, register() {}};
  const document = {
    getElementById: () => ({}), fonts: {ready: Promise.resolve()},
    body: {appendChild: element => pages.push(element.innerHTML)},
    createElement: () => ({style: {}, click() {filename = this.download;}, remove() {removed++;}}),
  };
  levels.html2canvas = async () => ({width: 800, height: 400, toDataURL: () => 'data:image/png;base64,test'});
  levels.jspdf = {jsPDF: class {constructor() {this.internal = {pageSize:{getWidth:()=>595, getHeight:()=>842}};} addPage() {} addImage() {} save(name) {pdfName=name;} }};
  vm.runInNewContext(source('course-levels.js'), {window: levels, document, Blob, URL:{createObjectURL: value => {blob=value; return 'blob:test';}, revokeObjectURL() {}}, setTimeout: callback => callback(), getComputedStyle: () => ({fontFamily:'ThemeFont'}), alert: message => {throw Error(message);}});
  await levels.exportCourseLevelsToExcel();
  const csv = await blob.text();
  assert(csv.includes('"\'=danger"') && csv.includes('یک ""خلاصه""'));
  assert(csv.includes('"\' \t=SUM(1,2)"'));
  assert.equal(filename, 'سطح درس‌ها.csv');
  await levels.exportCourseLevelsToPDF();
  assert.equal(pdfName, 'سطح درس‌ها.pdf');
  assert.equal(removed, 1);
  assert(pages[0].includes('&lt;img&gt;'));
  console.log('Panel UI: authorized lesson usage, escaping, Excel quoting/formula handling and PDF generation passed.');
})().catch(error => {console.error(error); process.exitCode=1;});
