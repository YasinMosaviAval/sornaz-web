<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;

// dump($_SESSION);
// exit();
?>
<main class="academy-request-form">
    <div>
        <h1><?= translate($settings, 'send_academy_form_page_title') ?></h1>
    </div>
    <form method="POST" action="<?=baseUrl()?>/page/add_new_academy/" enctype="multipart/form-data" id="addCategoryForm">
		<div>
			<label for="email"><?= translate($settings, 'authentication_email') ?> <span class="required">*</span></label>
			<input type="email" id="email" name="email"/>
		</div>
		<div>
			<label for="username"><?= translate($settings, 'authentication_username') ?> <span class="required">*</span></label>
			<input type="text" id="username" name="username"/>
		</div>
		<div>
			<label for="password1"><?= translate($settings, 'authentication_password') ?> <span class="required">*</span></label>
			<input type="password" id="password1" name="password1"/>
		</div>
		<div>
			<label for="password2"><?= translate($settings, 'authentication_confirm_password') ?> <span class="required">*</span></label>
			<input type="password" id="password2" name="password2"/>
		</div>

		<div>
			<label for="fullname">نام آموزشگاه <span class="required">*</span></label>
			<input type="text" id="fullname" name="fullname"/>
		</div>
        <div>
            <label for="brief">توضیح کوتاه</label>
            <input type="text" id="brief" name="brief" placeholder="<?//= $settings['academy_table_row_']['url'] ?>">
        </div>
        <div>
            <label for="biography">بیوگرافی</label>
            <textarea id="biography" name="biography" rows="3" placeholder="<?//= $settings['academy_table_row_']['description_fa'] ?>"></textarea>
        </div>

        <br>

        <button type="submit"><?= translate($settings, 'add_new_academy_cta_button') ?></button>
        <button type="reset" class="btn-outline"><?= translate($settings, 'add_new_academy_discard_button') ?></button>
    </form>
</main>




<!-- 

تعداد کلاس

تعداد مدرس

ساعت های حضور مدرسین در آموزشگاه

ساعت های خالی کلاس های آموزشگاه

ساعت های خالی مدرسین

ساعت کلاس های مدرسین

-->