<?php
namespace Modules\Academy\Services;

use Core\database\DB;
use Modules\System\Services\SiteAdminAccess;
use Core\translation\TranslationService;
use RuntimeException;

final class AcademyDiscountService
{
    public function ready(): bool
    {
        try { db()->query('SELECT discount_id FROM f_financial_system_discount_organizations LIMIT 0'); return true; }
        catch (\PDOException $error) {
            if (in_array((string) $error->getCode(), ['42S02','HY000'], true) && (str_contains($error->getMessage(), 'exist') || str_contains($error->getMessage(), 'no such table'))) return false;
            throw $error;
        }
    }

    public function bootstrap(int $actor): array
    {
        $catalog = (new AcademyTermService())->discountOrganizationCatalog($actor);
        $ready = $this->ready();
        return ['discounts'=>$this->rows($actor, $catalog['visible'], $ready), 'organizations'=>$catalog['manageable'], 'ready'=>$ready];
    }

    private function rows(int $actor, array $organizations, bool $ready): array
    {
        $site = SiteAdminAccess::allows(DB::table('users')->where('user_id', $actor)->whereNull('deleted_at')->first());
        $ids = array_column($organizations, 'user_id');
        $where = $site ? '1=1' : 'd.created_by=?';
        $parameters = $site ? [] : [$actor];
        if ($ready && !$site) {
            $where = '(o.discount_id IS NULL AND d.created_by=?)';
            if ($ids) { $where .= ' OR o.organization_user_id IN (' . implode(',', array_fill(0,count($ids),'?')) . ')'; array_push($parameters, ...$ids); }
        }
        $sql = 'SELECT d.*,' . ($ready ? 'o.organization_user_id' : 'NULL AS organization_user_id') . ' FROM financial_system_discounts d ' . ($ready ? 'LEFT JOIN f_financial_system_discount_organizations o ON o.discount_id=d.discount_id ' : '') . 'WHERE d.deleted_at IS NULL AND (' . $where . ') ORDER BY d.discount_id DESC';
        $query = db()->prepare($sql); $query->execute($parameters);
        $map = array_column($organizations, null, 'user_id');
        return array_map(function ($row) use ($map, $actor, $site) {
            $owner = $map[(int) ($row['organization_user_id'] ?? 0)] ?? null;
            $id = (int) $row['discount_id'];
            return ['id'=>$id, 'name'=>TranslationService::manager()->get('financial_system_discounts',$id,'title',locale()) ?: $row['code'], 'type'=>$row['type'], 'value'=>(float)$row['value'], 'status'=>$row['status'], 'canManage'=>$site || ($owner ? empty($owner['read_only']) : (int)$row['created_by']===$actor), 'startDate'=>$row['start_date'] ?? null, 'endDate'=>$row['end_date'] ?? null, 'organizationUserId'=>(int)($row['organization_user_id'] ?? 0), 'organizationKind'=>$owner['kind'] ?? 'legacy', 'branchId'=>$owner && $owner['kind']==='branch' ? (int)$owner['id'] : null, 'organizationName'=>$owner['name'] ?? (locale()==='en' ? 'Unassigned' : 'بدون سازمان')];
        }, $query->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function save(int $actor, array $data, int $id = 0): array
    {
        if (!$this->ready()) throw new RuntimeException('ابتدا به‌روزرسانی دیتابیس تخفیف‌های سازمانی را اجرا کنید.');
        $owner = $this->saveOwner($actor, $data, $id);
        $title = trim((string)($data['title'] ?? '')); $type=(string)($data['type'] ?? 'percentage');
        $value = $data['value'] ?? null;
        if ($title==='' || mb_strlen($title)>190 || !in_array($type,['percentage','fixed'],true) || !is_numeric($value) || !is_finite((float)$value) || (float)$value<=0 || ($type==='percentage' && (float)$value>100) || (float)$value>999999999999) throw new RuntimeException('عنوان، نوع یا مقدار تخفیف معتبر نیست.');
        return transaction(function () use ($actor,$owner,$title,$type,$value,$id) {
            if ($id) DB::table('financial_system_discounts')->where('discount_id',$id)->whereNull('deleted_at')->update(['type'=>$type,'value'=>$value,'updated_by'=>$actor]);
            else $id=DB::table('financial_system_discounts')->insertGetId(['code'=>'ADMIN-'.strtoupper(bin2hex(random_bytes(8))), 'type'=>$type, 'value'=>$value, 'status'=>'active', 'start_date'=>date('Y-m-d'), 'created_by'=>$actor, 'updated_by'=>$actor]);
            if (!DB::table('f_financial_system_discount_organizations')->where('discount_id',$id)->first()) DB::table('f_financial_system_discount_organizations')->insert(['discount_id'=>$id,'organization_user_id'=>(int)$owner['user_id'],'created_by'=>$actor]);
            TranslationService::manager()->set('financial_system_discounts',$id,'title',$title,locale());
            return ['id'=>(int)$id,'name'=>$title,'type'=>$type,'value'=>(float)$value,'organizationUserId'=>(int)$owner['user_id']];
        });
    }

    private function saveOwner(int $actor, array $data, int $id): array
    {
        $existing = $id ? $this->managed($actor,$id) : null;
        if ($existing && $existing['organizationUserId']) {
            if (!empty($data['organizationUserId']) && (int)$data['organizationUserId']!==$existing['organizationUserId']) throw new RuntimeException('تغییر سازمان تخفیف مجاز نیست.',403);
            $data['organizationUserId']=$existing['organizationUserId'];
        }
        $organizations = (new AcademyTermService())->discountOrganizationCatalog($actor)['manageable'];
        if (empty($data['organizationUserId']) && count($organizations)===1) $data['organizationUserId']=$organizations[0]['user_id'];
        $owner = null;
        foreach ($organizations as $organization) if ((int)$organization['user_id']===(int)($data['organizationUserId'] ?? 0)) $owner=$organization;
        if (!$owner) throw new RuntimeException('سازمان انتخاب‌شده خارج از محدوده دسترسی شما است.',403);
        return $owner;
    }

    private function managed(int $actor, int $id): array
    {
        foreach ($this->bootstrap($actor)['discounts'] as $row) if ($row['id']===$id && $row['canManage']) return $row;
        throw new RuntimeException('تخفیف در دسترس نیست.',403);
    }

    public function delete(int $actor, int $id): void
    {
        $this->managed($actor,$id);
        DB::table('financial_system_discounts')->where('discount_id',$id)->whereNull('deleted_at')->update(['deleted_at'=>date('Y-m-d H:i:s'),'deleted_by'=>$actor,'updated_by'=>$actor]);
    }

    public function assertAvailable(int $actor, int $id, int $organizationUserId): void
    {
        if (!$id) return;
        $catalog=$this->bootstrap($actor);
        $owners=[$organizationUserId];
        $branch=DB::table('academy_branches')->where('user_id',$organizationUserId)->whereNull('deleted_at')->first();
        if ($branch) { $academy=DB::table('academies')->where('academy_id',(int)$branch['academy_id'])->whereNull('deleted_at')->first(); if ($academy) $owners[]=(int)$academy['user_id']; }
        foreach ($catalog['discounts'] as $discount) if ($discount['id']===$id && $discount['status']==='active' && (!$discount['startDate'] || $discount['startDate']<=date('Y-m-d')) && (!$discount['endDate'] || $discount['endDate']>=date('Y-m-d')) && (!$discount['organizationUserId'] || in_array($discount['organizationUserId'],$owners,true))) return;
        throw new RuntimeException('تخفیف برای سازمان این ترم قابل استفاده نیست.',403);
    }
}
