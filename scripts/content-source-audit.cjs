// Read-only source candidates; registry matching is not proof of rendered coverage.
const fs=require('node:fs'),path=require('node:path');
const root=path.resolve(__dirname,'..');
const inventory=JSON.parse(fs.readFileSync(path.join(root,'docs/database/content-audit-2026-10-05.local.json'),'utf8'));
const registry=new Set(inventory.settings.f_settings.map(r=>r.variable_name));
const candidates=[],keys=new Map();
function visit(directory){
 for(const entry of fs.readdirSync(directory,{withFileTypes:true})){
  if(['vendor','node_modules','.git','Lib'].includes(entry.name))continue;
  const file=path.join(directory,entry.name);
  if(entry.isDirectory()){visit(file);continue;}
  if(!/\.(php|js)$/.test(entry.name))continue;
  const lines=fs.readFileSync(file,'utf8').split(/\r?\n/),relative=path.relative(root,file).replaceAll('\\','/');
  lines.forEach((line,index)=>{
   for(const match of line.matchAll(/\btrans\(\s*['"]([^'"]+)['"]\s*(?=[,)])/g)){
    if(!keys.has(match[1]))keys.set(match[1],[]);
    keys.get(match[1]).push({file:relative,line:index+1});
   }
   if(/[\u0600-\u06ff]/.test(line)&&!/^\s*(?:\/\/|\*|<!--)/.test(line))candidates.push({file:relative,line:index+1,translation_call:/\btrans\(|translations\(/.test(line),source:line.trim().slice(0,240)});
  });
 }
}
for(const name of ['Modules','core','assets'])visit(path.join(root,name));
const missing=[...keys].filter(([key])=>!registry.has(key)).map(([key,uses])=>({key,uses}));
fs.writeFileSync(path.join(root,'docs/database/content-source-audit-2026-10-05.local.json'),JSON.stringify({persian_source_candidates:candidates,literal_translation_keys:keys.size,unregistered_literal_keys:missing},null,2));
console.log(JSON.stringify({candidate_lines:candidates.length,literal_translation_keys:keys.size,unregistered_literal_keys:missing.length}));
