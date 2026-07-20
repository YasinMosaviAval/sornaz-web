<?php

$categories = [
['id' => 1, 'title' => 'پیانو', 'icon' => '🎹', 'count' => 132],
['id' => 2, 'title' => 'گیتار', 'icon' => '🎸', 'count' => 214],
['id' => 3, 'title' => 'ویولن', 'icon' => '🎻', 'count' => 87],
['id' => 4, 'title' => 'دف',    'icon' => '🥁', 'count' => 65],
['id' => 5, 'title' => 'تار',   'icon' => '🎼', 'count' => 44],
['id' => 6, 'title' => 'سنتور', 'icon' => '🎵', 'count' => 52],
['id' => 7, 'title' => 'فلوت',  'icon' => '🎶', 'count' => 28],
['id' => 8, 'title' => 'درام',  'icon' => '🥁', 'count' => 39]
];
?>

<section class="categories">
    <div class="container">
        <div class="section-header">
            <h2>دسته بندی سازها</h2>
            <p>آموزشگاه های هر ساز را مشاهده کنید.</p>
        </div>
        <div class="categories-grid">
            <?php foreach($categories as $item): ?>
                <a href="/academy?instrument=<?=$item['id']?>" class="category-card">
                    <div class="category-icon"><?=$item['icon']?></div>
                    <h3><?=$item['title']?></h3>
                    <span><?=$item['count']?> آموزشگاه</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>