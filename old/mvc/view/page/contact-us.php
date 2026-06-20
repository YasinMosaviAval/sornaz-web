<?
$contact_us = $data['contact-us'];

foreach ($contact_us as $item) {
    if(str_contains($item['variable_name'], 'category_title')) {
        $contact_us_title[] = $item;
    }
}
?>

<main class="contact-us">
    <div>
        <h1><?= translate($contact_us, 'contact_us_header') ?></h1>
    </div>
    <form method="POST" action="<?=baseUrl()?>/page/send_new_message/" enctype="multipart/form-data">
        <input type="hidden" name="email" id="email" value="<?= session_get('email') ?>" />
        <input type="hidden" name="user_id" id="user_id" value="<?= session_get('user_id') ?>" />
        <input type="hidden" name="type" id="type" value="contact" />
        <input type="hidden" name="receiver_user_id" id="receiver_user_id" value="1" />

        <? if(!session_isset('user_id')) { ?>
            <div>
                <label for="author"><?= translate($contact_us, 'contact_us_name_label') ?> <span class="required">*</span></label>
                <input type="text" id="author" name="author" required placeholder="<?= translate($contact_us, 'contact_us_name_placeholder') ?>">
            </div>
            <div>
                <label for="author_email"><?= translate($contact_us, 'contact_us_email_label') ?> <span class="required">*</span></label>
                <input type="email" id="author_email" name="author_email" required placeholder="<?= translate($contact_us, 'contact_us_email_placeholder') ?>">
            </div>
        <? } ?>

        <select name="post_id" id="post_id">
            <? for($i = 0; $i < sizeof($contact_us_title); $i++) { ?>
                <option value="<?= $i ?>"><?= translate($contact_us_title, $i) ?></option>
            <? } ?>
        </select>

        <div>
            <label for="content"><?= translate($contact_us, 'contact_us_message_label') ?> <span class="required">*</span></label>
            <textarea id="content" name="content" rows="6" required placeholder="<?= translate($contact_us, 'contact_us_message_placeholder') ?>"></textarea>
        </div>

        <button type="submit"><?= translate($contact_us, 'contact_us_send_button') ?></button>
        <p><?= translate($contact_us, 'contact_us_note') ?></p>
    </form>
</main>
