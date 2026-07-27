<?php
/** @var \Modules\Blog\DTO\BlogDTO $post */
$title = $post->title();
$excerpt = $post->excerpt();
$image = $post->cover();
?>

<article class="blog-card">
    <a class="blog-card-image" href="/blog/<?=e($post->slug)?>">
        <img src="<?=e($image ?: '/assets/images/no-image.webp')?>" alt="<?=e($title)?>">
    </a>
    <div class="blog-card-body">
        <div class="blog-card-meta">
            <span><?=e($post->author())?></span>
            <span>•</span>
            <span><?=e($post->publishedDate())?></span>
            <span>•</span>
            <span><?=number_format($post->views_count)?> بازدید</span>
        </div>
        <h2>
            <a href="/blog/<?=e($post->slug)?>"><?=e($title)?></a>
        </h2>
        <p><?=e($excerpt)?></p>
        <a class="read-more" href="/blog/<?=e($post->slug)?>">ادامه مطلب</a>
    </div>
</article>