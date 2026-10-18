<?php
// Render the public-free dashboard fragment without opening a database connection.
ob_start();
require __DIR__ . '/../Modules/Analytics/Resources/Views/sections/dashboard.php';
$html = ob_get_clean();
foreach (['todayClassesList', 'urgentItemsList', 'recentPaymentsList', 'recentDepositsList',
    'todayAbsencesList', 'recentRegistrationsList', 'upcomingHolidaysList', 'dashboardStats', 'dashboardActionItems'] as $id) {
    if (!str_contains($html, 'id="' . $id . '"')) throw new LogicException('Dashboard section missing: ' . $id);
}
if (str_contains($html, 'unreadMessagesList') || str_contains($html, 'پیام‌های خوانده‌نشده')) {
    throw new LogicException('Removed unread-message section is visible.');
}
if (substr_count($html, 'مشاهده همه') !== 7) throw new LogicException('Dashboard view-all actions changed.');
echo "Dashboard layout: isolated render retains section boxes and seven view-all actions without the unread-message section.\n";
