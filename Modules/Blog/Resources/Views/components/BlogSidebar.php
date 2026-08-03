<aside class="blog-sidebar">
    <section class="widget">
        <h3>دسته‌بندی‌ها</h3>
        <ul>
            <?php foreach($categories as $category): ?>
                <li>
                    <a href="/blog/category/<?=e($category->slug)?>">
                        <?=e($category->title())?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="widget">
        <h3>آخرین مطالب</h3>
        <ul>
            <?php foreach($latestPosts as $post): ?>
                <li>
                    <a href="/blog/<?=e($post->slug)?>">
                        <?=e($post->title())?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="widget">
        <h3>محبوب‌ترین مطالب</h3>
        <ul>
            <?php foreach($popularPosts as $post): ?>
                <li>
                    <a href="/blog/<?=e($post->slug)?>">
                        <?=e($post->title())?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</aside>