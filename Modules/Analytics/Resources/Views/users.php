<div id="page-users" class="site-page active">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">کاربران</h1>
            <p class="text-gray-500">اساتید، هنرجویان و اعضای فعال جامعه موسیقی</p>
        </div>

        <!-- فیلتر نقش -->
        <div class="bg-white rounded-3xl p-4 mb-6 shadow-sm flex flex-wrap gap-2 justify-center" id="siteUserRoleTabs">
            <button onclick="filterSiteUsers('all')" class="site-user-role px-4 py-2 rounded-xl text-sm bg-indigo-600 text-white">همه</button>
            <?php foreach (($userCategories ?? []) as $category): ?>
                <button onclick="filterSiteUsers(<?= htmlspecialchars(json_encode($category['role'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>)" data-role="<?= htmlspecialchars($category['role'], ENT_QUOTES, 'UTF-8') ?>" class="site-user-role px-4 py-2 rounded-xl text-sm border border-gray-200 hover:bg-gray-50"><?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?></button>
            <?php endforeach; ?>
        </div>

        <div class="mb-6 flex justify-center">
            <input type="text" id="siteUserSearch" placeholder="جستجو نام..." onkeyup="filterSiteUsers()" class="w-full md:w-80 border border-gray-300 rounded-2xl py-3 px-4 focus:outline-none focus:border-indigo-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="siteUsersGrid" data-dynamic-content></div>
    </div>
</div>

<style>
.site-user-card-role::before {
    content: attr(data-role-label);
    display: block;
    min-height: 1.5rem;
    color: #4f46e5;
    font-size: .875rem;
    font-weight: 500;
    line-height: 1.5rem;
}
</style>

<?php require __DIR__ . '/user.php'; ?>

<script>
window.siteUsersData = <?= json_encode($users ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
window.siteUserCategories = <?= json_encode($userCategories ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
window.siteUserUiLabels = <?= json_encode($userUiLabels ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<!-- پروفایل کاربر -->
<!--
    <div id="page-user-detail" class="site-page">
        <div class="max-w-2xl mx-auto px-4 py-12">
            <a href="/analytics/user" class="text-indigo-600 text-sm mb-6 hover:underline flex items-center gap-2">
                <i class="fas fa-arrow-right"></i> بازگشت به کاربران
            </a>
            <div id="siteUserDetail"></div>
        </div>
    </div>
-->
