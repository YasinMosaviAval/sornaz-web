<?
$settings = setIndexforDataArray($data['settings'], 'variable_name');;
$invoices = $data['invoices'] ?? [];
$branches = $data['branches'] ?? [];
$branches_courses = $data['branches_courses'] ?? [];
$courses_terms = $data['courses_terms'] ?? [];
$currencies = $data['currencies'] ?? [];
// dump($invoices);
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">فاکتور ها و بدهی ها</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>

        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_20']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_20') ?> <span class="count">(<?//= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>
        


        <? foreach($branches as $branch_id => $branch) { ?>
            <span> &nbsp; &nbsp; &nbsp; &nbsp; <?= $branch['title']?> &nbsp; / </span>
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
                                        <!-- <th>term_invoice_id</th> -->
                                        <th>row</th>
                                        <th>term_id</th>
                                        <th>title</th>
                                        <th>member_id</th>
                                        <th>discount_id</th>
                                        <th>payable_amount</th>
                                        <th>currency_id</th>
                                        <th>status</th>
                                        <th>due_date</th>
                                        <th>issued_at</th>
                                        <th>created_at</th>
                                        <th>created_by</th>
                                        <th>updated_at</th>
                                        <th>updated_by</th>
                                        <th>approved_at</th>
                                        <th>approved_by</th>
                                        <th>اقساط</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <? foreach($invoices as $invoice_branch_id => $invoice_array) { ?>
                                        <? $row = 0; ?>
                                        <? foreach($invoice_array as $term_id => $term_invoice) { ?>
                                            <? if($invoice_branch_id == $branch_id) { ?>
                                                <? foreach($term_invoice as $key => $invoice) { ?>
                                                    <? $row++; ?>
                                                    <tr>
                                                        <!-- <td><?//= $invoice['term_invoice_id']?></td> -->
                                                        <td><?= $row ?></td>
                                                        <td><?= $invoice['term_id']?></td>
                                                        <td><?= $invoice['title']?></td>
                                                        <td><?= $invoice['member_id']?></td>
                                                        <td><?= $invoice['discount_id']?></td>
                                                        <td><?= $invoice['payable_amount']?></td>
                                                        <td><?= $invoice['currency_id']?></td>
                                                        <td><?= $invoice['status']?></td>
                                                        <td><?= $invoice['due_date']?></td>
                                                        <td><?= $invoice['issued_at']?></td>
                                                        <td><?= $invoice['created_at']?></td>
                                                        <td><?= $invoice['created_by']?></td>
                                                        <td><?= $invoice['updated_at']?></td>
                                                        <td><?= $invoice['updated_by']?></td>
                                                        <td><?= $invoice['approved_at']?></td>
                                                        <td><?= $invoice['approved_by']?></td>
                                                        <td><a href="<?= baseUrl() . $settings['academy_beanch_cource_term_invoice_installments']['url'] . '/' . $invoice['term_invoice_id']?>">قسط بندی</a></td>
                                                    </tr>
                                                <? } ?>
                                            <? } ?>
                                        <? } ?>
                                    <? } ?>
                                </tbody>
                            </table>
                            <br>
                        <? } ?>






    </div>
</div>
