<?

$categories = $data['categories'];


$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');

$table_headers_title = setIndexforDataArray(getFilteredList($settings, 'settings_table_row_'), 'variable_name');

// dump($categories[0]);
// exit()
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="admin-content">
            <div class="header_ac">
                <h1 class="h1_ac"><?= translate($settings, 'categories_page_title') ?></h1>
                <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
            </div>

            <br>
            <table>
                <thead>
                    <tr>
                        <th>row</th>
                        <th>title</th>
                        <th>sort_order</th>
                        <th>parent_id</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th><?= translate($settings, 'tables_action_title') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($categories as $key => $category) { ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $category['title']?></td>
                            <td><?= $category['sort_order']?></td>
                            <td><?= $category['parent_id']?></td>
                            <td><?= $category['created_at']?></td>
                            <td><?= $category['created_by']?></td>
                            <td><?= $category['updated_at']?></td>
                            <td><?= $category['updated_by']?></td>
                            <td class="actions">
                                <a href="<?=baseUrl()?>/admin/showCategory/<?= $category['setting_id'] ?>" class="edit-cat"><?= translate($settings, 'tables_action_preview') ?></a>
                                |&nbsp;&nbsp;&nbsp;
                                <a href="<?=baseUrl()?>/admin/editCategory/<?= $category['setting_id'] ?>" class="edit-cat"><?= translate($settings, 'tables_action_edit') ?></a>
                                |&nbsp;&nbsp;&nbsp;
                                <a href="<?=baseUrl()?>/admin/delete_category/<?= $article['setting_id'] ?>" class="delete-cat"><?= translate($settings, 'tables_action_delete') ?></a>
                            </td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>


    <h3>دسته‌بندی‌های فعلی</h3>
    <table>
        <thead>
            <tr>
                <th>نام دسته</th>
                <th>نامک</th>
                <th>والد</th>
                <th>تعداد مقاله</th>
                <th>رنگ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>موسیقی ایرانی</td>
                <td>irani</td>
                <td>—</td>
                <td>۱۸</td>
                <td><span style="color:#0066cc;">■</span></td>
                <td>
                    <a href="#">ویرایش</a>
                    <a href="#">حذف</a>
                </td>
            </tr>
            <tr>
                <td>ردیف و دستگاه‌ها</td>
                <td>radif</td>
                <td>موسیقی ایرانی</td>
                <td>۱۲</td>
                <td><span style="color:#28a745;">■</span></td>
                <td>
                    <a href="#">ویرایش</a>
                    <a href="#">حذف</a>
                </td>
            </tr>
            <tr>
                <td>تئوری موسیقی جهانی</td>
                <td>global-theory</td>
                <td>—</td>
                <td>۹</td>
                <td><span style="color:#dc3545;">■</span></td>
                <td>
                    <a href="#">ویرایش</a>
                    <a href="#">حذف</a>
                </td>
            </tr>
        </tbody>
    </table>



    
            <h3><?= translate($settings, 'add_new_category_title') ?></h3>
            <form method="POST" action="<?=baseUrl()?>/admin/add_category/" enctype="multipart/form-data" id="addCategoryForm">
                <input type="hidden" name="page" id="page" value="article" />
                <input type="hidden" name="variable_name" id="variable_name" value="article_category_<?= sizeof($categories) + 1 ?>" />

                <div>
                    <label for="text_fa"><?= translate($settings, 'settings_table_row_6') ?> <span class="required">*</span></label>
                    <input type="text" id="text_fa" name="text_fa" required placeholder="<?= $settings['settings_table_row_0']['title'] ?>">
                </div>
                <div>
                    <label for="text_en"><?= translate($settings, 'settings_table_row_8') ?> <span class="required">*</span></label>
                    <input type="text" id="text_en" name="text_en" required placeholder="<?= $settings['settings_table_row_0']['title'] ?>">
                </div>

                <div>
                    <label for="description_fa"><?= translate($settings, 'settings_table_row_7') ?></label>
                    <textarea id="description_fa" name="description_fa" rows="3" placeholder="<?= $settings['settings_table_row_0']['description'] ?>"></textarea>
                </div>
                <div>
                    <label for="description_en"><?= translate($settings, 'settings_table_row_9') ?></label>
                    <textarea id="description_en" name="description_en" rows="3" placeholder="<?= $settings['settings_table_row_0']['description'] ?>"></textarea>
                </div>

                <div>
                    <label for="source"><?= translate($settings, 'settings_table_row_4') ?></label>
                    <input type="text" id="source" name="source" placeholder="<?= $settings['settings_table_row_0']['source'] ?>">
                </div>
                <div>
                    <label for="url"><?= translate($settings, 'settings_table_row_3') ?></label>
                    <input type="text" id="url" name="url" placeholder="<?= $settings['settings_table_row_0']['url'] ?>">
                </div>

                <div>
                    <label for="status"><?= translate($settings, 'settings_table_row_5') ?></label>
                    <input type="text" id="status" name="status" placeholder="<?= $settings['settings_table_row_0']['status'] ?>">
                </div>
                <div>
                    <label for="icon"><?= translate($settings, 'settings_table_row_10') ?></label>
                    <input type="text" id="icon" name="icon" placeholder="<?= $settings['settings_table_row_0']['icon'] ?>">
                    <!-- <small><?//= translate($settings, 'settings_table_row_10', 'title') ?></small> -->
                </div>

                <button type="submit"><?= translate($settings, 'add_new_category_cta_button') ?></button>
                <button type="reset" class="btn-outline"><?= translate($settings, 'add_new_category_discard_button') ?></button>
            </form>
        </div>
    </div>
</div>



<main class="academy-category">
    
    <h3>افزودن دسته‌بندی جدید</h3>
    <form id="addCategoryForm">
        <div>
            <div>
                <label for="cat_name">نام دسته‌بندی <span class="required">*</span></label>
                <input type="text" id="cat_name" name="cat_name" required placeholder="مثال: موسیقی ایرانی">
            </div>

            <div>
                <label for="cat_slug">نامک (Slug) <span class="required">*</span></label>
                <input type="text" id="cat_slug" name="cat_slug" required placeholder="مثال: irani">
                <br>
                <small>فقط حروف کوچک انگلیسی، عدد و خط تیره مجاز است</small>
            </div>
        </div>

        <div>
            <div>
                <label for="parent_cat">دسته‌بندی والد</label>
                <select id="parent_cat" name="parent_cat">
                    <option value="0">— بدون والد —</option>
                    <option value="1">موسیقی ایرانی</option>
                    <option value="2">تئوری موسیقی جهانی</option>
                    <option value="3">ردیف و دستگاه‌ها</option>
                    <option value="4">موسیقیدانان</option>
                    <!-- گزینه‌های بیشتر رو بعداً از دیتابیس لود کن -->
                </select>
            </div>

            <div>
                <label for="cat_description">توضیحات دسته‌بندی (اختیاری)</label>
                <textarea id="cat_description" name="cat_description" rows="4" placeholder="توضیح کوتاه برای صفحه آرشیو و سئو"></textarea>
            </div>
        </div>

        <div>
            <div>
                <label for="cat_color">رنگ دسته‌بندی (برای نمایش در سایت)</label>
                <input type="color" id="cat_color" name="cat_color" value="#0066cc">
            </div>
        </div>

        <div>
            <button type="submit">افزودن دسته‌بندی</button>
            <!-- <button type="reset" class="btn-outline">پاک کردن فرم</button> -->
        </div>
    </form>

</main>