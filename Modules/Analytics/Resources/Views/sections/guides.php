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
</section>
<script>
window.openGuideEditor=function(g){const f=(l)=>g[l]||{};document.getElementById('modalContainer').innerHTML=`<div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4" dir="rtl"><form class="w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white p-6" onsubmit="return saveGuide(event,'${g.key}')"><h2 class="text-xl font-bold mb-4">ویرایش ${g.title}</h2><div class="grid md:grid-cols-2 gap-4"><div><label>عنوان فارسی</label><input id="guideFaTitle" class="w-full border rounded-xl p-3 mt-1" value="${escapeHtml(f('fa').title||g.title)}"><label class="block mt-3">متن فارسی</label><textarea id="guideFaContent" rows="20" class="w-full border rounded-xl p-3 mt-1">${escapeHtml(f('fa').content||g.content)}</textarea></div><div dir="ltr"><label>English title</label><input id="guideEnTitle" class="w-full border rounded-xl p-3 mt-1" value="${escapeHtml(f('en').title||g.title)}"><label class="block mt-3">English content</label><textarea id="guideEnContent" rows="20" class="w-full border rounded-xl p-3 mt-1">${escapeHtml(f('en').content||g.content)}</textarea></div></div><div class="flex gap-3 mt-5"><button class="bg-indigo-600 text-white rounded-xl px-5 py-2">ذخیره</button><button type="button" onclick="closeModal()" class="border rounded-xl px-5 py-2">انصراف</button></div></form></div>`};
window.escapeHtml=function(v){const d=document.createElement('div');d.textContent=v??'';return d.innerHTML};window.saveGuide=async function(e,key){e.preventDefault();const d={key,fa:{title:guideFaTitle.value,content:guideFaContent.value},en:{title:guideEnTitle.value,content:guideEnContent.value}};const r=await fetch('/analytics/admin-guides',{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({_token:window.adminCsrfToken,payload_b64:btoa(unescape(encodeURIComponent(JSON.stringify(d))))})});const j=await r.json();if(!j.success){alert(j.message||'ذخیره ناموفق بود');return false;}location.reload();return false;};
</script>
