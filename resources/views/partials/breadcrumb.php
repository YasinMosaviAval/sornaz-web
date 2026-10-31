<?php
// $breadcrumbItems: ordered arrays containing label and optional local href.
$breadcrumbItems=$breadcrumbItems??[];
?>
<nav class="flex flex-wrap items-center gap-2 text-sm text-gray-400 mb-8" aria-label="<?= locale()==='en'?'Breadcrumb':'مسیر صفحه' ?>" data-site-breadcrumb>
    <div class="contents" data-breadcrumb-items>
        <?php foreach($breadcrumbItems as $index=>$crumb): ?>
            <?php if($index): ?><span class="text-gray-300" aria-hidden="true"><?= locale()==='en'?'⟶':'⟵' ?></span><?php endif; ?>
            <?php if(isset($crumb['href'])): ?><a href="<?= e($crumb['href']) ?>" class="hover:text-indigo-600"><?= e($crumb['label']) ?></a>
            <?php else: ?><span class="text-gray-600 line-clamp-1" aria-current="page" data-dynamic-content><?= e($crumb['label']) ?></span><?php endif; ?>
        <?php endforeach; ?>
    </div>
    <template data-breadcrumb-link><a class="hover:text-indigo-600"></a></template>
    <template data-breadcrumb-current><span class="text-gray-600 line-clamp-1" aria-current="page"></span></template>
    <template data-breadcrumb-separator><span class="text-gray-300" aria-hidden="true"><?= locale()==='en'?'⟶':'⟵' ?></span></template>
</nav>
