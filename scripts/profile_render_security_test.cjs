const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('assets/Page/js/site-pages.js', 'utf8');
const box = {innerHTML: ''};
const escape = source.slice(source.indexOf('function escapeHtml('), source.indexOf('function renderRelatedArticles('));
const functions = source.slice(source.indexOf('function renderUserAddresses('), source.indexOf('function renderUserAvailability('));
const context = vm.createContext({document: {getElementById: () => box}});
vm.runInContext(escape + functions, context);
vm.runInContext(source.slice(source.indexOf('function renderProfileContacts('), source.indexOf('function renderProfileCourses(')), context);
for (const command of [
  `renderUserAddresses({addresses:[{address:'<img src=x onerror=alert(1)>',note:'<script>x</script>'}]})`,
  `renderUserContacts({contacts:[{value:'<svg onload=alert(1)>',platform:'<img src=x>',note:'<iframe></iframe>'}]})`,
  `renderProfileAddresses({addresses:[{address:'<img src=x onerror=alert(1)>',postal_code:'<svg onload=alert(1)>'}]})`,
  `renderProfileContacts({contacts:[{mode:'phone',value:'"><img src=x onerror=alert(1)>'}],links:[{title:'<svg onload=alert(1)>',url:'javascript:alert(1)'}]})`,
]) {
  vm.runInContext(command, context);
  assert(!/<(?:img|svg|script|iframe)\b/i.test(box.innerHTML), box.innerHTML);
  assert(box.innerHTML.includes('&lt;'), 'Text was removed instead of escaped');
  assert(!box.innerHTML.includes('href="javascript:'), 'Executable link retained');
}
console.log('Profile rendering: address and contact injection payloads escaped.');
