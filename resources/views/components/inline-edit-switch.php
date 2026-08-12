<?php if (\Modules\System\Services\SiteAdminAccess::allows(auth()->user())): ?>
<label class="admin-edit-switch inline-flex items-center gap-2 rounded-xl border px-2 py-1.5 cursor-pointer" data-no-inline-edit title="<?= e(locale()==='fa'?'حالت نمایش یا ویرایش متن‌های ثابت':'View or edit static texts') ?>">
    <span class="hidden xl:inline text-xs font-medium" data-admin-edit-label><?= locale()==='fa'?'نمایش':'View' ?></span>
    <input type="checkbox" class="sr-only" data-admin-edit-toggle onchange="SiteInlineEditor.setMode(this.checked)">
    <span class="admin-edit-switch__track relative block h-5 w-9 rounded-full bg-gray-200 after:absolute after:top-1 after:left-1 after:h-3 after:w-3 after:rounded-full after:bg-white after:shadow"></span>
    <i class="fas fa-pen text-xs text-gray-500"></i>
</label>
<?php endif; ?>
