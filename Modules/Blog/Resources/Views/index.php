<div class="blog-page">
    <section class="blog-content">
        <?php foreach($posts as $post): ?>
            <?php component(
                'BlogCard',
                compact('post')
            ); ?>
        <?php endforeach; ?>
        <?php component(
            'Pagination',
            [
                'currentPage'=>$page,
                'totalPages'=>$pages
            ]
        ); ?>
    </section>
    <?php component(
        'BlogSidebar',
        compact(
            'categories',
            'latestPosts',
            'popularPosts'
        )
    ); ?>
</div>