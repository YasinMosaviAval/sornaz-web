<?php if ((int)auth()->id() !== 1) return; ?>
<section id="guides" class="section hidden" dir="rtl">
    <div class="mb-6"><h1 class="text-3xl font-bold">راهنمای عملکردها</h1><p class="mt-2 text-gray-500">مستندات ثبت‌نام کاربر، آموزشگاه و شعبه اصلی</p></div>
    <div class="grid gap-6 xl:grid-cols-3">
    <?php foreach(($guides??[]) as $guide): ?>
        <article class="rounded-3xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-slate-900 dark:border-slate-700">
            <h2 class="text-xl font-bold mb-4"><?= e($guide['title']) ?></h2>
            <div class="prose prose-sm max-w-none max-h-[32rem] overflow-y-auto whitespace-pre-wrap leading-7 text-gray-600 dark:text-slate-300"><?= e($guide['content']) ?></div>
            <button type="button" class="mt-5 rounded-xl bg-indigo-600 px-4 py-2 text-white" onclick='openGuideEditor(<?= json_encode($guide,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?>)'>ویرایش راهنما</button>
        </article>
    <?php endforeach; ?>
    </div>
    <div class="guide-library mt-10 rounded-[2rem] border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-5 shadow-sm dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-indigo-950 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200"><i class="fas fa-book-open"></i> کتابخانهٔ راهنما</div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">فایل‌های راهنما و مستندات پروژه</h2>
                <p class="mt-2 text-sm leading-7 text-slate-600 dark:text-slate-300">برای دیدن متن کامل و قالب‌بندی‌شده، کارت هر فایل را باز کنید.</p>
            </div>
            <span class="rounded-2xl border border-indigo-200 bg-white px-4 py-2 text-sm font-bold text-indigo-700 shadow-sm dark:border-indigo-500/30 dark:bg-slate-800 dark:text-indigo-200"><?= count($guideDocuments ?? []) ?> فایل Markdown</span>
        </div>
        <label for="guideDocumentSearch" class="mt-6 block text-sm font-semibold text-slate-700 dark:text-slate-200">جست‌وجو در عنوان و مسیر فایل‌ها</label>
        <div class="relative mt-2">
            <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
            <input id="guideDocumentSearch" type="search" class="w-full rounded-2xl border border-indigo-100 bg-white py-3 pr-11 pl-4 text-slate-800 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 dark:border-slate-600 dark:bg-slate-800 dark:text-white" placeholder="مثلاً امنیت، انتشار، آموزشگاه یا maintenance" oninput="filterGuideDocuments(this.value)">
        </div>
        <?php if (empty($guideDocuments)): ?>
        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-7 text-amber-900 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-200">هیچ فایل Markdown روی این سرور پیدا نشد. فایل‌های <code>docs/**/*.md</code> و راهنماهای <code>Modules/**/*.md</code> را همراه نسخهٔ سایت بارگذاری کنید و دسترسی مستقیم وب به پوشه‌های <code>docs</code> و <code>Modules</code> را ببندید.</div>
        <?php endif; ?>
        <?php foreach (['docs/' => ['مستندات و راهنماهای سایت', 'fas fa-folder-open'], 'Modules/' => ['راهنماهای ماژول‌ها', 'fas fa-puzzle-piece']] as $prefix => [$groupTitle, $icon]): ?>
        <?php $groupDocuments = array_values(array_filter($guideDocuments ?? [], static fn (array $item): bool => str_starts_with($item['path'], $prefix))); ?>
        <?php if ($groupDocuments): ?>
        <div class="guide-document-group mt-8">
            <h3 class="mb-4 flex items-center gap-3 text-lg font-bold text-slate-800 dark:text-slate-100"><span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300"><i class="<?= e($icon) ?>"></i></span><?= e($groupTitle) ?><span class="rounded-full bg-white px-2.5 py-1 text-xs text-slate-500 dark:bg-slate-800 dark:text-slate-300"><?= count($groupDocuments) ?></span></h3>
            <div class="grid items-start gap-4 lg:grid-cols-2">
            <?php foreach ($groupDocuments as $index => $document): ?>
                <details class="guide-document-card group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-indigo-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800 dark:hover:border-indigo-500" data-guide-search="<?= e(mb_strtolower($document['title'] . ' ' . $document['path'])) ?>">
                    <summary class="flex cursor-pointer list-none items-center gap-4 p-5 marker:hidden">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl <?= $prefix === 'docs/' ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300' : 'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-300' ?>"><i class="fas fa-file-alt"></i></span>
                        <span class="min-w-0 flex-1"><span class="block text-base font-bold leading-7 text-slate-900 dark:text-white"><?= e($document['title']) ?></span><span class="mt-1 block truncate text-xs text-slate-500 dark:text-slate-400" dir="ltr"><?= e($document['path']) ?></span></span>
                        <span class="guide-document-chevron text-indigo-500 transition-transform"><i class="fas fa-chevron-down"></i></span>
                    </summary>
                    <div class="border-t border-slate-100 px-5 pb-5 pt-5 dark:border-slate-700">
                        <div class="guide-md rounded-xl bg-slate-50 p-4 text-sm leading-8 text-slate-700 dark:bg-slate-900 dark:text-slate-200 md:p-6" dir="auto"><?= $document['html'] ?></div>
                        <button type="button" class="guide-document-close mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-100 px-4 py-2 text-sm font-bold text-indigo-700 hover:bg-indigo-200" onclick="closeGuideDocument(this)"><i class="fas fa-chevron-up"></i> بستن راهنما</button>
                    </div>
                </details>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <p id="guideDocumentEmpty" class="mt-6 hidden rounded-2xl border border-dashed border-indigo-200 bg-white p-6 text-center text-slate-500 dark:border-slate-600 dark:bg-slate-800">فایلی با این عنوان یا مسیر پیدا نشد.</p>
    </div>
</section>
<style>
#guides .guide-document-card[open] .guide-document-chevron{transform:rotate(180deg)}
#guides .guide-document-card[open]{grid-column:1/-1}
#guides .guide-document-card .guide-md{max-height:min(36rem,55vh);overflow:auto;overscroll-behavior:contain}
#guides .guide-document-close{background:var(--theme-100);color:var(--theme-700)}
#guides .guide-document-close:hover{background:var(--theme-200)}
#guides .guide-md h1,#guides .guide-md h2,#guides .guide-md h3,#guides .guide-md h4{font-weight:800;line-height:1.6;color:var(--theme-900);margin:1.4rem 0 .6rem}
#guides .guide-md h1{font-size:1.5rem;border-bottom:2px solid var(--theme-200);padding-bottom:.5rem;margin-top:0}
#guides .guide-md h2{font-size:1.25rem;border-right:4px solid var(--theme-500);padding-right:.75rem}
#guides .guide-md h3{font-size:1.1rem;color:#0f766e}
#guides .guide-md p{margin:.65rem 0;line-height:2}
#guides .guide-md ul,#guides .guide-md ol{padding-right:1.7rem;margin:.7rem 0}
#guides .guide-md ul{list-style:disc}#guides .guide-md ol{list-style:decimal}
#guides .guide-md li{padding:.18rem 0;line-height:1.9}
#guides .guide-md li::marker{color:var(--theme-500)}
#guides .guide-md blockquote{border-right:4px solid #14b8a6;background:#f0fdfa;padding:.6rem 1rem;border-radius:.7rem;margin:1rem 0}
#guides .guide-md pre{background:#0f172a;color:#e2e8f0;border-radius:.85rem;padding:1rem;overflow:auto;direction:ltr;text-align:left;line-height:1.7}
#guides .guide-md code{font-family:Consolas,monospace;background:var(--theme-100);color:var(--theme-900);border-radius:.4rem;padding:.1rem .35rem;direction:ltr;unicode-bidi:isolate}
#guides .guide-md pre code{background:transparent;color:inherit;padding:0}
#guides .guide-md a{color:var(--theme-700);text-decoration:underline;text-underline-offset:3px;overflow-wrap:anywhere}
#guides .guide-md strong{color:var(--theme-900)}
#guides .guide-md hr{border:0;border-top:1px solid #cbd5e1;margin:1.4rem 0}
#guides .guide-md-table-wrap{overflow:auto;margin:1rem 0;border:1px solid #cbd5e1;border-radius:.8rem}
#guides .guide-md table{border-collapse:collapse;min-width:100%;text-align:right}
#guides .guide-md td{padding:.65rem .85rem;border-bottom:1px solid #e2e8f0;border-left:1px solid #e2e8f0;vertical-align:top}
#guides .guide-md tr:first-child{background:var(--theme-100);font-weight:700}
html[data-mode="dark"] #guides .guide-library{background:linear-gradient(135deg,var(--surface-card),var(--surface),var(--theme-900));border-color:var(--border)}
html[data-mode="dark"] #guides .guide-document-card{background:var(--surface-card);border-color:var(--border)}
html[data-mode="dark"] #guides .guide-md{background:var(--surface-muted);color:var(--text)}
html[data-mode="dark"] #guides .guide-library .text-slate-900,html[data-mode="dark"] #guides .guide-library .text-slate-800,html[data-mode="dark"] #guides .guide-library .text-slate-700{color:var(--text)!important}
html[data-mode="dark"] #guides .guide-library .text-slate-600,html[data-mode="dark"] #guides .guide-library .text-slate-500{color:var(--text-muted)!important}
html[data-mode="dark"] #guides .guide-library .bg-slate-800,html[data-mode="dark"] #guides .guide-library .bg-slate-900{background:var(--surface-card)!important}
html[data-mode="dark"] #guides .guide-md h1,html[data-mode="dark"] #guides .guide-md h2,html[data-mode="dark"] #guides .guide-md h3,html[data-mode="dark"] #guides .guide-md h4{color:var(--theme-200)}
html[data-mode="dark"] #guides .guide-md strong{color:var(--text)}
html[data-mode="dark"] #guides .guide-md code{background:var(--theme-900);color:var(--theme-100)}
html[data-mode="dark"] #guides .guide-md pre code{background:transparent;color:inherit}
html[data-mode="dark"] #guides .guide-md blockquote{background:#134e4a;color:#ccfbf1}
html[data-mode="dark"] #guides .guide-md tr:first-child{background:var(--theme-900)}
html[data-mode="dark"] #guides .guide-md td{border-color:var(--border)}
</style>
<script>
window.filterGuideDocuments=function(query){const q=String(query||'').trim().toLocaleLowerCase();let visible=0;document.querySelectorAll('#guides .guide-document-card').forEach(card=>{const show=card.dataset.guideSearch.includes(q);card.hidden=!show;if(show)visible++});document.querySelectorAll('#guides .guide-document-group').forEach(group=>{group.hidden=!group.querySelector('.guide-document-card:not([hidden])')});document.getElementById('guideDocumentEmpty').classList.toggle('hidden',visible!==0)};
window.closeGuideDocument=function(button){const card=button.closest('details');if(!card)return;card.open=false;card.querySelector('summary')?.focus()};
document.querySelectorAll('#guides .guide-document-card').forEach(card=>card.addEventListener('toggle',()=>{if(!card.open)return;document.querySelectorAll('#guides .guide-document-card[open]').forEach(other=>{if(other!==card)other.open=false})}));
window.openGuideEditor=function(g){const f=(l)=>g[l]||{};document.getElementById('modalContainer').innerHTML=`<div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4" dir="rtl"><form class="w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6" onsubmit="return saveGuide(event,'${g.key}')"><h2 class="text-xl font-bold mb-4">ویرایش ${g.title}</h2><div class="grid md:grid-cols-2 gap-4"><div><label>عنوان فارسی</label><input id="guideFaTitle" class="w-full border rounded-xl p-3 mt-1" value="${escapeHtml(f('fa').title||g.title)}"><label class="block mt-3">متن فارسی</label><textarea id="guideFaContent" rows="20" class="w-full border rounded-xl p-3 mt-1">${escapeHtml(f('fa').content||g.content)}</textarea></div><div dir="ltr"><label>English title</label><input id="guideEnTitle" class="w-full border rounded-xl p-3 mt-1" value="${escapeHtml(f('en').title||g.title)}"><label class="block mt-3">English content</label><textarea id="guideEnContent" rows="20" class="w-full border rounded-xl p-3 mt-1">${escapeHtml(f('en').content||g.content)}</textarea></div></div><div class="flex gap-3 mt-5"><button class="bg-indigo-600 text-white rounded-xl px-5 py-2">ذخیره</button><button type="button" onclick="closeModal()" class="border rounded-xl px-5 py-2">انصراف</button></div></form></div>`};
window.escapeHtml=function(v){const d=document.createElement('div');d.textContent=v??'';return d.innerHTML};window.saveGuide=async function(e,key){e.preventDefault();const d={key,fa:{title:guideFaTitle.value,content:guideFaContent.value},en:{title:guideEnTitle.value,content:guideEnContent.value}};const r=await fetch('/analytics/admin-guides',{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({_token:window.adminCsrfToken,payload_b64:btoa(unescape(encodeURIComponent(JSON.stringify(d))))})});const j=await r.json();if(!j.success){alert(j.message||'ذخیره ناموفق بود');return false;}location.reload();return false;};
</script>
