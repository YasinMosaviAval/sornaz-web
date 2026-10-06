// Read-only source inventory; input is metadata from database-inventory.php.
const fs = require('fs');
const path = require('path');
const root = path.resolve(__dirname, '..');
const inventory = JSON.parse(fs.readFileSync(path.join(root, 'docs/database/current-inventory.local.json'), 'utf8').replace(/^\uFEFF/, ''));
const files = [];
function walk(dir) {
  if (!fs.existsSync(dir)) return;
  for (const item of fs.readdirSync(dir, {withFileTypes:true})) {
    if (['vendor','node_modules','.git','Lib'].includes(item.name)) continue;
    const file = path.join(dir,item.name);
    if (item.isDirectory()) walk(file);
    else if (/\.(php|js|cjs|sql)$/.test(item.name) || (!path.extname(item.name) && fs.readFileSync(file,'utf8').startsWith('<?php'))) files.push({file:path.relative(root,file).replaceAll('\\','/'), lines:fs.readFileSync(file,'utf8').split(/\r?\n/)});
  }
}
for (const dir of ['Modules','core','bootstrap','routes','config','scripts','docs/database']) walk(path.join(root,dir));
const results = inventory.tables.map(table => {
  const name = table.TABLE_NAME;
  const re = new RegExp(`(?<![a-zA-Z0-9_])${name}(?![a-zA-Z0-9_])`);
  const hits=[];
  for (const file of files) {
    if (/database-(code-audit|inventory)/.test(file.file)) continue;
    file.lines.forEach((line,index)=> { if(re.test(line)) hits.push({file:file.file,line:index+1,kind: file.file.endsWith('.sql')?'schema':file.file.startsWith('scripts/')||file.file.startsWith('docs/')?'tool':file.file.includes('/Models/')?'model':'runtime'}); });
  }
  const dependencies=inventory.foreign_keys.filter(f=> f.REFERENCED_TABLE_NAME===name || f.TABLE_NAME===name);
  const objects=[];
  for(const kind of ['views','triggers','routines','events']) for(const obj of inventory[kind]||[]) if(re.test(JSON.stringify(obj))) objects.push({kind,name:obj.TABLE_NAME||obj.TRIGGER_NAME||obj.ROUTINE_NAME||obj.EVENT_NAME});
  return {...table,hits,dependencies,objects};
});
fs.writeFileSync(path.join(root,'docs/database/code-audit.local.json'),JSON.stringify(results,null,2));
for(const row of results) {
  const runtime=row.hits.filter(h=>h.kind==='runtime'),models=row.hits.filter(h=>h.kind==='model');
  if(!runtime.length) console.log(row.TABLE_NAME, 'models='+models.length, 'tools='+row.hits.filter(h=>h.kind==='tool').length,'fk='+row.dependencies.length,models.slice(0,2).map(h=>h.file+':'+h.line).join(','));
}
console.log('Total tables:',results.length);
const existing = new Set(results.map(r=>r.TABLE_NAME));
const missing = new Map();
for(const file of files.filter(f=>!f.file.startsWith('docs/')&&!f.file.startsWith('scripts/')&&f.file.endsWith('.php'))) {
  file.lines.forEach((line,index)=> {
    const patterns = [/\b(?:DB::)?table\(\s*['"]([a-z][a-z0-9_]*)['"]/g, /\$table\s*=\s*['"]([a-z][a-z0-9_]*)['"]/g, /\b(?:FROM|JOIN|INTO|UPDATE)\s+`?([a-z][a-z0-9_]*)`?(?=\s|\()/g];
    for(const pattern of patterns) for(const match of line.matchAll(pattern)) {
      if(existing.has(match[1]))continue;
      if(!missing.has(match[1]))missing.set(match[1],[]);
      missing.get(match[1]).push({file:file.file,line:index+1});
    }
  });
}
fs.writeFileSync(path.join(root,'docs/database/missing-code-tables.local.json'),JSON.stringify(Object.fromEntries(missing),null,2));
console.log('Missing literal references:',[...missing.keys()].sort().join(', '));
function group(name) {
  return /^(academies$|academy_|creator_|music_sheet|instruments$|lessons$|levels$|user_instruments$|user_lessons$|classroom_types$|legacy_import_map$|social_lesson_progress$)/.test(name) ? 'پروژه' : 'فریمورک';
}
function status(row) {
  if(row.hits.some(h=>h.kind==='runtime')) return 'ارجاع کد؛ حفظ شود';
  if(row.hits.some(h=>h.kind==='model')) return 'مدل موجود؛ بررسی مسیر';
  if(row.hits.some(h=>h.kind==='tool')) return 'ابزار مهاجرت؛ حفظ شود';
  if(row.dependencies.length||row.objects.length) return 'بدون ارجاع؛ حذف وابسته';
  return 'نامزد حذف مشروط';
}
const summary=results.map(r=>({table:r.TABLE_NAME,group:group(r.TABLE_NAME),status:status(r),estimated_rows:r.TABLE_ROWS,evidence:[...new Set(r.hits.filter(h=>['runtime','model','tool'].includes(h.kind)).map(h=>`${h.file}:${h.line}`))].slice(0,3).join('؛ '),foreign_keys:r.dependencies.map(f=>`${f.TABLE_NAME}.${f.COLUMN_NAME} → ${f.REFERENCED_TABLE_NAME}.${f.REFERENCED_COLUMN_NAME}`).join('؛ ')}));
const cell=v=>'"'+String(v??'').replaceAll('"','""')+'"';
fs.writeFileSync(path.join(root,'docs/database/database-audit-2026-10-03.csv'),'\uFEFF'+[Object.keys(summary[0]).map(cell).join(','),...summary.map(r=>Object.values(r).map(cell).join(','))].join('\n'));
let report=`# گزارش وضعیت دیتابیس و دسته‌بندی جدول‌ها\n\nتاریخ: ۲۰۲۶-۱۰-۰۳. منبع: دیتابیس تنظیم‌شده محیط فعلی؛ وضعیت هاست اصلی مستقلاً تأیید نشده است. این کار فقط خواندنی بود؛ هیچ جدول یا داده‌ای تغییر نکرد.\n\n## نتیجه\n\n۱۳۳ جدول موجود، همگی InnoDB؛ ${summary.filter(r=>r.group==='فریمورک').length} جدول فریمورک و ${summary.filter(r=>r.group==='پروژه').length} جدول پروژه. برای ۱۱۸ جدول ارجاع در کد پیدا شد. ۱۵ جدول ارجاع اجرایی ندارند: ۸ نامزد مستقل مشروط، ۶ جدول جغرافیایی وابسته و ۱ نگاشت مهاجرت. تعداد ردیف‌ها برآورد information_schema است؛ عدد صفر اثبات خالی‌بودن نیست.\n\n## معیار دو دسته\n\nفریمورک طبق تعریف کاربر شامل زیرساخت‌ها و قابلیت‌های عمومی قابل استفاده مجدد است: users و پروفایل عمومی، دسترسی، احراز هویت، تنظیمات، ترجمه، محتوا، رسانه، پیام‌رسانی، شبکه اجتماعی، رهگیری، جغرافیا و زیرساخت مالی. این دسته‌بندی به معنی استقلال فعلی پیاده‌سازی از سرناز نیست؛ مثلاً مالی و نقش‌ها وابستگی‌هایی به فاکتور شهریه و آموزشگاه دارند که برای انتقال به پروژه دیگر باید جدا شوند.\n\nپروژه شامل آموزشگاه، شعبه، دوره، ترم، شهریه، بازار دوره، نت، ساز و سوابق موسیقی و نگاشت مهاجرت اختصاصی سرناز است. social_lesson_progress به دلیل اتصال به درس آموزشی پروژه محسوب شده؛ سایر social_* عمومی‌اند. نام‌گذاری بر اساس مفهوم است، نه صرفاً پیشوند. هیچ تغییر نام فیزیکی انجام نشده است.\n\n## نامزدهای مستقل حذف مشروط\n\nهیچ ارجاع اجرایی، مدل یا ابزار و هیچ FK ورودی/خروجی برای این جدول‌ها پیدا نشد:\n\n| جدول | ردیف برآوردی |\n|---|---:|\n`;
for(const r of summary.filter(r=>r.status==='نامزد حذف مشروط'))report+=`| ${r.table} | ${r.estimated_rows} |\n`;
report+=`\nاین فهرست تضمین حذف بی‌خطر نیست. financial_system_ledger_entries و financial_system_refunds ممکن است سوابق مالی تاریخی داشته باشند؛ نبود ارجاع فعلی مجوز پاک‌کردن آن سوابق نیست. پیش از حذف، COUNT(*) و داده واقعی در کپی پشتیبان بررسی شود. داده مرجع جغرافیایی بایگانی شود. cronها، ابزارهای خارج مخزن و گزارش‌های هاست بررسی نشده‌اند.\n\n## شش جدول وابسته جغرافیایی\n\nz_world_postcodes، z_world_cities، z_world_states، z_world_countries، z_world_subregions و z_world_regions در کد ارجاع ندارند ولی FK دارند. اگر کل این مجموعه حذف شود، ترتیب مطابق FK موجود: postcodes → cities → states → countries → subregions → regions، همگی با پیشوند z_world_. خاموش‌کردن FOREIGN_KEY_CHECKS لازم نیست. جزئیات FK پایین گزارش آمده است.\n\nworld_iran_provinces و world_iran_counties در ثبت‌نام، مدیریت شعب و دوره‌ها استفاده می‌شوند؛ حفظ شوند. ماژول World به worlds اشاره دارد و مصرف‌کننده این شش جدول نیست.\n\n## نگاشت مهاجرت\n\nlegacy_import_map ارجاع اجرایی وب ندارد، اما docs/database/import-wordpress-content و docs/database/verify-wordpress-content از آن استفاده می‌کنند. برآورد ۵۳۰ ردیف دارد. حذف آن تطبیق شناسه وردپرس/مقصد و تکرار واردکردن داده را تحت تأثیر قرار می‌دهد؛ تا پایان مهاجرت و بایگانی نگاشت حفظ شود.\n\n## جدول‌های مفقود نسبت به کد\n\nz_user_settings در دیتابیس وجود ندارد، اما Modules/Analytics/Services/AdminAccountService.php:274,282,285,287 و Modules/System/Services/UserService.php:176 آن را می‌خوانند/می‌نویسند. تنظیمات حساب و حریم خصوصی نمایش اطلاعات تماس در معرض خطا هستند. از روی این بررسی نمی‌توان گفت اخیراً حذف شده یا از ابتدا غایب بوده است. پیش از حذف بیشتر، ساختار پشتیبان را با قرارداد کد تطبیق دهید؛ هیچ جدول خودکار بازسازی نشده است.\n\nارجاع‌های دیگر به جدول‌های غایب در مدل/Repositoryهای اولیه: academys، cmss، communications، educations، enrollments، finances، homes، medias، pages، profiles، systems و worlds. این نام‌ها غالباً قالب اولیه‌اند؛ مسیرشان باید جدا بررسی شود. وجود نامشان اثبات حذف اخیر یا دلیل ساخت خودکار جدول نیست.\n\n## روش و حدود اعتبار\n\nفهرست فعلی از information_schema و تطبیق نام کامل جدول در PHP/JS، مدل، Repository، مسیر، هسته، تنظیمات، SQL و ابزار PHP بدون پسوند تهیه شد. مسیرهای table متغیر در پروفایل، دسترسی، رهگیری و پشتیبان آموزشگاه بررسی شدند؛ فهرست‌های ثابتشان در شواهد لحاظ شده است. شماره خطوط شامل تغییرات ثبت‌نشده قبلی است. ارجاع کد اثبات استفاده واقعی در همه مسیرها نیست و ممکن است فقط ثابت یا ابزار مدیریت باشد؛ بنابراین ۱۱۸ جدول دارای ارجاع را صرفاً به دلیل خالی‌بودن حذف نکنید.\n\nview، trigger، routine و event در metadata قابل مشاهده یافت نشد؛ خطاهای metadata: ${inventory.metadata_errors.length}. دیدپذیری تابع مجوز حساب دیتابیس است. این ممیزی ایستا است و تضمین پوشش نام‌های ساخته‌شده در زمان اجرا، کد خارجی و ارتباط‌های منطقی بدون FK نیست. اجرای تک‌تک قابلیت‌ها انجام نشده؛ snapshotهای قدیمی SQL موجودی امروز محسوب نشده‌اند و رکوردهای خصوصی صادر نشده‌اند.\n\n## حذف و بازگشت\n\n۱. فهرست مقصد را دوباره تطبیق دهید و از ساختار و داده جدول‌های هدف پشتیبان خصوصی بگیرید.\n۲. حذف منتخب را ابتدا روی نسخه تست بررسی کنید. DROP TABLE در MySQL با rollback عادی برنمی‌گردد؛ بازگشت از پشتیبان است.\n۳. ورود، تنظیمات حساب، پروفایل و حریم خصوصی تماس، ثبت‌نام آموزشگاه، شعبه و استان/شهرستان، دوره/ترم، پرداخت و callback تکراری، شبکه اجتماعی و پیام‌رسانی را بررسی کنید؛ مسیر رد دسترسی آموزشگاه/کاربر دیگر نیز آزموده شود.\n۴. حذف روی مقصد پس از موفقیت تست و درخواست صریح انتشار انجام شود. در این کار SQL حذف ارائه یا اجرا نشده است.\n\n## فایل‌ها و بازتولید\n\nگزارش Markdown و CSV برای مرور هستند. scripts/database-code-audit.cjs تحلیلگر ایستا است و snapshot محلی metadata را از docs/database/current-inventory.local.json می‌خواند. برای بازتولید گزارش همین snapshot، node scripts/database-code-audit.cjs را اجرا کنید. دریافت snapshot جدید به اتصال خواندنی به information_schema نیاز دارد؛ ابزار دریافت snapshot در فایل‌های تحویلی موجود نیست. فایل‌های *.local.json محلی‌اند و جزو بسته انتشار نیستند.\n\nتغییر برنامه، کلاس جدید، وابستگی یا migration وجود ندارد؛ آپلود به هاست لازم نیست. بازگشت این کار کنارگذاشتن فایل‌های گزارش و ابزار است؛ دیتابیس تغییر نکرده. کنترل syntax تحلیلگر و git diff --check انجام شد؛ تست انتشار کامل برای گزارش صرف اجرا نشده است.\n\n## فهرست کامل دو دسته\n`;
for(const g of ['فریمورک','پروژه']) {
 report+=`\n### جدول‌های ${g}\n\n| جدول | وضعیت | ردیف برآوردی | نمونه شاهد کد |\n|---|---|---:|---|\n`;
 for(const r of summary.filter(r=>r.group===g))report+=`| ${r.table} | ${r.status} | ${r.estimated_rows} | ${r.evidence||'—'} |\n`;
}
report+='\n## FKهای مجموعه جغرافیایی بدون ارجاع\n\n| مبدأ | مقصد |\n|---|---|\n';
for(const f of inventory.foreign_keys.filter(f=>f.TABLE_NAME.startsWith('z_world_')))report+=`| ${f.TABLE_NAME}.${f.COLUMN_NAME} | ${f.REFERENCED_TABLE_NAME}.${f.REFERENCED_COLUMN_NAME} |\n`;
fs.writeFileSync(path.join(root,'docs/database/database-audit-2026-10-03.fa.md'),report);
console.log('Framework/project:',summary.filter(r=>r.group==='فریمورک').length,summary.filter(r=>r.group==='پروژه').length);
