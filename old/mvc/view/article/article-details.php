<?
$article = $data['article'][0] ?? [];
$comments = $data['comments'] ?? [];
$settings = setIndexforDataArray($data['settings'], 'variable_name') ?? [];
$categories = setIndexforDataArray($data['categories'], 'variable_name') ?? [];
// $related_articles = array()
// if(sizeof($data['related_articles']) > 1) {
    $related_articles = $data['related_articles'] ?? [];
// }

// dump($related_articles);
// dump($article);
// dump($data);
// exit();
?>

<section class="article-details">
    <div>
        <h1><?= $article['title'] ?></h1>
        <p>
            <a href="<?= $settings['application_name']['url'] ?>"><?= translate($settings, 'application_name') ?></a>
            <span> ==> </span>
            <a href="<?= $settings['header_link_1']['url'] ?>"><?= translate($settings, 'header_link_1') ?></a>
        </p>
    </div>

    <br>

    <article>
        <? if($article['cover']) { ?>
            <img src="<?= get_article_origin_source($article['cover']) ?>" alt="<?= $article['title'] ?>">
            <!-- <img src="<?//= get_article_origin_source($article['cover'], $type='webp') ?>" alt="<?//= $article['title'] ?>"> -->
        <? } ?>

        <span><?= translate($settings, 'article_list_release_date') . $article['created_at'] ?></span>
        <br>
        <span><?= translate($settings, 'article_list_modified_date') . $article['updated_at'] ?></span>
        <br>
        <a href="<?=baseUrl()?>/admin/editArticle/<?= $article['post_id'] ?>"><?= translate($settings, 'tables_action_edit') ?></a>

        <div class="article-body"><?= $article['content'] ?></div>

        <br>

        <div class="article-tags">
            <? foreach($categories as $category_key => $category_value) { ?>
                <? if($article['categories'] && strhas($article['categories'], $category_value['setting_id'])) { ?>
                    <a href="#"><?= translate($categories, $category_key) ?></a>
                <? } ?>
            <? } ?>
        </div>

        <br>
        <br>

        <? if(sizeof($related_articles)) { ?>
            <section class="related-articles">
                <h2><?= translate($settings, 'article_details_related_articles') ?></h2>
                <div>
                    <? foreach($related_articles as $key => $related_article) { ?>
                        <div>
                            <div>
                                <img src="<?= get_article_thumbnail_source($related_article['cover']) ?>" alt="<?= $related_article['title'] ?>">
                                <!-- <img src="<?//= get_article_cover_source($related_article['cover'], $type='webp') ?>" alt="<?//= $related_article['title'] ?>"> -->
                            </div>
                            <div>
                                <h3>
                                    <a href="/concept-piece-iranian-music"><?= $related_article['title'] ?></a>
                                </h3>
                                <p><?= $related_article['description'] ?></p>
                                <div>
                                    <? foreach($categories as $category_key => $category_value) { ?>
                                        <?// if(strhas($related_article['categories'], $category_value['setting_id'])) { ?>
                                            <span><?= translate($categories, $category_key) ?></span>
                                        <? //} ?>
                                    <? } ?>
                                </div>
                                <div>
                                    <span><?= translate($settings, 'article_list_modified_date') . $related_article['updated_at'] ?></span>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                </div>
            </section>
        <? } ?>

        <br>
        <br>
        
        <a href="<?= baseUrl() ?>/article/articleList"><?= translate($settings, 'article_details_back_to_articles_list') ?></a>
        
        <br>
        <br>

        <section class="comments-section">
            <h2><?= translate($settings, 'article_details_users_comments') ?></h2>
            <div>
                <? foreach($comments as $key => $value) { ?>
                    <div class="<?= $value['parent'] == 0 ? '' : 'comment-reply' ?>">
                        <div>
                            <span><?= $value['author'] ?></span>
                            <span><?= $value['date'] ?></span>
                        </div>
                        <p><?= $value['content'] ?></p>
                        <? if($value['has_response'] == 0 && session_get('user_id') != $value['user_id'] ) { ?>
                            <button><?= translate($settings, 'article_details_reply') ?></button>
                        <? } ?>
                    </div>
                <? } ?>
                <? if(sizeof($comments) == 0) { ?>
                    <p><?= translate($settings, 'article_details_no_comment') ?></p>
                <? } ?>
            </div>
            <br>
            <h3><?= translate($settings, 'article_details_header') ?></h3>
            <form id="commentForm">

            
                <? if(isNotUser()) { ?>
                    <div>
                        <label for="name"><?= translate($settings, 'article_details_name_label') ?> <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="<?= translate($settings, 'article_details_name_placeholder') ?>">
                    </div>
                    <div>
                        <label for="email"><?= translate($settings, 'article_details_email_label') ?> <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="<?= translate($settings, 'article_details_email_placeholder') ?>">
                    </div>
                <? } ?>

                <div>
                    <label for="comment"><?= translate($settings, 'article_details_message_label') ?> <span class="required">*</span></label>
                    <textarea id="comment" name="comment" rows="6" required placeholder="<?= translate($settings, 'article_details_message_placeholder') ?>"></textarea>
                </div>
                <button type="submit"><?= translate($settings, 'article_details_send_button') ?></button>
                <p><?= translate($settings, 'article_details_note') ?></p>
            </form>
        </section>
    </article>
</section>

