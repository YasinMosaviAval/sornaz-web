const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('assets/Analytics/js/terms.js', 'utf8');
const start = source.indexOf('function openTermMetadataEditor(');
const end = source.indexOf('window.cycleTermStatus =', start);
assert.ok(start >= 0 && end > start, 'Metadata editor not found');
const fields = {
  Name: {value: 'Renamed term', reportValidity() {}},
  Summary: {value: 'Summary'},
  Description: {value: 'Description'},
};
const modal = {innerHTML: ''};
const requests = [];
const context = {
  window: {}, document: {documentElement: {lang: 'en'}, getElementById: () => modal},
  allTerms: [{id: 4, name: '<script>unsafe</script>', summary: '', description: ''}],
  escapeHtml: value => String(value).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;'),
  termField: (prefix, field) => fields[field],
  termApi: async (url, data) => requests.push({url, data}), loadTerms: async () => {},
  closeModal() {}, alert() {}, filteredTerms: [], renderTermsTable() {},
  editingTermRowId: null, attendanceTermRowId: null,
};
vm.createContext(context);
vm.runInContext(source.slice(start, end), context);
(async () => {
  await context.window.editTerm(4);
  assert.ok(modal.innerHTML.includes('&lt;script&gt;unsafe&lt;/script&gt;'));
  assert.ok(!modal.innerHTML.includes('<script>'));
  assert.ok(!modal.innerHTML.includes('editTermCost'));
  await context.window.saveEditedTerm(4);
  assert.equal(requests[0].url, '/academy/admin/terms/4/update');
  assert.deepEqual(JSON.parse(JSON.stringify(requests[0].data)), {
    metadataOnly: true, name: 'Renamed term', summary: 'Summary', description: 'Description',
  });
  fields.Name.value = '   ';
  await context.window.saveEditedTerm(4);
  assert.equal(requests.length, 1, 'Blank title submitted');
  await context.window.toggleTermInlineEdit(4);
  assert.ok(modal.innerHTML.includes('editTermName'));
  console.log('Financial records browser: escaped metadata editor, restricted payload, required title and inline entry passed.');
})().catch(error => { console.error(error); process.exitCode = 1; });
