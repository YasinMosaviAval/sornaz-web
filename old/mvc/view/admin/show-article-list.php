<?
$my_articles = $data['my-articles'];
$all_articles = $data['all-articles'];
$users = $data['users'];
// dump($users);

$shown_articles = $data['all-articles'];
$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');

// $my_articles = [];
// foreach($all_articles as $key => $article) {
//     if($article['author_id'] == session_get('user_id')) {
//         $my_articles[$key] = $article;
//     }
// }

switch($data['type-filter']) {
    case 'mine': $shown_articles = $my_articles; break;
    case 'published': $shown_articles = $data['articles-post-published'] + $data['articles-music_theory-published']; break;
    case 'private': $shown_articles = $data['articles-post-private'] + $data['articles-music_theory-private']; break;
    case 'pending': $shown_articles = $data['articles-post-pending'] + $data['articles-music_theory-pending']; break;
    case 'draft': $shown_articles = $data['articles-post-draft'] + $data['articles-music_theory-draft']; break;
    case 'trash': $shown_articles = $data['articles-post-trash'] + $data['articles-music_theory-trash']; break;
}

// dump($shown_articles);
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac"><?= translate($settings, 'article_list_page_title') ?></h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>
        
        <div style="text-align: end; margin: 2rem;">
            <a href="<?=baseUrl() . $settings['superadmin_panel_topbar_2_sidebar_3']['url'] ?>" class="btn-outline"><?= translate($settings, 'superadmin_panel_topbar_2_sidebar_3') ?></a>
        </div>
        
        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl()?>/admin/showArticleList/all/posts.post_id"><?= translate($settings, 'article_list_top_filter_1') ?> <span class="count">(<?= sizeof($all_articles)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl()?>/admin/showArticleList/mine/posts.post_id"><?= translate($settings, 'article_list_top_filter_2') ?> <span class="count">(<?= sizeof($my_articles)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl()?>/admin/showArticleList/published/posts.post_id"><?= translate($settings, 'article_list_top_filter_3') ?> <span class="count">(<?= sizeof($data['articles-post-published'] + $data['articles-music_theory-published'])?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl()?>/admin/showArticleList/private/posts.post_id"><?= translate($settings, 'article_list_top_filter_4') ?> <span class="count">(<?= sizeof($data['articles-post-private'] + $data['articles-music_theory-private'])?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl()?>/admin/showArticleList/pending/posts.post_id"><?= translate($settings, 'article_list_top_filter_5') ?> <span class="count">(<?= sizeof($data['articles-post-pending'] + $data['articles-music_theory-pending'])?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl()?>/admin/showArticleList/draft/posts.post_id"><?= translate($settings, 'article_list_top_filter_6') ?> <span class="count">(<?= sizeof($data['articles-post-draft'] + $data['articles-music_theory-draft'])?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl()?>/admin/showArticleList/trash/posts.post_id"><?= translate($settings, 'article_list_top_filter_7') ?> <span class="count">(<?= sizeof($data['articles-post-trash'] + $data['articles-music_theory-trash'])?>)</span></a></li>
            </ul>
        </div>

        <!-- <?//= showTable($shown_articles, translate($settings, 'article_list_page_title')) ?>
        <br> -->

        <table>
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <!-- <th>ردیف</th> -->
                    <th><a href="<?=baseUrl()?>/admin/showArticleList/<?= $data['type-filter'] . '/users.user_id'?>" style="color:white"><?= translate($settings, 'post__table_row_1') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?=baseUrl()?>/admin/showArticleList/<?= $data['type-filter'] . '/translations.title'?>" style="color:white"><?= translate($settings, 'post__table_row_2') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?=baseUrl()?>/admin/showArticleList/<?= $data['type-filter'] . '/posts.status'?>" style="color:white"><?= translate($settings, 'post_table_row_11') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?=baseUrl()?>/admin/showArticleList/<?= $data['type-filter'] . '/posts.type'?>" style="color:white"><?= translate($settings, 'post_table_row_10') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    
                    <th><a href="<?=baseUrl()?>/admin/showArticleList/<?= $data['type-filter'] . '/posts.type'?>" style="color:white"><?= translate($settings, 'post_table_row_9') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?=baseUrl()?>/admin/showArticleList/<?= $data['type-filter'] . '/posts.updated_at'?>" style="color:white"><?= translate($settings, 'post_table_row_8') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><?= translate($settings, 'tables_action_title') ?></th>
                </tr>
            </thead>
            <tbody>
                </tr><? foreach($shown_articles as $key => $article) { ?>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><?= $users[$article['author_id']]['title'] ?? $users[0]['title'] ?></td>
                        <td><a href="<?=baseUrl()?>/admin/editArticle/<?= $article['post_id'] ?>"><?= $article['title'] ?></a></td>
                        <td><?= $article['status'] ?></td>
                        <td><?= $article['type'] ?></td>
                        <td><?= $article['updated_at'] ?></td>
                        <td class="actions">
                            <a href="<?=baseUrl()?>/article/articleDetails/<?= $article['post_id'] ?>" class="edit-cat"><?= translate($settings, 'tables_action_preview') ?></a>
                            |&nbsp;&nbsp;&nbsp;
                            <a href="<?=baseUrl()?>/admin/delete_article/<?= $article['post_id'] ?>" class="delete-cat"><?= translate($settings, 'tables_action_delete') ?></a>
                        </td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>
