const fs=require('node:fs');
const vm=require('node:vm');
const path=require('node:path');
const root=process.cwd();
const log=fs.readFileSync('storage/notation-section-meters-release-tests.log','utf16le');
const started=[...log.matchAll(/^Running (.+)\r?$/gm)].map(m=>m[1].trim());
const failures=[...log.matchAll(/FAILED: ([\w.-]+)/g)].map(m=>m[1]);
const count=new Set(failures).size;
const completed=started.slice(0,-1);
let source=fs.readFileSync('scripts/run-release-tests.cjs','utf8');
source=source.replace('let failed=0;',`let failed=${count}; const completed=new Set(${JSON.stringify(completed)});`);
source=source.replace('for (const [command,file] of suites) {','for (const [command,file] of suites) { if(completed.has(file))continue;');
console.log(`Resuming after ${completed.length} completed suites; ${count} earlier failures. Re-running unfinished suite ${started.at(-1)}.`);
vm.runInNewContext(source,{require,process,console,__dirname:path.join(root,'scripts')},{filename:path.join(root,'scripts/run-release-tests.cjs')});

