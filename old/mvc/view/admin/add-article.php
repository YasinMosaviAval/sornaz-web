<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
// dump($shown_articles);
?>


<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="admin-content">
            <div class="header_ac">
                <h1 class="h1_ac">افزودن مقاله جدید</h1>
                <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
            </div>
            <div style="text-align: end; margin: 2rem;">
                <a href="<?=baseUrl()?>/admin/showArticleList/all/posts.updated_at" class="btn btn-outline">بازگشت به لیست</a>
            </div>


            <form>
                <div>
                    <label for="title">عنوان مقاله <span class="required">*</span></label>
                    <input type="text" id="title" name="title" required placeholder="عنوان کامل مقاله را وارد کنید">
                </div>

                <div>
                    <label for="excerpt">خلاصه مقاله (برای نمایش در لیست)</label>
                    <textarea id="excerpt" name="excerpt" rows="4" placeholder="خلاصه کوتاه مقاله (حداکثر ۱۶۰ کاراکتر برای سئو)"></textarea>
                </div>

                <div>
                    <label for="category">دسته‌بندی ها</label>
                    <input type="checkbox" id="category" name="category" value=""><span>دسته بندی</span>
                    <input type="checkbox" id="category" name="category" value=""><span>دسته بندی</span>
                    <input type="checkbox" id="category" name="category" value=""><span>دسته بندی</span>
                    <input type="checkbox" id="category" name="category" value=""><span>دسته بندی</span>
                    <input type="checkbox" id="category" name="category" value=""><span>دسته بندی</span>
                </div>
                <div>
                    <label for="tags">کلمه کلیدی</label>
                    <input type="text" id="tags" name="tags" placeholder="کلمه کلیدی">
                </div>

                <div>
                    <label for="featured_image">تصویر شاخص مقاله</label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*">
                    <small>اندازه پیشنهادی: ۱۲۰۰×۶۳۰ پیکسل (برای نمایش بهتر در شبکه‌های اجتماعی)</small>
                </div>

                <div>
                    <label for="content">محتوای مقاله <span class="required">*</span></label>
                    <textarea id="content" name="content" rows="20" required placeholder="محتوای کامل مقاله را اینجا بنویسید..."></textarea>
                </div>

                <div>
                    <label for="status">وضعیت انتشار</label>
                    <select id="status" name="status">
                        <option value="draft">پیش‌نویس</option>
                        <option value="publish" selected>انتشار فوری</option>
                        <option value="pending">در انتظار بررسی</option>
                    </select>
                </div>
                <div>
                    <label for="publish_date">تاریخ انتشار</label>
                    <input type="datetime-local" id="publish_date" name="publish_date">
                </div>

                <button type="submit">ذخیره و انتشار</button>
                <button type="button" class="btn-outline">ذخیره به عنوان پیش‌نویس</button>
                <a href="<?=baseUrl()?>/admin/addNewArticle" class="btn-cancel">لغو</a>
            </form>
        </div>
    </div>
</div>

