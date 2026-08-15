<?php $isEnglish = locale() === 'en'; ?>
<style>
    .article-body p { margin-bottom: 1.25rem; }
    .article-body h2 { margin: 2.5rem 0 1rem; font-size: 1.5rem; font-weight: 700; color: #111827; }
    .article-body h3 { margin: 2rem 0 .75rem; font-size: 1.25rem; font-weight: 700; color: #111827; }
    .article-body ul, .article-body ol { margin: 1rem 1.25rem 1.25rem; }
    .article-body li { margin-bottom: .35rem; }
    .article-body a { color: #4f46e5; }
    .article-body a:hover { text-decoration: underline; }
    .article-body img { max-width: 100%; height: auto; border-radius: 1rem; margin: 1.5rem auto; }
    .article-body blockquote { border-right: 4px solid #c7d2fe; background: #f8fafc; padding: 1rem 1.25rem; margin: 1.5rem 0; border-radius: 0 1rem 1rem 0; color: #475569; }
</style>

<div id="page-article-detail">
    <div class="max-w-3xl mx-auto px-4 py-10 md:py-14">
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
            <img src="<?= htmlspecialchars($article['cover']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="w-full max-h-[480px] object-cover rounded-3xl mb-8" data-dynamic-content>
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

        <div class="mt-12 pt-6 border-t">
            <a href="/analytics/articles" class="inline-flex items-center gap-2 text-indigo-600 hover:underline"><i class="fas fa-arrow-right"></i><?= $isEnglish ? 'Back to articles' : 'بازگشت به مقاله‌ها' ?></a>
        </div>
    </div>
</div>

<style>[data-dynamic-content][data-page-content-key], [data-dynamic-content] [data-page-content-key] { outline: none !important; cursor: default !important; }</style>
<script>
document.addEventListener('click', function (event) {
    if (new URLSearchParams(location.search).get('cms') === '1' && event.target.closest('[data-dynamic-content]')) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);
</script>
