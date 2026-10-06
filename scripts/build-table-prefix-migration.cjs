// Rebuild deployment artifacts from the single runtime mapping; no DB connection.
const fs = require('node:fs');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const source = fs.readFileSync(path.join(root, 'config/table-names.php'), 'utf8');
const pairs = [...source.matchAll(/"([a-z0-9_]+)"\s*=>\s*"([a-z0-9_]+)"/g)].map(m=>[m[1],m[2]]);
if (!pairs.length || new Set(pairs.map(p=>p[1])).size!==pairs.length || pairs.some(p=>p[1].length>64)) throw new Error('Invalid mapping');
// Historical first-stage schema includes the tuition table merged by stage two.
const bootstrapPairs = [...pairs, ['academy_term_invoice_payments','p_academy_term_invoice_payments']];
const changed = bootstrapPairs.filter(([a,b])=>a!==b);
const directory = path.join(root,'docs/database/migrations');
for(const reverse of [false,true]) {
 const rename = changed.map(([a,b])=>reverse?[b,a]:[a,b]);
 fs.writeFileSync(path.join(directory,'2026_10_04_table_prefixes'+(reverse?'_rollback':'')+'.sql'),
  '-- Explicit maintenance-window migration. Run preflight and take a private backup first.\n'+
  '-- Keep logical discriminator values and existing prefixed tables unchanged.\n'+
  'RENAME TABLE\n'+rename.map(([a,b])=>'  `'+a+'` TO `'+b+'`').join(',\n')+';\n');
}
const mapping = bootstrapPairs.map(([a,b])=>`SELECT '${a}' AS logical_name, '${b}' AS physical_name`).join('\nUNION ALL\n');
const check = `-- Read-only. Every row in the first result must be ready.\nSELECT m.logical_name,m.physical_name,\nCASE WHEN src.TABLE_NAME IS NULL THEN 'MISSING_SOURCE'\n WHEN src.TABLE_TYPE <> 'BASE TABLE' OR src.ENGINE <> 'InnoDB' THEN 'REVIEW_ENGINE'\n WHEN m.logical_name <> m.physical_name AND dst.TABLE_NAME IS NOT NULL THEN 'TARGET_COLLISION'\n ELSE 'READY' END AS migration_status\nFROM (\n${mapping}\n) m\nLEFT JOIN information_schema.TABLES src ON src.TABLE_SCHEMA=DATABASE() AND src.TABLE_NAME=m.logical_name\nLEFT JOIN information_schema.TABLES dst ON dst.TABLE_SCHEMA=DATABASE() AND dst.TABLE_NAME=m.physical_name;\n\n-- All counts below must be zero; visibility depends on the DB account grants.\nSELECT 'views' AS object_type,COUNT(*) AS object_count FROM information_schema.VIEWS WHERE TABLE_SCHEMA=DATABASE()\nUNION ALL SELECT 'triggers',COUNT(*) FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA=DATABASE()\nUNION ALL SELECT 'routines',COUNT(*) FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA=DATABASE()\nUNION ALL SELECT 'events',COUNT(*) FROM information_schema.EVENTS WHERE EVENT_SCHEMA=DATABASE();\n\n-- No cross-schema FK rows should be returned.\nSELECT TABLE_SCHEMA,TABLE_NAME,REFERENCED_TABLE_SCHEMA,REFERENCED_TABLE_NAME\nFROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL\nAND (TABLE_SCHEMA=DATABASE() OR REFERENCED_TABLE_SCHEMA=DATABASE())\nAND (TABLE_SCHEMA<>DATABASE() OR REFERENCED_TABLE_SCHEMA<>DATABASE());\n`;
fs.writeFileSync(path.join(directory,'2026_10_04_table_prefixes_preflight.sql'),check);
fs.writeFileSync(path.join(root,'docs/database/table-prefix-map-2026-10-04.csv'),'\uFEFFlogical_name,physical_name,category,action\n'+pairs.map(([a,b])=>[a,b,b.startsWith('p_')?'project':'framework',a===b?'keep':'rename'].join(',')).join('\n')+'\n');
console.log(`Prefix artifacts: ${pairs.length} current tables; ${bootstrapPairs.length} historical bootstrap tables.`);
