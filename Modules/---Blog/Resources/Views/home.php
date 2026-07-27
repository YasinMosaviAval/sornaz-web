<h1>Blog</h1>
<?php foreach ($posts as $post): ?>
<div>
    <h2><?= e($post->title()) ?></h2>
    <a href="/blog/<?= e($post->slug) ?>">ادامه مطلب</a>
</div>
<?php endforeach; ?>