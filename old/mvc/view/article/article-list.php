<?
$settings = $data['settings'];
$categories = setIndexforDataArray($data['categories'], 'variable_name');
$articles = $data['posts'];
$settings_items = setIndexforDataArray($data['settings'], 'variable_name');
;

// dump($articles[0]);
// exit();
?>

<section class="article-list">
    <div>
        <h1><?= translate($settings_items, 'article_list_title') ?></h1>
        <p><?= translate($settings_items, 'article_list_description') ?></p>
    </div>
    <br>
    <section>
        <div>
            <h3><?= translate($settings_items, 'article_list_categories_title') ?></h3>
            <div>
                <? foreach($categories as $key => $category) { ?>
                    <a href="<?= $category['url'] ?>" class="<?= $category['title'] == 'Iranian Music' ? 'active' : '' ?>" data-filter="all"><?= translate($categories, $key) ?></a>
                <? } ?>
            </div>
        </div>
        <ul>
            <? foreach($articles as $key => $article) { ?>
                <li data-categories="irani radif">
                    <div>
                        <? if($article['cover']) { ?>
                            <img src="<?= get_article_thumbnail_source($article['cover'])?>" alt="<?= $article['title'] ?>">
                            <!-- <img src="<?//= get_article_thumbnail_source($article['cover'], 'webp')?>" alt="<?//= $article['title'] ?>"> --> 
                        <? } ?>
                    </div>
                    <div>
                        <h2>
                            <a href="<?=baseUrl()?>/article/articleDetails/<?= $article['post_id'] ?>"><?= $article['title'] ?></a>
                        </h2>
                        <div>
                            <? foreach($categories as $category_key => $category_value) { ?>
                                <? if($article['categories'] && strhas($article['categories'], $category_value['setting_id'])) { ?>
                                    <span><?= translate($categories, $category_key) ?></span>
                                <? } ?>
                            <? } ?>
                        </div>
                        <p><?= $article['description'] ?></p>
                        <span><?= translate($settings_items, 'article_list_release_date') . $article['updated_at'] ?></span>
                    </div>
                </li>
            <? } ?>
        </ul>    
    </section>
    <!-- <div>
        <a href="#">قبلی</a>
        <a>۱</a>
        <span>...</span>
        <a>۳</a>
        <span>۴</span>
        <a>۵</a>
        <span>...</span>
        <a>۱۳</a>
        <a href="#">بعدی</a>
    </div> -->
</section>    




