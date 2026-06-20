<?
$branches = $data['branches'] ?? [];
$branches_members = $data['branches_members'] ?? [];
$roles = $data['roles'] ?? [];

$users = $data['users'];

$settings_array = $data['settings'];
$settings = setIndexforDataArray($settings_array, 'variable_name');

$table_headers_title = setIndexforDataArray(getFilteredList($settings, 'user_table_row_'), 'variable_name');
$genders = setIndexforDataArray(getFilteredList($settings, 'gender_'), 'variable_name');

// dump($academy_roles);

$account_roles = setIndexforDataArray(getFilteredList($settings, 'account_role_'), 'variable_name');
$user_activities = setIndexforDataArray(getFilteredList($settings, 'user_activity_'), 'variable_name');
?>

<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>

    <div class="content">
        <div class="header_ac">
            <h1 class="h1_ac">مدیران</h1>
            <p>به آموزشگاه سُرناز خوش آمدید - کلاس‌های حضوری و آنلاین</p>
        </div>


        <div class="comments-filter-bar">
            <ul class="filter-list">
                <li class="filter-item active"><a href="<?=baseUrl() . $settings['academy_managing_panel_sidebar_32']['url'] ?>"><?= translate($settings, 'academy_managing_panel_sidebar_32') ?> <span class="count">(<?= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_1_sidebar_2']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_1_sidebar_2') ?> <span class="count">(<?= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_2_sidebar_2']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_2_sidebar_2') ?> <span class="count">(<?= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_3_sidebar_2']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_3_sidebar_2') ?> <span class="count">(<?= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_4_sidebar_2']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_4_sidebar_2') ?> <span class="count">(<?= sizeof($branches_members)?>)</span></a></li>
                <li class="filter-item"><a href="<?=baseUrl() . $settings['academy_managing_panel_topbar_5_sidebar_2']['url'] ?>"><?= translate($settings, 'academy_managing_panel_topbar_5_sidebar_2') ?> <span class="count">(<?= sizeof($branches_members)?>)</span></a></li>
            </ul>
        </div>

        <!-- <?//= showTable($shown_articles, translate($settings, 'article_list_page_title')) ?>
        <br> -->
<!--
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th><a href="<?//=baseUrl()?>/admin/userManagement/<?//= $data['type-filter'] . '/sor_user.user_id'?>" style="color:white"><?//= translate($settings, 'post__table_row_1') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?//=baseUrl()?>/admin/userManagement/<?//= $data['type-filter'] . '/sor_posts.title'?>" style="color:white"><?//= translate($settings, 'post__table_row_2') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?//=baseUrl()?>/admin/userManagement/<?//= $data['type-filter'] . '/sor_posts.status'?>" style="color:white"><?//= translate($settings, 'post_table_row_10') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?//=baseUrl()?>/admin/userManagement/<?//= $data['type-filter'] . '/sor_posts.type'?>" style="color:white"><?//= translate($settings, 'post_table_row_9') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><a href="<?//=baseUrl()?>/admin/userManagement/<?//= $data['type-filter'] . '/sor_posts.modified'?>" style="color:white"><?//= translate($settings, 'post_table_row_8') ?></a><i class="icon-copy dw dw-sort"></i></th>
                    <th><?//= translate($settings, 'tables_action_title') ?></th>
                </tr>
            </thead>
            <tbody>
                </tr><?// foreach($shown_articles as $key => $value) { ?>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><?//= translateStrings($value, 'fullname') ?></td>
                        <td><a href="<?//=baseUrl()?>/admin/editArticle/<?//= $value['post_id'] ?>"><?//= translateStrings($value, 'title') ?></a></td>
                        <td><?//= $value['status'] ?></td>
                        <td><?//= $value['type'] ?></td>
                        <td><?//= $value['modified'] ?></td>
                        <td class="actions">
                            <a href="<?//=baseUrl()?>/article/articleDetails/<?//= $value['post_id'] ?>" class="edit-cat"><?//= translate($settings, 'tables_action_preview') ?></a>
                            |&nbsp;&nbsp;&nbsp;
                            <a href="<?//=baseUrl()?>/admin/delete_article/<?//= $value['post_id'] ?>" class="delete-cat"><?//= translate($settings, 'tables_action_delete') ?></a>
                        </td>
                    </tr>
                <? //} ?>
            </tbody>
        </table>
-->
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
                        <th>row</th>
                        <!-- <th>member_id</th> -->
                        <!-- <th>branch_id</th> -->
                        <th>user_id</th>
                        <th>title</th>
                        <th>role_id</th>
                        <th>status</th>
                        <th>joined_at</th>
                        <th>created_at</th>
                        <th>created_by</th>
                        <th>updated_at</th>
                        <th>updated_by</th>
                        <th>approved_at</th>
                        <th>approved_by</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($branches_members[$branch_id] as $key => $branch_member) { ?>
                        <tr style="background-color:<?= $branch_member['color']; ?>">
                            <td><?= $key + 1 ?></td>
                            <!-- <td><?//= $branch_member['member_id']?></td> -->
                            <!-- <td><?//= $branch_member['branch_id']?></td> -->
                            <td><?= $branch_member['user_id']?></td>
                            <td><?= $branch_member['title']?></td>
                            <td><?= $branch_member['name']?></td>
                            <!-- <td><?//= $branch_member['role_id']?></td> -->
                            <td><?= $branch_member['status']?></td>
                            <td><?= $branch_member['joined_at']?></td>
                            <td><?= $branch_member['created_at']?></td>
                            <td><?= $branch_member['created_by']?></td>
                            <td><?= $branch_member['updated_at']?></td>
                            <td><?= $branch_member['updated_by']?></td>
                            <td><?= $branch_member['approved_at']?></td>
                            <td><?= $branch_member['approved_by']?></td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
            <br>
        <? } ?>

                <?//= showTable($branches, 'Branches', $settings, $contact_table_headers_title) ?>
<br>


        <form method="POST" action="<?=baseUrl()?>/admin/add_new_academy_branch_member/">
            <input type="hidden" name="manager_id" value="<?= session_get('user_id') ?>" />
            
            <div class="form-group">
                <label for="branch_id">شعبه</label>
                <select id="branch_id" name="branch_id">
                    <? foreach ($branches as $key => $branch) { ?>
                        <option value="<?= $key ?>"><?= $branch['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="role_id">نقش</label>
                <select id="role_id" name="role_id">
                    <? foreach ($roles as $key => $role) { ?>
                        <option value="<?= $role['table_id'] ?>"><?= $role['title'] ?></option>
                    <? } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">وضعیت</label>
                <select id="status" name="status">
                    <option value="active">active</option>
                    <option value="pending">pending</option>
                    <option value="rejected">rejected</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="gender">جنسیت</label>
                <select id="gender" name="gender">
                    <option value="male">مرد</option>
                    <option value="female">زن</option>
                </select>
            </div>
            
            <div>
                <label for="joined_at">تاریخ شروع کار</label>
                <input type="date" id="joined_at" name="joined_at">
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
            <button type="submit">ثبت کلاس</button>
            <button type="reset" class="btn-outline">انصراف</button>
        </form>
    </div>
</div>




<style>
    :root {
        --primary: #0066cc;
        --primary-dark: #004080;
        --bg: #f8f9fa;
        --white: #ffffff;
        --shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    body {
        font-family: 'Vazirmatn', sans-serif;
        background: var(--bg);
        margin: 0;
        direction: rtl;
    }
    .admin-content { padding: 2rem; }

    .topbar {
        background: white;
        padding: 1.5rem 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        border-radius: 12px;
    }
    .page-title { font-size: 2.6rem; color: var(--primary); }

    /* تب‌های نقش */
    .role-tabs {
        display: flex;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        overflow: hidden;
        flex-wrap: wrap;
    }
    .tab {
        padding: 1.3rem 2rem;
        font-size: 1.6rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border-bottom: 4px solid transparent;
        flex: 1;
        text-align: center;
        min-width: 140px;
    }
    .tab.active {
        background: #f0f7ff;
        border-bottom: 4px solid var(--primary);
        color: var(--primary);
    }

    /* کنترل‌ها */
    .controls {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .search-box input {
        padding: 1rem 1.5rem;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 1.6rem;
        width: 320px;
    }
    .status-filter {
        padding: 1rem 1.5rem;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 1.55rem;
        background: white;
    }

    /* جدول */
    .table-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 1.4rem 1.8rem;
        text-align: right;
        border-bottom: 1px solid #eee;
        font-size: 1.55rem;
    }
    th {
        background: var(--primary);
        color: white;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
    }
    th:hover { background: #0055aa; }
    tr:hover { background: #f0f7ff; }

    .user-photo {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
    }
    .role-badge {
        padding: 0.4rem 1.2rem;
        border-radius: 30px;
        font-size: 1.35rem;
        font-weight: 500;
    }
    .status {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 1.4rem;
    }
    .status.active { background: #e8f5e9; color: #2e7d32; }
    .status.inactive { background: #fff3e0; color: #ef6c00; }

    .actions a {
        margin-left: 1rem;
        padding: 0.6rem 1.1rem;
        border-radius: 6px;
        font-size: 1.4rem;
        text-decoration: none;
    }
    .edit-btn { color: var(--primary); }
    .delete-btn { color: #d32f2f; }
</style>


<div class="admin-content">

    <div class="topbar">
        <h1 class="page-title">مدیریت کاربران سایت</h1>
    </div>

    <!-- تب‌های نقش -->
    <div class="role-tabs" id="roleTabs">
        <div class="tab active" data-role="all" onclick="filterByRole('all')">همه کاربران</div>
        <div class="tab" data-role="admin" onclick="filterByRole('admin')">مدیران سایت</div>
        <div class="tab" data-role="school-admin" onclick="filterByRole('school-admin')">مدیران آموزشگاه</div>
        <div class="tab" data-role="teacher" onclick="filterByRole('teacher')">مدرسین</div>
        <div class="tab" data-role="writer" onclick="filterByRole('writer')">نویسندگان</div>
        <div class="tab" data-role="developer" onclick="filterByRole('developer')">برنامه‌نویسان</div>
        <div class="tab" data-role="designer" onclick="filterByRole('designer')">طراحان</div>
    </div>

    <!-- کنترل‌ها -->
    <div class="controls">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="جستجو در نام، نام کاربری یا ایمیل..." onkeyup="filterTable()">
        </div>
        <select class="status-filter" id="statusFilter" onchange="filterTable()">
            <option value="all">همه وضعیت‌ها</option>
            <option value="active">فعال</option>
            <option value="inactive">غیرفعال</option>
        </select>

        <!-- دکمه‌های خروجی و حذف چندتایی -->
        <button onclick="exportToExcel()" class="btn btn-primary" style="margin-right:10px;">
            <i class="fas fa-file-excel"></i> خروجی Excel
        </button>
        <button onclick="exportToPDF()" class="btn btn-primary" style="margin-right:10px;">
            <i class="fas fa-file-pdf"></i> خروجی PDF
        </button>
        <button onclick="deleteSelected()" class="btn btn-cancel" style="margin-right:10px;">
            <i class="fas fa-trash"></i> حذف انتخابی
        </button>
    </div>

    <!-- جدول کاربران -->
    <div class="table-card">
        <table id="usersTable">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                    <th onclick="sortTable(1)">عکس</th>
                    <th onclick="sortTable(2)">نام و نام خانوادگی</th>
                    <th onclick="sortTable(3)">نام کاربری</th>
                    <th onclick="sortTable(4)">ایمیل</th>
                    <th onclick="sortTable(5)">نقش</th>
                    <th onclick="sortTable(6)">وضعیت</th>
                    <th onclick="sortTable(7)">تاریخ ثبت</th>
                    <th onclick="sortTable(8)">آخرین ورود</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <!-- داده‌های نمونه -->
                <tr data-role="admin" data-status="active">
                    <td><input type="checkbox" class="row-select"></td>
                    <td><img src="https://via.placeholder.com/55" alt="" class="user-photo"></td>
                    <td><strong>علی رضایی</strong></td>
                    <td>admin</td>
                    <td>admin@sornaz.com</td>
                    <td><span class="role-badge" style="background:#0066cc;color:white;">مدیر سایت</span></td>
                    <td><span class="status active">فعال</span></td>
                    <td>۱۴۰۳/۰۵/۱۰</td>
                    <td>۱۴۰۴/۱۱/۲۷</td>
                    <td class="actions">
                        <a href="#" class="edit-btn"><i class="fas fa-edit"></i></a>
                        <a href="#" class="delete-btn" onclick="deleteRow(this)"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <!-- می‌توانید ردیف‌های بیشتری اضافه کنید -->
            </tbody>
        </table>
    </div>

</div>

<script>
    // انتخاب همه
    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.row-select');
        const selectAll = document.getElementById('selectAll');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    }

    // حذف انتخابی
    function deleteSelected() {
        if (!confirm('آیا از حذف کاربران انتخاب‌شده مطمئن هستید؟')) return;
        
        const checkboxes = document.querySelectorAll('.row-select:checked');
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.remove();
        });
        document.getElementById('selectAll').checked = false;
    }

    // حذف تک ردیف
    function deleteRow(btn) {
        if (confirm('آیا از حذف این کاربر مطمئن هستید؟')) {
            btn.closest('tr').remove();
        }
    }

    // جستجوی زنده + فیلتر وضعیت + تب نقش
    function filterTable() {
        const searchText = document.getElementById("searchInput").value.toLowerCase().trim();
        const statusFilter = document.getElementById("statusFilter").value;
        const rows = document.querySelectorAll('#usersTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.getAttribute('data-status');
            const matchesSearch = text.includes(searchText);
            const matchesStatus = statusFilter === 'all' || status === statusFilter;
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    // مرتب‌سازی جدول
    let sortDirection = {};
    function sortTable(colIndex) {
        const table = document.getElementById("usersTable");
        const tbody = table.getElementsByTagName("tbody")[0];
        let rows = Array.from(tbody.getElementsByTagName("tr"));

        sortDirection[colIndex] = !sortDirection[colIndex];

        rows.sort((a, b) => {
            let cellA = a.cells[colIndex].textContent.trim();
            let cellB = b.cells[colIndex].textContent.trim();
            if (colIndex === 1) return 0; // عکس

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return sortDirection[colIndex] ? cellA - cellB : cellB - cellA;
            }
            return sortDirection[colIndex] ? 
                cellA.localeCompare(cellB, 'fa') : 
                cellB.localeCompare(cellA, 'fa');
        });

        tbody.innerHTML = "";
        rows.forEach(row => tbody.appendChild(row));
    }

    // خروجی Excel (CSV با فرمت xlsx)
    function exportToExcel() {
        const table = document.getElementById("usersTable");
        let csv = "نام و نام خانوادگی,نام کاربری,ایمیل,نقش,وضعیت,تاریخ ثبت,آخرین ورود\n";
        
        table.querySelectorAll("tbody tr").forEach(row => {
            if (row.style.display !== "none") {
                let cells = Array.from(row.cells).slice(2, -1); // حذف عکس و عملیات
                let rowData = cells.map(cell => `"${cell.textContent.trim()}"`).join(",");
                csv += rowData + "\n";
            }
        });

        const blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8;" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "users_export.xlsx";
        link.click();
    }

    // خروجی PDF با jsPDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // landscape

        doc.setFont("Vazirmatn", "normal");
        doc.setFontSize(18);
        doc.text("لیست کاربران سایت - آموزشگاه موسیقی سُرناز", 20, 20);

        doc.setFontSize(12);
        doc.text("تاریخ گزارش: " + new Date().toLocaleDateString('fa-IR'), 20, 30);

        // استخراج داده‌ها از جدول
        const table = document.getElementById("usersTable");
        let y = 45;

        table.querySelectorAll("tbody tr").forEach(row => {
            if (row.style.display !== "none") {
                let name = row.cells[2].textContent.trim();
                let username = row.cells[3].textContent.trim();
                let email = row.cells[4].textContent.trim();
                let role = row.cells[5].textContent.trim();
                
                doc.text(`${name} - ${username} - ${email} - ${role}`, 20, y);
                y += 8;
                if (y > 190) {
                    doc.addPage();
                    y = 20;
                }
            }
        });

        doc.save("users_list.pdf");
    }
</script>




<!-- ================================================================================================================= -->
<!-- ================================================================================================================= -->
<!-- ================================================================================================================= -->


<div class="wrapper">
    <? View::partial("/admin/sidebar", $data) ?>
    <div class="content">
        <div class="admin-content">
            <div class="topbar">
                <h1 class="page-title">مدیریت کاربران</h1>
            </div>


            <h3>افزودن کاربر جدید</h3>
            <form action="<?=baseUrl()?>/admin/add_user_from_admin_panel" method="post" enctype="multipart/form-data">
                <div>
                    <label><?= translate($settings, 'authentication_email') ?> <span class="required">*</span></label>
                    <input type="email" name="email"/>
                </div>
                <div>
                    <label><?= translate($settings, 'authentication_username') ?> <span class="required">*</span></label>
                    <input type="text" name="username"/>
                </div>
                <div>
                    <label><?= translate($settings, 'authentication_fullname_fa') ?> <span class="required">*</span></label>
                    <input type="text" name="fullname_fa"/>
                </div>
                <div>
                    <label><?= translate($settings, 'authentication_fullname_en') ?> <span class="required">*</span></label>
                    <input type="text" name="fullname_en"/>
                </div>




                <div>
                    <label>national_code <span class="required">*</span></label>
                    <input type="text" name="national_code"/>
                </div>


                <div>
                    <label>birthday</label>
                    <input type="date" name="birthday" placeholder="۱۲ سال">
                </div>

                
                <div>
                    <label>شماره تماس</label>
                    <input type="tel" name="mobile" placeholder="۰۹۱۲ XXX XXXX">
                </div>




                <div>
                    <label>نقش<span>*</span></label>
                    <select name="role" required>
                        <option value="">انتخاب کنید...</option>
                        <? foreach($account_roles as $key => $account_role) { ?>
                            <option value="<?= $account_role['text_en'] . '-' . substr($account_role['text_en'], 0, strlen($account_role['text_en']) - 1) ?>"><?= translateStrings($account_role, 'text')  ?></option>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label>gender<span>*</span></label>
                    <select name="gender" required>
                        <option value="">انتخاب کنید...</option>
                        <? foreach($genders as $key => $gender) { ?>
                            <option value="<?= $gender['text_en'] ?>"><?= translateStrings($gender, 'text')  ?></option>
                        <? } ?>
                    </select>
                </div>

                <div>
                    <label>عکس</label>
                    <input type="file" name="picture_type" accept="image/*">
                </div>
                <!-- <div>
                    <label>activity_status</label>
                    <input type="checkbox" name="activity_status">
                </div> -->

                <div>
                    <label>وضعیت</label>
                    <select name="activity_status">
                        <option value="">انتخاب کنید...</option>
                        <? foreach($user_activities as $key => $user_activity) { ?>
                            <option value="<?= $user_activity['value'] ?>"><?= translateStrings($user_activity, 'text')  ?></option>
                        <? } ?>
                    </select>
                </div>
                <!-- <div>
                    <label for="status"></label>
                    <select id="status" name="activity_status">
                        <option value="on">فعال</option>
                        <option value="off">غیرفعال</option>
                    </select>
                </div> -->

                <button type="submit"><?= translate($settings, 'authentication_register') ?></button>
                <!-- <button type="submit">ثبت مدرس جدید</button> -->
            </form>


            <?//= showTable($users, 'Users', $settings, $table_headers_title, 'edit', 'user_id', '/admin/delete_user_from_admin_panel', '/admin/editUser') ?>
            <br>


        </div>
    </div>

</div>


