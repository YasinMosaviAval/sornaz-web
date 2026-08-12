<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use RuntimeException;

class AcademyBranchService {
    public function academyForUser(int $userId): array {
        $academy = DB::table('academies')->where('user_id', $userId)->whereNull('deleted_at')->first();
        if (!$academy) throw new RuntimeException('آموزشگاه مرتبط یافت نشد.');
        return $academy;
    }

    public function bootstrap(int $ownerUserId, bool $siteAdmin = false): array {
        if ($siteAdmin) {
            return [
                'branches' => $this->allBranches(),
                'academies' => $this->academies(),
                'read_only' => false,
                'site_admin' => true,
                'types' => $this->types(),
                'provinces' => DB::table('world_iran_provinces')->select('province_id', 'province_name')->get(),
                'counties' => DB::table('world_iran_counties')->select('county_id', 'county_name', 'province_id')->get(),
            ];
        }
        $academy = $this->academyForUser($ownerUserId);
        $this->normalizeMain((int)$academy['academy_id'], $ownerUserId);
        return [
            'branches' => $this->branches((int)$academy['academy_id']),
            'academies' => [],
            'read_only' => false,
            'site_admin' => false,
            'types' => $this->types(),
            'provinces' => DB::table('world_iran_provinces')->select('province_id', 'province_name')->get(),
            'counties' => DB::table('world_iran_counties')->select('county_id', 'county_name', 'province_id')->get(),
        ];
    }

    public function store(int $ownerUserId, array $data, bool $siteAdmin = false): array {
        return transaction(function () use ($ownerUserId, $data, $siteAdmin) {
            $academy = $siteAdmin
                ? DB::table('academies')->where('academy_id', (int)($data['academy_id'] ?? 0))->whereNull('deleted_at')->first()
                : $this->academyForUser($ownerUserId);
            if (!$academy) throw new RuntimeException('آموزشگاه مقصد معتبر نیست.');
            $academyId = (int)$academy['academy_id'];
            $this->lockAcademy($academyId);
            $data = $this->validate($data);
            $hasBranches = DB::table('academy_branches')->where('academy_id', $academyId)->whereNull('deleted_at')->count() > 0;
            $isMain = !$hasBranches || !empty($data['is_main']);

            $branchUserId = DB::table('users')->insertGetId([
                'username' => 'branch_' . $academyId . '_' . bin2hex(random_bytes(5)),
                'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                'type' => 'branch', 'status' => $this->activeStatus($data['status'] ?? null) ? 'approved' : 'inactive',
                'locale' => 'fa', 'timezone' => 'Asia/Tehran', 'register_method' => 'admin',
                'visibility' => 'unlisted', 'created_by' => $ownerUserId, 'updated_by' => $ownerUserId,
            ]);
            if (!$branchUserId) throw new RuntimeException('ایجاد حساب شعبه ناموفق بود.');

            if ($isMain) $this->clearMain($academyId, $ownerUserId);
            $branchId = DB::table('academy_branches')->insertGetId([
                'academy_id' => $academyId, 'user_id' => $branchUserId, 'is_main' => $isMain ? 1 : 0,
                'academy_branch_type_id' => $data['type_id'], 'mode' => $data['physical_type'],
                'timezone' => 'Asia/Tehran', 'created_by' => $ownerUserId, 'updated_by' => $ownerUserId,
            ]);
            if (!$branchId) throw new RuntimeException('ایجاد شعبه ناموفق بود.');
            $this->saveDetails($branchId, $branchUserId, $ownerUserId, $data);
            return $this->findOwned($academyId, $branchId);
        });
    }

    public function update(int $ownerUserId, int $branchId, array $data, bool $siteAdmin = false): array {
        return transaction(function () use ($ownerUserId, $branchId, $data, $siteAdmin) {
            $globalBranch = $siteAdmin ? DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first() : null;
            $academy = $siteAdmin && $globalBranch
                ? DB::table('academies')->where('academy_id', (int)$globalBranch['academy_id'])->whereNull('deleted_at')->first()
                : $this->academyForUser($ownerUserId);
            if (!$academy) throw new RuntimeException('آموزشگاه مرتبط یافت نشد.');
            $academyId = (int)$academy['academy_id'];
            $this->lockAcademy($academyId);
            $branch = $this->ownedRow($academyId, $branchId);
            $data = $this->validate($data);
            $isMain = !empty($data['is_main']) || (bool)$branch['is_main'];
            if (!empty($data['is_main'])) $this->clearMain($academyId, $ownerUserId, $branchId);

            DB::table('academy_branches')->where('branch_id', $branchId)->update([
                'is_main' => $isMain ? 1 : 0, 'academy_branch_type_id' => $data['type_id'],
                'mode' => $data['physical_type'], 'updated_by' => $ownerUserId,
            ]);
            DB::table('users')->where('user_id', (int)$branch['user_id'])->update([
                'status' => $this->activeStatus($data['status'] ?? null) ? 'approved' : 'inactive', 'updated_by' => $ownerUserId,
            ]);
            $this->softDeleteDetails((int)$branch['user_id'], $ownerUserId);
            $this->saveDetails($branchId, (int)$branch['user_id'], $ownerUserId, $data);
            return $this->findOwned($academyId, $branchId);
        });
    }

    public function delete(int $ownerUserId, int $branchId, bool $siteAdmin = false): void {
        transaction(function () use ($ownerUserId, $branchId, $siteAdmin) {
            $globalBranch = $siteAdmin ? DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first() : null;
            $academyId = $globalBranch ? (int)$globalBranch['academy_id'] : (int)$this->academyForUser($ownerUserId)['academy_id'];
            $this->lockAcademy($academyId);
            $branch = $this->ownedRow($academyId, $branchId);
            $now = date('Y-m-d H:i:s');
            DB::table('academy_branches')->where('branch_id', $branchId)->update(['deleted_at' => $now, 'deleted_by' => $ownerUserId, 'updated_by' => $ownerUserId]);
            DB::table('users')->where('user_id', (int)$branch['user_id'])->update(['deleted_at' => $now, 'deleted_by' => $ownerUserId, 'updated_by' => $ownerUserId]);
            $this->softDeleteDetails((int)$branch['user_id'], $ownerUserId);
            if ((bool)$branch['is_main']) {
                $replacement = DB::table('academy_branches')->where('academy_id', $academyId)->whereNull('deleted_at')->first();
                if ($replacement) DB::table('academy_branches')->where('branch_id', (int)$replacement['branch_id'])->update(['is_main' => 1, 'updated_by' => $ownerUserId]);
            }
        });
    }

    public function addType(int $ownerUserId, string $name): array {
        $name = trim($name);
        if ($name === '' || mb_strlen($name) > 100) throw new RuntimeException('نام نوع آموزشی معتبر نیست.');
        return transaction(function () use ($ownerUserId, $name) {
            foreach ($this->types() as $type) if ($type['name'] === $name) return $type;
            $id = DB::table('academy_branch_types')->insertGetId(['type' => 'other', 'created_by' => $ownerUserId, 'updated_by' => $ownerUserId]);
            $this->setTranslations('academy_branch_types', $id, ['name' => $name], $ownerUserId);
            return ['id' => $id, 'name' => $name];
        });
    }

    private function branches(int $academyId): array {
        $rows = DB::table('academy_branches')->leftJoin('users', 'academy_branches.user_id', '=', 'users.user_id')
            ->select('academy_branches.branch_id', 'academy_branches.academy_id', 'academy_branches.user_id', 'academy_branches.is_main', 'academy_branches.academy_branch_type_id', 'academy_branches.mode', 'users.status')
            ->where('academy_branches.academy_id', $academyId)->whereNull('academy_branches.deleted_at')->latest('academy_branches.branch_id')->get();
        return array_map(fn($row) => $this->decorate($row), $rows);
    }

    private function allBranches(): array {
        $rows = DB::table('academy_branches')->leftJoin('users', 'academy_branches.user_id', '=', 'users.user_id')
            ->select('academy_branches.branch_id', 'academy_branches.academy_id', 'academy_branches.user_id', 'academy_branches.is_main', 'academy_branches.academy_branch_type_id', 'academy_branches.mode', 'users.status')
            ->whereNull('academy_branches.deleted_at')->latest('academy_branches.branch_id')->get();
        return array_map(fn($row) => $this->decorate($row), $rows);
    }

    private function academies(): array {
        $tr = TranslationService::manager();
        return array_map(function (array $academy) use ($tr) {
            $academyId = (int)$academy['academy_id'];
            $user = DB::table('users')->where('user_id', (int)$academy['user_id'])->first();
            return [
                'id' => $academyId,
                'name' => $tr->get('academies', $academyId, 'title', 'fa')
                    ?: $tr->get('users', (int)$academy['user_id'], 'full_name', 'fa')
                    ?: ($user['username'] ?? 'آموزشگاه'),
            ];
        }, DB::table('academies')->whereNull('deleted_at')->latest('academy_id')->get());
    }

    private function decorate(array $row): array {
        $branchId = (int)$row['branch_id']; $userId = (int)$row['user_id']; $tr = TranslationService::manager();
        $academyId = (int)($row['academy_id'] ?? 0);
        $academy = $academyId ? DB::table('academies')->where('academy_id', $academyId)->first() : null;
        $academyUser = $academy ? DB::table('users')->where('user_id', (int)$academy['user_id'])->first() : null;
        $academyName = $academy
            ? ($tr->get('academies', $academyId, 'title', 'fa') ?: $tr->get('users', (int)$academy['user_id'], 'full_name', 'fa') ?: ($academyUser['username'] ?? 'آموزشگاه'))
            : 'بدون آموزشگاه';
        $type = $row['academy_branch_type_id'] ? DB::table('academy_branch_types')->where('academy_branch_type_id', (int)$row['academy_branch_type_id'])->first() : null;
        $typeName = $type ? ($tr->get('academy_branch_types', (int)$type['academy_branch_type_id'], 'name', 'fa') ?: $this->typeLabel($type['type'])) : 'سایر';
        $contacts = DB::table('user_contacts')->where('user_id', $userId)->whereNull('deleted_at')->get();
        $phones = []; $links = [];
        foreach ($contacts as $contact) {
            $contactValue = $tr->get('user_contacts', (int)$contact['user_contact_id'], 'value', 'fa') ?: '';
            if (($contact['mode'] ?? '') === 'phone') $phones[] = ['number' => $contactValue, 'priority' => $contact['priority'] ?? 'primary', 'is_main' => (bool)($contact['is_main'] ?? false)];
            else $links[] = ['title' => $tr->get('user_contacts', (int)$contact['user_contact_id'], 'title', 'fa') ?: 'لینک', 'url' => $contactValue, 'mode' => $contact['mode'] ?? 'social', 'platform' => $contact['platform'] ?? 'other', 'priority' => $contact['priority'] ?? 'secondary', 'is_main' => (bool)($contact['is_main'] ?? false)];
        }
        $addresses = array_map(function ($address) use ($tr) {
            $province = $address['province_id'] ? DB::table('world_iran_provinces')->where('province_id', $address['province_id'])->first() : null;
            $county = $address['county_id'] ? DB::table('world_iran_counties')->where('county_id', $address['county_id'])->first() : null;
            return ['province' => $province['province_name'] ?? '', 'city' => $county['county_name'] ?? '', 'address' => $tr->get('user_addresses', (int)$address['address_id'], 'address', 'fa') ?: '', 'postal_code' => $address['postal_code'], 'lat' => $address['latitude'], 'lng' => $address['longitude'], 'is_main' => (bool)$address['is_main']];
        }, DB::table('user_addresses')->where('user_id', $userId)->whereNull('deleted_at')->get());
        return ['id' => $branchId, 'academy_id' => $academyId, 'academy_name' => $academyName, 'name' => $tr->get('academy_branches', $branchId, 'name', 'fa') ?: $tr->get('users', $userId, 'full_name', 'fa') ?: 'شعبه', 'type' => $typeName, 'type_id' => $row['academy_branch_type_id'] ?? null, 'physical_type' => $row['mode'] ?? 'physical', 'is_main' => (bool)($row['is_main'] ?? false), 'slogan' => $tr->get('academy_branches', $branchId, 'slogan', 'fa') ?: '', 'bio' => $tr->get('academy_branches', $branchId, 'description', 'fa') ?: '', 'manager' => $tr->get('academy_branches', $branchId, 'manager', 'fa') ?: '', 'classrooms' => DB::table('academy_branch_classrooms')->where('branch_id', $branchId)->whereNull('deleted_at')->count(), 'status' => ($row['status'] ?? null) === 'approved' ? 'فعال' : 'غیرفعال', 'phones' => $phones, 'links' => $links, 'addresses' => $addresses];
    }

    private function types(): array {
        $tr = TranslationService::manager();
        return array_map(fn($row) => ['id' => (int)$row['academy_branch_type_id'], 'name' => $tr->get('academy_branch_types', (int)$row['academy_branch_type_id'], 'name', 'fa') ?: $this->typeLabel($row['type'])], DB::table('academy_branch_types')->whereNull('deleted_at')->get());
    }

    private function typeLabel(?string $type): string { return ['music'=>'موسیقی','poetry'=>'شعر و ادبیات','painting'=>'نقاشی','hybrid'=>'ترکیبی','other'=>'سایر'][$type ?? 'other'] ?? 'سایر'; }
    private function activeStatus(mixed $status): bool { return in_array(trim((string)$status), ['active', 'approved', 'فعال'], true); }

    private function validate(array $data): array {
        if (trim((string)($data['name'] ?? '')) === '') throw new RuntimeException('نام شعبه الزامی است.');
        if (!in_array($data['physical_type'] ?? '', ['online','physical','hybrid'], true)) throw new RuntimeException('نوع ارائه معتبر نیست.');
        $type = DB::table('academy_branch_types')->where('academy_branch_type_id', (int)($data['type_id'] ?? 0))->whereNull('deleted_at')->first();
        if (!$type) throw new RuntimeException('نوع آموزشی معتبر نیست.');
        $data['type_id'] = (int)$data['type_id']; $data['name'] = trim($data['name']);
        $data['phones'] = is_array($data['phones'] ?? null) ? $data['phones'] : []; $data['links'] = is_array($data['links'] ?? null) ? $data['links'] : []; $data['addresses'] = is_array($data['addresses'] ?? null) ? $data['addresses'] : [];
        return $data;
    }

    private function saveDetails(int $branchId, int $userId, int $ownerUserId, array $data): void {
        $this->setTranslations('academy_branches', $branchId, ['name'=>$data['name'],'slogan'=>$data['slogan'] ?? '','description'=>$data['bio'] ?? '','manager'=>$data['manager'] ?? ''], $ownerUserId);
        $this->setTranslations('users', $userId, ['full_name'=>$data['name']], $ownerUserId);
        foreach ($data['phones'] as $phone) if (trim((string)($phone['number'] ?? '')) !== '') { $id=DB::table('user_contacts')->insertGetId(['user_id'=>$userId,'mode'=>'phone','platform'=>'other','priority'=>$phone['priority'] ?? 'primary','is_main'=>!empty($phone['is_main'])?1:0,'status'=>'active','created_by'=>$ownerUserId,'updated_by'=>$ownerUserId]); $this->setTranslations('user_contacts',$id,['value'=>trim($phone['number'])],$ownerUserId); }
        foreach ($data['links'] as $link) if (trim((string)($link['url'] ?? '')) !== '') { $id=DB::table('user_contacts')->insertGetId(['user_id'=>$userId,'mode'=>in_array($link['mode'] ?? '', ['email','social'],true)?$link['mode']:'social','platform'=>$link['platform'] ?? 'other','priority'=>$link['priority'] ?? 'secondary','is_main'=>!empty($link['is_main'])?1:0,'status'=>'active','created_by'=>$ownerUserId,'updated_by'=>$ownerUserId]); $this->setTranslations('user_contacts',$id,['title'=>$link['title'] ?? 'لینک','value'=>trim($link['url'])],$ownerUserId); }
        foreach ($data['addresses'] as $address) { $province=DB::table('world_iran_provinces')->where('province_name',$address['province'] ?? '')->first(); $county=DB::table('world_iran_counties')->where('county_name',$address['city'] ?? '')->first(); $id=DB::table('user_addresses')->insertGetId(['user_id'=>$userId,'country_id'=>1,'province_id'=>$province['province_id'] ?? null,'county_id'=>$county['county_id'] ?? null,'is_main'=>!empty($address['is_main'])?1:0,'latitude'=>($address['lat'] ?? '') !== '' ? $address['lat'] : null,'longitude'=>($address['lng'] ?? '') !== '' ? $address['lng'] : null,'postal_code'=>$address['postal_code'] ?? null,'created_by'=>$ownerUserId,'updated_by'=>$ownerUserId]); $this->setTranslations('user_addresses',$id,['address'=>$address['address'] ?? ''],$ownerUserId); }
    }

    private function setTranslations(string $table,int $id,array $values,int $owner): void { $tr=TranslationService::manager(); foreach($values as $field=>$value)$tr->set($table,$id,$field,$value,'fa'); DB::table('translations')->where('table_name',$table)->where('table_id',$id)->update(['created_by'=>$owner,'updated_by'=>$owner]); }
    private function clearMain(int $academyId,int $owner,int $except=0): void { $query=DB::table('academy_branches')->where('academy_id',$academyId)->whereNull('deleted_at'); if($except)$query->where('branch_id','!=',$except); $query->update(['is_main'=>0,'updated_by'=>$owner]); }
    private function normalizeMain(int $academyId,int $owner): void { $branches=DB::table('academy_branches')->where('academy_id',$academyId)->whereNull('deleted_at')->get(); if(!$branches)return; $main=null; foreach($branches as $branch){if((bool)$branch['is_main']&&$main===null)$main=(int)$branch['branch_id'];} $main??=(int)$branches[0]['branch_id']; foreach($branches as $branch){$expected=(int)$branch['branch_id']===$main?1:0;if((int)$branch['is_main']!==$expected)DB::table('academy_branches')->where('branch_id',(int)$branch['branch_id'])->update(['is_main'=>$expected,'updated_by'=>$owner]);} }
    private function lockAcademy(int $academyId): void { $statement=db()->prepare('SELECT academy_id FROM academies WHERE academy_id = ? FOR UPDATE'); $statement->execute([$academyId]); }
    private function ownedRow(int $academyId,int $branchId): array { $row=DB::table('academy_branches')->where('academy_id',$academyId)->where('branch_id',$branchId)->whereNull('deleted_at')->first(); if(!$row)throw new RuntimeException('شعبه یافت نشد.'); return $row; }
    private function findOwned(int $academyId,int $branchId): array { return $this->decorate($this->ownedRow($academyId,$branchId)); }
    private function softDeleteDetails(int $userId,int $owner): void { $now=date('Y-m-d H:i:s'); DB::table('user_contacts')->where('user_id',$userId)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$owner,'updated_by'=>$owner]); DB::table('user_addresses')->where('user_id',$userId)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$owner,'updated_by'=>$owner]); }
}
