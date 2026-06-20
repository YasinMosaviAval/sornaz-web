<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$invoice = $data['invoice'][0] ?? [];
$installments = $data['installments'] ?? [];
// dump($invoice);
// dump($installments);
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">قسط بندی فاکتور</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>
<br>
        <div>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $invoice['title']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['brief']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['description']?> &nbsp; / </span>
            <br>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $invoice['term_id']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['member_id']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['discount_id']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['payable_amount']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['currency_id']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['status']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['due_date']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['issued_at']?> &nbsp; / </span>
            <br>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; - <?= $invoice['created_by']?> &nbsp; / </span>
            <span> &nbsp; <?= $invoice['created_at']?> &nbsp; / </span>
        </div>
        <br>
        <br>
        <table>
            <thead>
                <tr>
                    <!-- <th>term_invoice_installment_id</th> -->
                    <th>term_invoice_installment_id</th>
                    <th>invoice_id</th>
                    <th>title</th>
                    <th>installment_number</th>
                    <th>amount</th>
                    <th>due_date</th>
                    <th>status</th>
                    <th>paid_at</th>
                    <th>created_at</th>
                    <th>created_by</th>
                    <th>updated_at</th>
                    <th>updated_by</th>
                    <th>approved_at</th>
                    <th>approved_by</th>
                </tr>
            </thead>
            <tbody>
                <? foreach($installments as $key => $installment) { ?>
                    <tr>
                        <!-- <td><?//= $installment['term_invoice_installment_id']?></td> -->
                        <td><?= $key + 1 ?></td>
                        <td><?= $installment['invoice_id']?></td>
                        <td><?= $installment['title']?></td>
                        <td><?= $installment['installment_number']?></td>
                        <td><?= $installment['amount']?></td>
                        <td><?= $installment['due_date']?></td>
                        <td><?= $installment['status']?></td>
                        <td><?= $installment['paid_at']?></td>
                        <td><?= $installment['created_at']?></td>
                        <td><?= $installment['created_by']?></td>
                        <td><?= $installment['updated_at']?></td>
                        <td><?= $installment['updated_by']?></td>
                        <td><?= $installment['approved_at']?></td>
                        <td><?= $installment['approved_by']?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
        <br>
<br>

        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_course_term_invoice_installment/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            <input type="hidden" name="invoice_id" value="<?= $invoice['term_invoice_id'] ?>" />

            <div>
                <label for="due_date">تاریخ ایجاد</label>
                <input type="date" id="due_date" name="due_date" required>
            </div>

            <div>
                <label for="installment_number">شماره قسط</label>
                <input type="number" id="installment_number" name="installment_number" required>
            </div>

            <div>
                <label for="amount">مبلغ</label>
                <input type="number" id="amount" name="amount" required>
            </div>

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
            <button type="submit">ثبت نام</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>




    </div>
</div>
