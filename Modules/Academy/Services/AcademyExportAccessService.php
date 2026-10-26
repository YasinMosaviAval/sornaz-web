<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;

/** Subscription entitlements apply exclusively to built-in Excel/PDF exports. */
class AcademyExportAccessService
{
    public function status(int $actor, array $filters = []): array
    {
        $user = auth()->user();
        if (!$user || (int) ($user['user_id'] ?? 0) !== $actor) throw new RuntimeException('ورود به حساب کاربری لازم است.', 401);
        if (SiteAdminAccess::allows($user)) return ['allowed' => true, 'reason' => 'site_admin', 'academies' => []];
        $scope = $this->organizations($actor);
        $academies = array_column($scope['academies'], null, 'academy_id');
        $selected = $this->selectedAcademies($filters, $scope);
        $ids = $selected ?? array_keys($academies);
        foreach ($ids as $id) if (!isset($academies[$id])) throw new RuntimeException('آموزشگاه خارج از محدودهٔ دسترسی شما است.', 403);
        foreach ($ids as $id) if (!isset($academies[$id])) throw new RuntimeException('آموزشگاه خارج از محدودهٔ دسترسی شما است.', 403);
        $periods = $ids ? DB::table('academy_subscription_periods')->whereIn('academy_id', $ids)->whereNull('deleted_at')->get() : [];
        $today = $this->today();
        $items = [];
        foreach ($ids as $id) {
            if (!isset($academies[$id])) throw new RuntimeException('آموزشگاه خارج از محدودهٔ دسترسی شما است.', 403);
            $items[] = $this->entitlement($academies[$id], array_values(array_filter($periods, static fn ($period) => (int) $period['academy_id'] === (int) $id)), $today);
        }
        return ['allowed' => !array_filter($items, static fn ($item) => !$item['allowed']), 'reason' => $items ? 'academy_exports' : 'personal', 'academies' => $items, 'subscriptionUrl' => '/analytics/admin-panel#finance'];
    }

    protected function organizations(int $actor): array
    {
        return (new AcademyClassScheduleService())->exportOrganizations($actor);
    }

    protected function today(): string
    {
        return date('Y-m-d');
    }

    private function selectedAcademies(array $filters, array $scope): ?array
    {
        $ids = [];
        foreach (['academyIds', 'branchIds', 'organizationUserIds'] as $field) {
            $values = $filters[$field] ?? [];
            if (!is_array($values) || count($values) > 500) throw new RuntimeException('محدودهٔ خروجی معتبر نیست.', 422);
            foreach ($values as $value) {
                if (!is_scalar($value) || !ctype_digit((string) $value) || (int) $value < 1) throw new RuntimeException('محدودهٔ خروجی معتبر نیست.', 422);
                if ($field === 'academyIds') { $ids[] = (int) $value; continue; }
                $matches = array_filter($scope['branches'], static fn ($branch) => (int) $branch[$field === 'branchIds' ? 'branch_id' : 'user_id'] === (int) $value);
                if ($field === 'organizationUserIds') foreach ($scope['academies'] as $academy) if ((int) $academy['user_id'] === (int) $value) $ids[] = (int) $academy['academy_id'];
                foreach ($matches as $branch) $ids[] = (int) $branch['academy_id'];
                if (!$matches && !array_filter($scope['academies'], static fn ($academy) => $field === 'organizationUserIds' && (int) $academy['user_id'] === (int) $value)) throw new RuntimeException('سازمان خارج از محدودهٔ دسترسی شما است.', 403);
            }
        }
        return $ids ? array_values(array_unique($ids)) : null;
    }

    private function entitlement(array $academy, array $periods, string $today): array
    {
        $start = substr((string) ($academy['created_at'] ?? ''), 0, 10);
        if (!$this->validDate($start)) {
            $starts = array_column(array_filter($periods, fn ($period) => $period['status'] === 'free' && $this->validDate((string) $period['period_start'])), 'period_start');
            sort($starts); $start = $starts[0] ?? '';
        }
        $trialEnd = $start ? (new \DateTimeImmutable($start))->modify('+1 month')->format('Y-m-d') : null;
        $allowed = $trialEnd && $start <= $today && $today < $trialEnd;
        $reason = $allowed ? 'trial' : 'subscription_required'; $until = $allowed ? $trialEnd : null;
        foreach ($periods as $period) {
            if ($period['status'] === 'paid' && !empty($period['paid_at']) && $period['period_start'] <= $today && $today < $period['period_end']) {
                $allowed = true; $reason = 'paid'; $until = max($until ?? '', $period['period_end']);
            }
        }
        return ['academyId' => (int) $academy['academy_id'], 'allowed' => (bool) $allowed, 'reason' => $reason, 'trialEndsAt' => $trialEnd, 'validUntil' => $until];
    }

    private function validDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        return $parsed && $parsed->format('Y-m-d') === $date;
    }
}
