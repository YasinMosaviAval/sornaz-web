<?php $isEnglish = locale() === 'en'; ?>
<style>
    .article-body p { margin-bottom: 1.25rem; }
    .article-body h2 { margin: 2.5rem 0 1rem; font-size: 1.5rem; font-weight: 700; color: #111827; }
    .article-body h3 { margin: 2rem 0 .75rem; font-size: 1.25rem; font-weight: 700; color: #111827; }
    .article-body ul, .article-body ol { margin: 1rem 1.25rem 1.25rem; }
    .article-body li { margin-bottom: .35rem; }
    .article-body a, .comment-html a { color: #4f46e5; }
    .comment-html { font-weight: 400 !important; }
    .article-body a:hover { text-decoration: underline; }
    .article-body img { width: auto; max-width: none; height: auto; border-radius: 1rem; margin: 1.5rem auto; }
    .article-body blockquote { border-right: 4px solid #c7d2fe; background: #f8fafc; padding: 1rem 1.25rem; margin: 1.5rem 0; border-radius: 0 1rem 1rem 0; color: #475569; }
</style>

<div id="page-article-detail">
    <div class="max-w-6xl mx-auto px-4 py-10 md:py-14">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-400 mb-8">
            <a href="/" class="hover:text-indigo-600"><?= $isEnglish ? 'Home' : 'خانه' ?></a>
            <span class="text-gray-300">⟵</span>
            <a href="/analytics/articles" class="hover:text-indigo-600"><?= $isEnglish ? 'Educational articles' : 'مقاله‌های آموزشی' ?></a>
            <span class="text-gray-300">⟵</span>
            <span class="text-gray-600 line-clamp-1" data-dynamic-content><?= htmlspecialchars($article['title']) ?></span>
        </nav>

        <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-6" data-dynamic-content><?= htmlspecialchars($article['title']) ?></h1>

        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-500 mb-8 pb-6 border-b border-gray-100">
            <?php if ($article['author_name']): ?><span><i class="far fa-user text-indigo-400 ml-1"></i><span data-dynamic-content><?= htmlspecialchars($article['author_name']) ?></span></span><?php endif; ?>
            <span><i class="far fa-calendar text-indigo-400 ml-1"></i><?= $isEnglish ? 'Published:' : 'تاریخ انتشار:' ?> <strong class="font-medium text-gray-700" data-dynamic-content><?= htmlspecialchars((string)($article['published_at'] ?? '—')) ?></strong></span>
            <?php if ($article['updated_at']): ?><span><i class="far fa-clock text-indigo-400 ml-1"></i><?= $isEnglish ? 'Updated:' : 'آخرین به‌روزرسانی:' ?> <strong class="font-medium text-gray-700" data-dynamic-content><?= htmlspecialchars($article['updated_at']) ?></strong></span><?php endif; ?>
            <span><i class="far fa-eye text-indigo-400 ml-1"></i><strong class="font-medium text-gray-700" data-dynamic-content><?= (int)$article['views'] ?></strong> <?= $isEnglish ? 'views' : 'بازدید' ?></span>
        </div>

        <?php if ($article['cover']): ?>
            <img src="<?= htmlspecialchars($article['cover']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="mx-auto h-auto max-w-full rounded-xl mb-8 object-contain" data-dynamic-content>
        <?php endif; ?>

        <?php if ($article['categories']): ?>
            <div class="flex flex-wrap gap-2 mb-8" data-dynamic-content>
                <?php foreach ($article['categories'] as $category): ?><span class="px-3 py-1.5 rounded-xl text-xs bg-indigo-50 text-indigo-700"><?= htmlspecialchars($category) ?></span><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($article['summary']): ?><div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 md:p-6 mb-8 text-indigo-900 leading-relaxed" data-dynamic-content><?= nl2br(htmlspecialchars($article['summary'])) ?></div><?php endif; ?>
        <?php if ($article['description']): ?><div class="text-gray-600 leading-8 mb-8" data-dynamic-content><?= nl2br(htmlspecialchars($article['description'])) ?></div><?php endif; ?>

        <article class="article-body prose prose-lg max-w-none text-gray-700 leading-8 text-justify" data-dynamic-content>
            <?= $article['content'] !== '' ? $article['content'] : '<p class="text-gray-400">'.($isEnglish?'No content has been added.':'محتوایی برای این نوشته ثبت نشده است.').'</p>' ?>
        </article>

        <section class="mt-14 border-t border-gray-100 pt-10" id="articleComments" dir="rtl">
            <h2 class="text-2xl font-bold mb-6">نظرات کاربران</h2>
            <div class="space-y-5 mb-10">
                <?php if (empty($comments)): ?><p class="rounded-2xl bg-gray-50 p-5 text-gray-500">هنوز نظری برای این مقاله ثبت نشده است.</p><?php endif; ?>
                <?php foreach (($comments ?? []) as $comment): $isReply=!empty($comment['parent']); ?><article class="rounded-2xl border border-gray-100 bg-white p-5 <?= $isReply ? 'mr-8 md:mr-16 w-[calc(100%-2rem)] md:w-[calc(100%-4rem)]' : '' ?>"><div class="flex items-center justify-between gap-3 mb-3"><strong><?= htmlspecialchars($comment['author']) ?></strong><time class="text-xs text-gray-400"><?= htmlspecialchars((string)$comment['created_at']) ?></time></div><div class="text-gray-600 leading-8 comment-html"><?= $comment['content'] ?></div></article><?php endforeach; ?>
            </div>
            <div class="rounded-3xl bg-gray-50 p-6 md:p-8">
                <h3 class="text-xl font-bold mb-5">ارسال نظر جدید</h3>
                <form id="publicCommentForm" class="space-y-4"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>"><?php $viewer=auth()->user(); if(!$viewer): ?><div class="grid md:grid-cols-2 gap-4"><label class="block text-sm">نام و نام خانوادگی<input name="author" placeholder="نام و نام خانوادگی" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3"></label><label class="block text-sm">ایمیل<input name="author_email" type="email" placeholder="ایمیل" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3"></label></div><?php endif; ?><label class="block text-sm">نظر شما<textarea name="content" rows="6" placeholder="نظر خود را بنویسید ..." class="mt-2 w-full resize-y rounded-2xl border border-gray-200 bg-white px-4 py-3 outline-none"></textarea></label><div class="flex justify-end"><button type="submit" class="rounded-2xl bg-indigo-600 px-6 py-3 font-medium text-white hover:bg-indigo-700">ارسال نظر</button></div><p id="commentFormMessage" class="text-sm"></p></form>
            </div>
        </section>

        <?php if (!empty($article['content_images'])): ?><div class="mt-8 space-y-6"><?php foreach ($article['content_images'] as $image): ?><figure><img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="h-auto max-w-none rounded-xl shadow-sm" loading="lazy"></figure><?php endforeach; ?></div><?php endif; ?>

        <div class="mt-12 pt-6 border-t">
            <a href="/analytics/articles" class="inline-flex items-center gap-2 text-indigo-600 hover:underline"><i class="fas fa-arrow-right"></i><?= $isEnglish ? 'Back to articles' : 'بازگشت به مقاله‌ها' ?></a>
        </div>
        <?php if (!empty($article['related_posts'])): ?><section class="mt-12 border-t border-gray-100 pt-8"><h2 class="text-2xl font-bold mb-6"><?= $isEnglish ? 'Related articles' : 'مقالات مرتبط' ?></h2><div class="grid grid-cols-1 gap-[30px] md:grid-cols-2 lg:grid-cols-3"><?php foreach($article['related_posts'] as $related): ?><article class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition border border-gray-50 flex flex-col items-center gap-[30px] p-[30px]"><?php if($related['thumbnail']): ?><a href="/analytics/article-details?id=<?= (int)$related['id'] ?>" class="block w-full"><img src="<?= htmlspecialchars($related['thumbnail']) ?>" alt="<?= htmlspecialchars($related['title']) ?>" class="block w-full aspect-[16/9] object-cover rounded-xl" loading="lazy"></a><?php endif; ?><div class="flex w-full flex-1 flex-col"><?php if(!empty($related['categories'])): ?><div class="mb-3 flex flex-wrap justify-center gap-2"><?php foreach($related['categories'] as $category): ?><span class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs text-indigo-700"><?= htmlspecialchars($category) ?></span><?php endforeach; ?></div><?php endif; ?><a href="/analytics/article-details?id=<?= (int)$related['id'] ?>"><h3 class="text-center text-xl font-bold mb-3 hover:text-indigo-600"><?= htmlspecialchars($related['title']) ?></h3></a><?php if($related['summary']): ?><p class="text-center text-gray-600 text-sm leading-relaxed line-clamp-3"><?= htmlspecialchars($related['summary']) ?></p><?php endif; ?><div class="mt-auto flex items-center justify-between pt-3 text-xs text-gray-400" dir="ltr"><span><i class="far fa-calendar ml-1"></i><?= htmlspecialchars((string)($related['published_at']??'—')) ?></span></div></div></article><?php endforeach; ?></div></section><?php endif; ?>
    </div>
</div>

<style>[data-dynamic-content][data-page-content-key], [data-dynamic-content] [data-page-content-key] { outline: none !important; cursor: default !important; } .comment-editor:empty:before { content: attr(data-placeholder); color: #9ca3af; }</style>
<script>
document.addEventListener('click', function (event) {
    if (new URLSearchParams(location.search).get('cms') === '1' && event.target.closest('[data-dynamic-content]')) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);
</script>
<script>
document.getElementById('publicCommentForm')?.addEventListener('submit',async function(e){e.preventDefault();const m=document.getElementById('commentFormMessage'),b=this.querySelector('button[type="submit"]');if(!this.elements.content.value.trim()){m.textContent='متن نظر الزامی است.';m.className='text-sm text-red-600';return;}b.disabled=true;try{const data=new FormData(this),r=await fetch('/analytics/article-comments/<?= (int)($article['id']??0) ?>',{method:'POST',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':data.get('_token')||''},body:data}),payload=await r.json(),j=payload.data||payload;if(!r.ok||!j.success)throw new Error(j.message||'ارسال نظر ناموفق بود.');m.textContent=j.message;m.className='text-sm text-emerald-600';this.reset();}catch(x){m.textContent=x.message;m.className='text-sm text-red-600';}finally{b.disabled=false;}});
</script>
