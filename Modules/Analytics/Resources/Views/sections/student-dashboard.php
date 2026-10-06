<?php
$studentUserId = (int) auth()->id();
$studentUser = \Core\database\DB::table('users')->where('user_id', $studentUserId)->whereNull('deleted_at')->first();
$studentMembers = \Core\database\DB::table('academy_branch_members')->where('user_id', $studentUserId)->whereNull('deleted_at')->get();
$studentMemberIds = array_map(static fn($m) => (int) $m['member_id'], $studentMembers);
$studentRequests = $studentMemberIds ? \Core\database\DB::table('academy_branch_course_term_waiting_list')->whereIn('member_id', $studentMemberIds)->whereNull('deleted_at')->orderBy('term_waiting_list_id', 'DESC')->limit(20)->get() : [];
$studentText = static function (string $table, int $id, string $field, string $fallback = ''): string {
    $row = \Core\database\DB::table('translations')->where('table_name', $table)->where('table_id', $id)->where('field', $field)->where('locale', 'fa')->whereNull('deleted_at')->orderBy('translation_id', 'DESC')->first();
    return (string) ($row['value'] ?? $fallback);
};
$studentName = $studentText('users', $studentUserId, 'full_name', (string) ($studentUser['username'] ?? ''));
$studentFather = $studentText('users', $studentUserId, 'father_name');
$studentAddressRow = \Core\database\DB::table('user_addresses')->where('user_id', $studentUserId)->where('is_main', 1)->whereNull('deleted_at')->first();
$studentAddress = $studentAddressRow ? $studentText('user_addresses', (int) $studentAddressRow['address_id'], 'address') : '';
?>
<section id="dashboard" class="section hidden space-y-6" data-dashboard-kind="student">
    <header><h1 class="text-3xl font-bold">داشبورد من</h1><p class="mt-1 text-gray-500">درخواست‌های ثبت‌نام و مسیر کلاس‌های شما</p></header>
    <div class="grid gap-4 sm:grid-cols-3">
        <button type="button" onclick="showSection('chat')" class="rounded-2xl bg-indigo-600 p-5 text-right text-white">گفتگوهای من</button>
        <button type="button" onclick="showSection('my-terms')" class="rounded-2xl bg-white p-5 text-right shadow">ترم‌های من</button>
        <button type="button" onclick="showSection('my-classrooms')" class="rounded-2xl bg-white p-5 text-right shadow">کلاس‌های من</button>
    </div>
    <div><h2 class="mb-4 text-xl font-bold">درخواست‌های ثبت‌نام من</h2><div class="grid gap-4 lg:grid-cols-2">
        <?php if (!$studentRequests): ?><p class="rounded-2xl bg-white p-6 text-gray-500">هنوز درخواست ثبت‌نامی ثبت نکرده‌اید.</p><?php endif; ?>
        <?php foreach ($studentRequests as $request):
            $requestId = (int) $request['term_waiting_list_id'];
            $term = \Core\database\DB::table('academy_branch_course_terms')->where('term_id', (int) $request['term_id'])->whereNull('deleted_at')->first();
            $course = $term ? \Core\database\DB::table('academy_branch_courses')->where('course_id', (int) $term['course_id'])->whereNull('deleted_at')->first() : null;
            $termTitle = $studentText('academy_branch_course_terms', (int) $request['term_id'], 'title', 'ترم #' . (int) $request['term_id']);
            $courseTitle = $course ? $studentText('academy_branch_courses', (int) $course['course_id'], 'title', 'دوره #' . (int) $course['course_id']) : '';
            $chatId = (int) $studentText('academy_branch_course_term_waiting_list', $requestId, 'conversation_id');
            $note = $studentText('academy_branch_course_term_waiting_list', $requestId, 'description');
            $canEdit = empty($request['approved_at']);
            $studentEnrollment = \Core\database\DB::table('academy_branch_course_term_enrollments')->where('term_id', (int) $request['term_id'])->where('member_id', (int) $request['member_id'])->where('type', 'student')->whereNull('deleted_at')->first();
            $requestStatus = !$canEdit && ($studentEnrollment['status'] ?? '') === 'active' ? 'کلاس فعال' : ($canEdit ? 'در انتظار بررسی آموزشگاه' : 'تأیید شده؛ در انتظار هماهنگی کلاس');
        ?>
        <article class="rounded-2xl border bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h3 class="font-bold"><?= e($termTitle) ?></h3><p class="mt-1 text-sm text-gray-500"><?= e($courseTitle) ?> · درخواست #<?= $requestId ?></p></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs text-indigo-700"><?= e($requestStatus) ?></span></div>
            <?php if ($chatId): ?><button type="button" onclick="showSection('chat');Promise.resolve(window.chatReady).then(()=>window.reloadChat?.()).then(()=>window.openChat?.(<?= $chatId ?>))" class="mt-4 rounded-xl border border-indigo-300 px-4 py-2 text-sm text-indigo-700">گفتگوی هماهنگی ساعت و مدرس</button><?php endif; ?>
            <?php if ($canEdit): ?><details class="mt-4"><summary class="cursor-pointer text-sm font-medium text-indigo-700">مشاهده و ویرایش اطلاعات درخواست</summary><form class="mt-4 grid gap-3 sm:grid-cols-2" onsubmit="saveMyWaiting(event,<?= $requestId ?>)"><input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <label class="text-sm">نام و نام خانوادگی<input name="name" required value="<?= e($studentName) ?>" class="mt-1 w-full rounded-xl border p-2"></label>
                <label class="text-sm">شماره تماس<input name="phone" required value="<?= e((string) ($studentUser['phone'] ?? '')) ?>" class="mt-1 w-full rounded-xl border p-2"></label>
                <label class="text-sm">کد ملی<input name="national_id" value="<?= e((string) ($studentUser['national_code'] ?? '')) ?>" class="mt-1 w-full rounded-xl border p-2"></label>
                <label class="text-sm">نام پدر<input name="father_name" value="<?= e($studentFather) ?>" class="mt-1 w-full rounded-xl border p-2"></label>
                <label class="text-sm">تاریخ تولد<input name="birth_date" type="date" value="<?= e((string) ($studentUser['birthday'] ?? '')) ?>" class="mt-1 w-full rounded-xl border p-2"></label>
                <label class="text-sm sm:col-span-2">آدرس<textarea name="address" class="mt-1 w-full rounded-xl border p-2"><?= e($studentAddress) ?></textarea></label>
                <label class="text-sm sm:col-span-2">توضیحات درخواست<textarea name="note" maxlength="2000" class="mt-1 w-full rounded-xl border p-2"><?= e($note) ?></textarea></label>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-white sm:col-span-2">ذخیره تغییرات</button>
            </form></details><?php elseif ($note !== ''): ?><p class="mt-3 text-sm text-gray-600">توضیحات: <?= e($note) ?></p><?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div></div>
</section>
<script>
window.saveMyWaiting = async function (event, id) {
    event.preventDefault();
    const form = event.currentTarget, button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    try {
        const response = await fetch('/analytics/my-waiting/' + Number(id), {method:'POST', credentials:'same-origin', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':window.adminCsrfToken||''}, body:new URLSearchParams(new FormData(form))});
        const payload = await response.json(), result = payload.data ?? payload;
        if (!response.ok || result.success === false) throw new Error(result.message || 'ذخیره انجام نشد.');
        window.location.hash = 'dashboard';
        window.location.reload();
    } catch (error) { alert(error.message || 'ذخیره انجام نشد.'); button.disabled = false; }
};
</script>
