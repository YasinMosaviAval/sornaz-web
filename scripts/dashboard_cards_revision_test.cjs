const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const root = path.join(__dirname, '..');
const window = {};
vm.runInNewContext(fs.readFileSync(path.join(root, 'assets/Analytics/js/dashboard-templates.js'), 'utf8'), {window});
const html = window.getDashboardStatsHTML({
  activeStudents: 11, monthlyIncome: 22, pendingPayments: 33, newStudentsWeek: 44,
  todayClasses: 55, attendanceRate: '66%', newMessages: 77, pointsAwarded: 88,
});
for (const label of ['هنرجویان فعال', 'درآمد ماهانه', 'پرداخت‌های معوق', 'ثبت‌نام هفته']) assert(html.includes(label));
for (const label of ['کلاس‌های امروز', 'نرخ حضور', 'پیام‌های جدید', 'امتیازهای اهداشده']) assert(!html.includes(label));
assert.equal((html.match(/hover:shadow-md/g) || []).length, 4);
assert(window.getDashboardStatsHTML({activeStudents: '<script>'}).includes('&lt;script&gt;'));
console.log('Dashboard stats: four cards, removed metrics and escaping passed.');

const elements = {
  accountInfo: {innerHTML: ''},
  account: {classList: {contains: () => false}},
  accountPageTitle: {textContent: ''},
  panelPageTitle: {textContent: ''},
};
const accountWindow = {addEventListener() {}};
vm.runInNewContext(fs.readFileSync(path.join(root, 'assets/Analytics/js/account.js'), 'utf8'), {
  window: accountWindow,
  document: {querySelectorAll: () => [], getElementById: id => elements[id] || null},
  setTimeout() {},
});
for (const name of ['renderAccountCover', 'renderAccountDocuments', 'renderAccountDevices', 'renderAccountLoginHistory', 'renderAccountSecurityAlerts', 'renderAccountPrivacy', 'renderAccountBackupStatus', 'loadAccountInviteLink', 'renderUserMerges']) accountWindow[name] = () => {};
accountWindow.renderAccountInfo().then(() => {
  assert.equal(elements.accountPageTitle.textContent, 'حساب کاربری');
  assert.equal(elements.panelPageTitle.textContent, 'حساب کاربری');
  console.log('Account title: page and panel header use the plain account title.');
}).catch(error => {console.error(error); process.exitCode = 1;});
