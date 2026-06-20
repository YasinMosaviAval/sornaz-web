<!DOCTYPE html>
<html>
<? View::renderPartial("/article/wp/article-list-showing/head.php") ?>
<!-- <body class="no-js"> -->
	<div dir="rtl">
		<div id="wpwrap">
			<h1 class="wp-heading-inline">افزودن نوشته</h1>
			<form name="post" action="post.php" method="post" id="post">
				<? View::renderPartial("/article/wp/article-list-showing/post-body-content.php") ?>
				<div id="postbox-container-2" class="postbox-container">
					<? View::renderPartial("/article/wp/article-list-showing/comment-status.php") ?>
					<? View::renderPartial("/article/wp/article-list-showing/slug.php") ?>
					<? View::renderPartial("/article/wp/article-list-showing/author.php") ?>
					<? View::renderPartial("/article/wp/article-list-showing/rank-math.php") ?>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables">
						<? View::renderPartial("/article/wp/article-list-showing/post-structure.php") ?>
						<? View::renderPartial("/article/wp/article-list-showing/post-tags.php") ?>
					</div>
				</div>
			</form>
		</div>
	</div>
<!-- </body> -->
</html>
<!-- چکیده‌ها، خلاصه‌ای اختیاری از نوشته شماست، می‌توانید از آن در پوستهٔ سایت خود استفاده کنید.  -->