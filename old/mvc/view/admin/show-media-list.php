<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$users = setIndexforDataArray($data['users'], 'user_id');
$branches = $data['branches'] ?? [];
$medias = $data['medias'] ?? [];
// dump($medias);
?>
<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">رسانه ها</h1>
            <p>رسانه هایی که به صورت عمومی در سطح سایت نمایش داده می شوند با رنگ سبز در جدول مشخص شده اند!</p>
        </div>


        <div style="text-align: end; margin: 2rem;">
            <a href="<?=baseUrl() . $settings['superadmin_panel_topbar_2_sidebar_5']['url'] ?>" class="btn-outline"><?= translate($settings, 'superadmin_panel_topbar_2_sidebar_5') ?></a>
            <a href="<?=baseUrl() . $settings['superadmin_panel_topbar_3_sidebar_5']['url'] ?>" class="btn-outline"><?= translate($settings, 'superadmin_panel_topbar_3_sidebar_5') ?></a>
        </div>


<br>
        <? foreach($branches as $branch_id => $branch) { ?>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $branch['title']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['brief']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['description']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['phone']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['national_code']?> &nbsp; / </span>
            <span> &nbsp; <?= $branch['birthday']?></span>
            <br>
            <br>
            <table>
                <thead>
                    <tr>
                        <!-- <th>media_file_id</th> -->
                        <th>row</th>
                        <!-- <th>user_id</th> -->
                        <th>title</th>
                        <!-- <th>disk</th> -->
                        <!-- <th>directory</th> -->
                        <!-- <th>filename</th> -->
                        <!-- <th>extension</th> -->
                        <!-- <th>mime_type</th> -->
                        <th>type</th>
                        <!-- <th>path</th> -->
                        <!-- <th>thumbnail_path</th> -->
                        <!-- <th>original_filename</th> -->
                        <!-- <th>fileable_type</th> -->
                        <!-- <th>fileable_id</th> -->
                        <!-- <th>sort_order</th> -->
                        <!-- <th>size</th> -->
                        <!-- <th>duration</th> -->
                        <!-- <th>width</th> -->
                        <!-- <th>height</th> -->
                        <!-- <th>checksum</th> -->
                        <!-- <th>visibility</th> -->
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($medias[$branch_id] as $key => $media) { ?>
                        <tr style="<?= $media['visibility'] == 'public' ? 'background-color: #00ff00;' : '' ?>">
                            <!-- <td><?//= $media['media_file_id']?></td> -->
                            <td><?= $key + 1 ?></td>
                            <!-- <td><?//= $media['user_id']?></td> -->
                            <td><?= $media['title']?></td>
                            <!-- <td><?//= $media['disk']?></td> -->
                            <!-- <td><?//= $media['directory']?></td> -->
                            <!-- <td><?//= $media['filename']?></td> -->
                            <!-- <td><?//= $media['extension']?></td> -->
                            <!-- <td><?//= $media['mime_type']?></td> -->
                            <td><?= $media['type']?></td>
                            <!-- <td><?//= $media['path']?></td> -->
                            <!-- <td><?//= $media['thumbnail_path']?></td> -->
                            <!-- <td><?//= $media['original_filename']?></td> -->
                            <!-- <td><?//= $media['fileable_type']?></td> -->
                            <!-- <td><?//= $media['fileable_id']?></td> -->
                            <!-- <td><?//= $media['sort_order']?></td> -->
                            <!-- <td><?//= $media['size']?></td> -->
                            <!-- <td><?//= $media['duration']?></td> -->
                            <!-- <td><?//= $media['width']?></td> -->
                            <!-- <td><?//= $media['height']?></td> -->
                            <!-- <td><?//= $media['checksum']?></td> -->
                            <!-- <td><?//= $media['visibility'] ?></td> -->
                            <td><?= $media['created_at']?></td>
                            <td><?= $users[$media['created_by']]['title'] ?></td>
                            <td><?= $media['updated_at'] ?></td>
                            <td><?= $users[$media['updated_by']]['title'] ?></td>
                            <? if($media['approved_by'] === null) { ?>
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?=baseUrl()?>/admin/approved_media_file/<?= $media['media_file_id'] ?>">
                                        <button>تایید رسانه</button>
                                    </a>
                                </td>
                            <? } else { ?>
                                <td><?= $media['approved_at'] ?></td>
                                <td><?= $users[$media['approved_by']]['title'] ?? '' ?></td>
                            <? } ?>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            در تایید خطا دیدم ولی عملکرد درست بود
            <br>
        <? } ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_media_file/" enctype="multipart/form-data">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="user_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="disk" value="public" /><!-- (local,public,private,s3,wasabi,google) -->
            <input type="hidden" name="directory" value="/media/pictures/other/" />
            <input type="hidden" name="fileable_type" value="" />
            <input type="hidden" name="fileable_id" value="0" />
            
            <div class="form-group">
                <label for="user_id">شعبه</label>
                <select id="user_id" name="user_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $branch['user_id'] ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="visibility">وضعیت مشاهده</label>
                    <select id="visibility" name="visibility">
                        <option value="public">عمومی</option>
                        <option value="private">خصوصی</option>
                        <option value="academy_only">فقط آموزشگاه</option>
                </select>
            </div>

            <div>
                <label for="featured_image"><?= translate($settings, 'edit_articles_cover') ?></label>
                <input type="file" id="media_file" name="media_file"
                    accept="
                    image/*,
                    video/*,
                    audio/*,
                    application/pdf,
                    application/msword,
                    application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                    application/vnd.ms-excel,
                    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                    application/vnd.ms-powerpoint,
                    application/vnd.openxmlformats-officedocument.presentationml.presentation"
                >
            </div>

            <div><label for="sort_order">ترتیب</label><input type="number" id="sort_order" name="sort_order"></div>

            <div>
                <label for="title">عنوان</label>
                <input type="text" id="title" name="title">
            </div>
            <div>
                <label for="brief">توضیح خلاصه</label>
                <input type="text" id="brief" name="brief">
            </div>
            <div>
                <label for="description">توضیح کامل</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>

            <br>
            <button type="submit">ثبت رسانه</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>



