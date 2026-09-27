const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const calls = [];
const window = {location: {href:'https://site.test/page',origin:'https://site.test'},siteCsrfToken:'fixture',fetch:(...args)=>calls.push(args)};
vm.runInNewContext(fs.readFileSync('assets/theme/csrf.js','utf8'),{window,URL,Headers,Request,document:{querySelector:()=>null}});
for (const method of ['POST','PUT','PATCH','DELETE']) {
    window.fetch('/action',{method}); assert.equal(calls.at(-1)[1].headers.get('X-CSRF-TOKEN'),'fixture');
}
window.fetch('https://other.test/action',{method:'POST'}); assert.equal(calls.at(-1)[1].headers,undefined);
window.fetch('/read'); assert.equal(calls.at(-1)[1].headers,undefined);
window.fetch(new Request('https://site.test/action',{method:'POST',headers:{'X-Custom':'yes'}}));
assert.equal(calls.at(-1)[1].headers.get('X-Custom'),'yes');
assert.equal(calls.at(-1)[1].headers.get('X-CSRF-TOKEN'),'fixture');
window.fetch('/action',{method:'POST',headers:{'X-CSRF-TOKEN':'explicit'}});
assert.equal(calls.at(-1)[1].headers.get('X-CSRF-TOKEN'),'explicit');
console.log('CSRF fetch: same-origin mutations, external requests, Request headers and explicit tokens passed.');
