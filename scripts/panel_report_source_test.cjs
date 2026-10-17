const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const pending = [];
let start;
const tbody = { innerHTML: '', textContent: '', insertRow() { return { insertCell() { return {}; } }; } };
const context = {
  URLSearchParams, Date, console,
  setTimeout(callback) { start = callback; },
  document: { documentElement: { lang: 'fa' },
    getElementById(id) { return id === 'reportsTable' ? {} : null; },
    querySelector() { return tbody; }, querySelectorAll() { return []; },
  },
  fetch(url, options) { return new Promise(resolve => pending.push({ url, options, resolve })); },
  AdminPanelTable: {},
};
context.window = context;
vm.createContext(context);
vm.runInContext(fs.readFileSync('assets/Analytics/js/reports.js', 'utf8'), context);
const rendered = [];
context.renderReportsTable = rows => rendered.push(JSON.parse(JSON.stringify(rows)));
const reply = (request, reports, branches = []) => request.resolve({ ok: true, json: async () => ({ success: true, data: { reports, branches } }) });
const flush = () => new Promise(resolve => setImmediate(resolve));
(async () => {
  start();
  assert.equal(pending.length, 1);
  assert.equal(pending[0].options.credentials, 'same-origin');
  context.filterReportsByBranch('7');
  assert.match(pending[1].url, /branchId=7/);
  reply(pending[1], [{ id: 1, title: '<real>', type: 'مالی', dateISO: '2026-10-17', status: 'آماده' }]);
  await flush();
  assert.equal(rendered.at(-1)[0].title, '<real>');
  reply(pending[0], [{ id: 2, title: 'stale', dateISO: '2026-10-17' }]);
  await flush();
  assert.equal(rendered.at(-1)[0].title, '<real>', 'Stale responses must not replace the selected scope');
  context.filterReportsByBranch('8');
  assert.deepEqual(rendered.at(-1), [], 'Changing scope clears old rows immediately');
  pending[2].resolve({ ok: false, json: async () => ({ success: false, message: 'Denied' }) });
  await flush();
  assert.deepEqual(rendered.at(-1), [], 'Denied requests must not restore other scope rows');
  console.log('Report source: real endpoint, branch query, credentials, stale responses and denied scope passed.');
})().catch(error => { console.error(error); process.exitCode = 1; });
