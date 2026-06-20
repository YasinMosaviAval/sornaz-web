<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');
$branches = $data['branches'] ?? [];
$roles = $data['roles'] ?? [];

// dump($branches);
// dump($_SESSION);
// exit();
?>


<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">منشی ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>
        <form method="POST" action="<?=baseUrl()?>/admin/add_new_/">
            <input type="hidden" name="manager_id" value="<?= session_get('id') ?>" />

            <div class="form-group">
                <label for="term_id">ترم دوره</label>
                <select id="term_id" name="term_id">
                    <? foreach ($branches_course_terms as $branch_id => $branches_course_term) { ?>
                        <? foreach ($branches_course_term as $term) { ?>
                            <option value="<?= $term['id'] ?>"><?= $term['title'] . " - " . $branches[$branch_id]['title'] ?></option>
                            <? $price = $term['price'] ?>
                            <? $term_currency_id = $term['currency_id'] ?>
                        <? } ?>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="currency_id">نوع پول</label>
                <select id="currency_id" name="currency_id">
                    <? foreach ($currencies as $key => $currency) { ?>
                        <option value="<?= $currency['table_id'] ?>"><?= $currency['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="discount_type">نوع تخفیف</label>
                <select id="discount_type" name="discount_type">
                    <option value="percentage">درصد</option>
                    <option value="fixed">مقدار ثابت</option>
                </select>
            </div>

            <div>
                <label for="value">مقدار</label>
                <input type="text" id="value" name="value" step="0.01">
            </div>


            <input type="hidden" name="used_count" value="<?= session_get('used_count') ?>" />

            <div>
                <label for="payable_amount">قیمت قابل پرداخت</label>
                <input type="number" id="payable_amount" name="payable_amount" step="0.01">
            </div>

            <div class="form-group">
                <label for="is_main">آیا شعبه اصلی است؟</label>
                <input type="checkbox" id="is_main" name="is_main">
            </div>

            <div>
                <label for="due_date">تاریخ سررسید - مهلت پرداخت</label>
                <input type="date" id="due_date" name="due_date" required>
            </div>

            <br>
            <button type="submit">ثبت نام</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>
