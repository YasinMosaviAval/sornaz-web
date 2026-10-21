<?php
// Render the public-free dashboard fragment without opening a database connection.
ob_start();
require __DIR__ . '/../Modules/Analytics/Resources/Views/sections/dashboard.php';
$html = ob_get_clean();
foreach (['todayClassesList', 'recentPaymentsList', 'recentDepositsList',
    'todayAbsencesList', 'recentRegistrationsList', 'upcomingHolidaysList', 'dashboardStats', 'dashboardActionItems'] as $id) {
    if (!str_contains($html, 'id="' . $id . '"')) throw new LogicException('Dashboard section missing: ' . $id);
}
if (str_contains($html, 'unreadMessagesList') || str_contains($html, 'پیام‌های خوانده‌نشده')) {
    throw new LogicException('Removed unread-message section is visible.');
}
if (str_contains($html, 'urgentItemsList') || str_contains($html, 'موارد فوری')) {
    throw new LogicException('Removed urgent section is visible.');
}
if (substr_count($html, 'مشاهده همه') !== 6) throw new LogicException('Dashboard view-all actions changed.');
$previous = -1;
foreach (['todayClassesList', 'todayAbsencesList', 'recentRegistrationsList', 'upcomingHolidaysList', 'recentPaymentsList', 'recentDepositsList'] as $id) {
    $position = strpos($html, 'id="' . $id . '"');
    if ($position === false || $position <= $previous) throw new LogicException('Dashboard card order changed: ' . $id);
    $previous = $position;
}
if (str_contains($html, 'onclick="refreshDashboard()"')) throw new LogicException('Dashboard refresh button is visible.');
echo "Dashboard layout: isolated render retains section boxes and six view-all actions without urgent or unread-message sections.\n";
