<?php

$current = current_url();
$menu = [
    ['title'=>'خانه', 'url'=>'/'],
    ['title'=>'آموزشگاه ها', 'url'=>'/academy'],
    ['title'=>'اساتید', 'url'=>'/teacher'],
    ['title'=>'دوره ها', 'url'=>'/course'],
    ['title'=>'مقالات', 'url'=>'/blog'],
    ['title'=>'درباره ما', 'url'=>'/about'],
    ['title'=>'تماس', 'url'=>'/contact']
];

?>

<header class="site-header">
    <div class="container">
        <div class="header-left">
            <a href="/" class="logo">
                <img src="/assets/images/logo.svg" alt="Sornaz">
                <span>Sornaz</span>
            </a>
        </div>
        <nav class="desktop-menu">
            <?php foreach($menu as $item): ?>
                <a href="<?=$item['url']?>" class="<?=$current==$item['url']?'active':''?>"><?=$item['title']?></a>
            <?php endforeach; ?>
        </nav>
        <div class="header-right">
            <button class="search-btn">
                <i class="fa fa-search"></i>
            </button>
            <button class="favorite-btn">
                <i class="fa fa-heart"></i>
            </button>
            <a href="/login" class="login-btn">ورود</a>
            <a href="/register" class="register-btn">ثبت نام</a>
            <button class="mobile-menu-btn">☰</button>
        </div>
    </div>
</header>