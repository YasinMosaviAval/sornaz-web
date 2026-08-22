<?php

namespace Modules\Academy\Services;

use Core\database\DB;
use Core\translation\TranslationService;
use RuntimeException;
use Modules\System\Services\UserNotificationService;

class AcademyBranchService {
    public function academyForUser(int $userId): array {
        $academy = DB::table('academies')->where('user_id', $userId)->whereNull('deleted_at')->first();
        if (!$academy) $academy = DB::table('academies')->where('created_by', $userId)->whereNull('deleted_at')->orderBy('academy_id')->first();
        if (!$academy) {
            $membership = DB::table('academy_branch_members')
                ->join('academy_branch_member_roles', 'academy_branch_member_roles.member_id', '=', 'academy_branch_members.member_id')
                ->where('academy_branch_members.user_id', $userId)
                ->where('academy_branch_member_roles.role_id', 7)
                ->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->first();
            if ($membership) $academy = DB::table('academies')->where('academy_id', (int)$membership['academy_id'])->whereNull('deleted_at')->first();
        }
        if (!$academy) throw new RuntimeException('آموزشگاه مرتبط یافت نشد.');
        return $academy;
    }

    public function bootstrap(int $ownerUserId, bool $siteAdmin = false): array {
        $this->ensureDefaultTypes($ownerUserId);
        if ($siteAdmin) {
            foreach (DB::table('academies')->whereNull('deleted_at')->get() as $academy) {
                $this->normalizeMain((int)$academy['academy_id'], $ownerUserId);
            }
            return [
                'branches' => $this->allBranches(),
                'academies' => $this->academies(),
                'read_only' => false,
                'site_admin' => true,
                'types' => $this->types(),
                'provinces' => DB::table('world_iran_provinces')->select('province_id', 'province_name')->get(),
                'counties' => DB::table('world_iran_counties')->select('county_id', 'county_name', 'province_id')->get(),
                'members' => $this->members(null),
                'manager_candidates' => $this->managerCandidates(null),
            ];
        }
        $branchAccount = $this->branchForUser($ownerUserId);
        $academy = $branchAccount
            ? DB::table('academies')->where('academy_id', (int)$branchAccount['academy_id'])->whereNull('deleted_at')->first()
            : $this->academyForUser($ownerUserId);
        if (!$academy) throw new RuntimeException('آموزشگاه مرتبط یافت نشد.');
        $this->normalizeMain((int)$academy['academy_id'], $ownerUserId);
        $this->repairRegisteredMainBranchIdentity($academy, $ownerUserId);
        return [
            'branches' => $branchAccount ? [$this->decorate($branchAccount)] : $this->branches((int)$academy['academy_id']),
            'academies' => [],
            'read_only' => false,
            'site_admin' => false,
            'branch_account' => (bool)$branchAccount,
            'can_create_branch' => !$branchAccount,
            'can_delete_branch' => !$branchAccount,
            'types' => $this->types(),
            'provinces' => DB::table('world_iran_provinces')->select('province_id', 'province_name')->get(),
            'counties' => DB::table('world_iran_counties')->select('county_id', 'county_name', 'province_id')->get(),
            'members' => $this->members((int)$academy['academy_id'], $branchAccount ? [(int)$branchAccount['branch_id']] : null),
            'manager_candidates' => $this->managerCandidates((int)$academy['academy_id']),
        ];
    }

    private function managerCandidates(?int $academyId): array {
        $query = DB::table('academy_branch_members')->join('users', 'users.user_id', '=', 'academy_branch_members.user_id')
            ->select('academy_branch_members.academy_id', 'academy_branch_members.user_id', 'users.username')
            ->whereNull('academy_branch_members.deleted_at')->whereNull('users.deleted_at');
        if ($academyId !== null) $query->where('academy_branch_members.academy_id', $academyId);
        $unique = [];
        foreach ($query->get() as $row) {
            $userId = (int)$row['user_id'];
            $key = (int)$row['academy_id'] . ':' . $userId;
            if (isset($unique[$key])) continue;
            $name = TranslationService::manager()->get('users', $userId, 'full_name', 'fa') ?: $row['username'] ?: ('کاربر ' . $userId);
            $unique[$key] = ['user_id'=>$userId, 'academy_id'=>(int)$row['academy_id'], 'name'=>$name];
        }
        return array_values($unique);
    }

    private function members(?int $academyId, ?array $branchIds = null): array {
        $this->syncTeacherLessons($academyId, $branchIds);
        $where = $academyId === null ? '' : ' AND b.academy_id = ' . (int)$academyId;
        if ($branchIds !== null) $where .= ' AND b.branch_id IN (' . ($branchIds ? implode(',', array_map('intval', $branchIds)) : '0') . ')';
        $statement = db()->prepare("SELECT m.member_id, m.user_id, m.branch_id, m.status, m.joined_at, u.username, u.phone, u.national_code, u.birthday, u.visibility, c.member_contract_id, c.type contract_type, c.user_lesson_id, c.start_date, c.end_date, c.price, c.currency_id, ul.lesson_id, ul.level_id FROM academy_branch_members m JOIN users u ON u.user_id=m.user_id JOIN academy_branches b ON b.branch_id=m.branch_id LEFT JOIN academy_branch_member_contracts c ON c.member_id=m.member_id AND c.deleted_at IS NULL LEFT JOIN user_lessons ul ON ul.user_lesson_id=c.user_lesson_id AND ul.deleted_at IS NULL WHERE m.deleted_at IS NULL AND u.deleted_at IS NULL AND b.deleted_at IS NULL{$where} ORDER BY m.member_id DESC");
        $statement->execute(); $rows = $statement->fetchAll();
        if (!$rows) return [];
        $userIds = array_values(array_unique(array_map(fn(array $r)=>(int)$r['user_id'], $rows)));
        $branchIds = array_values(array_unique(array_map(fn(array $r)=>(int)$r['branch_id'], $rows)));
        $names=[]; foreach (DB::table('translations')->where('table_name','users')->where('field','full_name')->where('locale','fa')->whereIn('table_id',$userIds)->get() as $t) $names[(int)$t['table_id']]=$t['value'];
        $branches=[]; foreach (DB::table('translations')->where('table_name','academy_branches')->where('field','name')->where('locale','fa')->whereIn('table_id',$branchIds)->get() as $t) $branches[(int)$t['table_id']]=$t['value'];
        $lessonIds=array_values(array_unique(array_filter(array_map(fn(array $r)=>(int)($r['lesson_id']??0),$rows))));
        $lessonNames=[]; if($lessonIds) foreach(DB::table('translations')->where('table_name','lessons')->where('field','title')->where('locale','fa')->whereIn('table_id',$lessonIds)->whereNull('deleted_at')->get() as $t)$lessonNames[(int)$t['table_id']]=$t['value'];
        return array_map(function(array $r) use($names,$branches,$lessonNames){
            $student=str_contains((string)$r['username'], self::STUDENT_USERNAME_MARKER);
            return ['id'=>(int)$r['member_id'],'user_id'=>(int)$r['user_id'],'name'=>$names[(int)$r['user_id']]??$r['username'],'phone'=>$r['phone']?:'',
                'nationalId'=>$r['national_code']?:'','birthDate'=>$r['birthday']?:'','branchId'=>(int)$r['branch_id'],'branch'=>$branches[(int)$r['branch_id']]??('شعبه '.$r['branch_id']),
                'type'=>$student?'student':($r['contract_type']?:'other'),'typeLabel'=>$student?'هنرجو':match($r['contract_type']){'teacher'=>'مدرس','receptionist'=>'پذیرش','manager'=>'مدیر',default=>'کارمند'},
                'contractTitle'=>$student?'قرارداد آموزشی':'قرارداد همکاری','contractDescription'=>'قرارداد ثبت‌شده در سامانه','startDate'=>$r['start_date']?:$r['joined_at'],'endDate'=>$r['end_date']?:'',
                'price'=>(float)($r['price']?:0),'currencyId'=>(int)($r['currency_id']?:1),'currency'=>'تومان','status'=>$r['status']==='active'?'فعال':'غیرفعال',
                'profileVisibility'=>$r['visibility']?:'private','userLessonId'=>(int)($r['user_lesson_id']??0),'lessonId'=>(int)($r['lesson_id']??0),'lessonName'=>$lessonNames[(int)($r['lesson_id']??0)]??'—','instrument'=>'','level'=>'','teacher'=>'','remaining'=>0,'financial'=>'تسویه','attendance'=>'—','registrationDate'=>$r['joined_at']?:''];
        },$rows);
    }

    private function syncTeacherLessons(?int $academyId, ?array $branchIds = null): void {
        $where=$academyId===null?'':' AND b.academy_id='.(int)$academyId;
        if($branchIds!==null)$where.=' AND b.branch_id IN ('.($branchIds?implode(',',array_map('intval',$branchIds)):'0').')';
        $rows=db()->query("SELECT c.member_contract_id,c.user_lesson_id,m.user_id,m.branch_id,b.user_id branch_user_id,c.created_by FROM academy_branch_member_contracts c JOIN academy_branch_members m ON m.member_id=c.member_id JOIN academy_branches b ON b.branch_id=m.branch_id WHERE c.type='teacher' AND c.deleted_at IS NULL AND m.deleted_at IS NULL AND b.deleted_at IS NULL{$where}")->fetchAll();
        foreach($rows as $index=>$row){
            $valid=$row['user_lesson_id']?DB::table('user_lessons')->where('user_lesson_id',(int)$row['user_lesson_id'])->where('user_id',(int)$row['user_id'])->whereNull('deleted_at')->first():null;
            if($valid)continue;
            $offered=DB::table('user_lessons')->where('user_id',(int)$row['branch_user_id'])->whereNull('deleted_at')->get();
            if(!$offered)continue;
            $source=$offered[$index%count($offered)];
            $teacherLesson=DB::table('user_lessons')->where('user_id',(int)$row['user_id'])->where('lesson_id',(int)$source['lesson_id'])->whereNull('deleted_at')->first();
            $actor=(int)($row['created_by']?:1);
            if($teacherLesson)$userLessonId=(int)$teacherLesson['user_lesson_id'];
            else $userLessonId=DB::table('user_lessons')->insertGetId(['user_id'=>(int)$row['user_id'],'lesson_id'=>(int)$source['lesson_id'],'level_id'=>$source['level_id']?:null,'start_date'=>$source['start_date']?:date('Y-m-d'),'is_primary'=>1,'created_by'=>$actor,'updated_by'=>$actor]);
            DB::table('academy_branch_member_contracts')->where('member_contract_id',(int)$row['member_contract_id'])->update(['user_lesson_id'=>$userLessonId,'updated_by'=>$actor]);
        }
    }

    private const STUDENT_USERNAME_MARKER = 'test_branch_member_student_';

    public function updateMember(int $actorId, int $memberId, array $data, bool $siteAdmin = false): array {
        return transaction(function() use($actorId,$memberId,$data,$siteAdmin){
            $member=DB::table('academy_branch_members')->where('member_id',$memberId)->whereNull('deleted_at')->first();
            if(!$member) throw new RuntimeException('عضو مورد نظر یافت نشد.');
            $branch=DB::table('academy_branches')->where('branch_id',(int)$member['branch_id'])->whereNull('deleted_at')->first();
            if(!$branch) throw new RuntimeException('شعبه عضو یافت نشد.');
            if(!$siteAdmin && !$this->canAccessBranch($actorId, (int)$branch['branch_id'])) throw new RuntimeException('دسترسی ویرایش این عضو را ندارید.');
            $userId=(int)$member['user_id'];
            DB::table('users')->where('user_id',$userId)->update(['phone'=>trim((string)($data['phone']??''))?:null,'national_code'=>trim((string)($data['nationalId']??''))?:null,
                'birthday'=>($data['birthDate']??'')?:null,'visibility'=>in_array(($data['profileVisibility']??''),['public','private','unlisted'],true)?$data['profileVisibility']:'private','updated_by'=>$actorId]);
            if(trim((string)($data['name']??''))!=='') TranslationService::manager()->set('users',$userId,'full_name',trim((string)$data['name']),'fa');
            $newBranch=(int)($data['branchId']??$member['branch_id']);
            if(!DB::table('academy_branches')->where('branch_id',$newBranch)->where('academy_id',(int)$branch['academy_id'])->whereNull('deleted_at')->first() || (!$siteAdmin && !$this->canAccessBranch($actorId, $newBranch)))$newBranch=(int)$member['branch_id'];
            DB::table('academy_branch_members')->where('member_id',$memberId)->update(['branch_id'=>$newBranch,'status'=>($data['status']??'فعال')==='فعال'?'active':'pending','updated_by'=>$actorId]);
            $contract=DB::table('academy_branch_member_contracts')->where('member_id',$memberId)->whereNull('deleted_at')->first();
            $type=($data['type']??'other');if(!in_array($type,['teacher','receptionist','manager'],true))$type='other';
            $values=['type'=>$type,'start_date'=>($data['startDate']??'')?:null,'end_date'=>($data['endDate']??'')?:null,'price'=>max(0,(float)($data['price']??0)),
                'currency_id'=>(int)($data['currencyId']??1),'updated_by'=>$actorId];
            if($type!=='teacher')$values['user_lesson_id']=null;
            if($contract)DB::table('academy_branch_member_contracts')->where('member_contract_id',(int)$contract['member_contract_id'])->update($values);
            else DB::table('academy_branch_member_contracts')->insertGetId(['member_id'=>$memberId,'created_by'=>$actorId]+$values);
            return ['member_id'=>$memberId];
        });
    }

    public function deleteMember(int $actorId,int $memberId,bool $siteAdmin=false): void {
        $member=DB::table('academy_branch_members')->where('member_id',$memberId)->whereNull('deleted_at')->first();if(!$member)throw new RuntimeException('عضو مورد نظر یافت نشد.');
        if(!$siteAdmin && !$this->canAccessBranch($actorId, (int)$member['branch_id']))throw new RuntimeException('دسترسی حذف این عضو را ندارید.');
        $now=date('Y-m-d H:i:s');DB::table('academy_branch_member_contracts')->where('member_id',$memberId)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actorId,'updated_by'=>$actorId]);
        DB::table('academy_branch_members')->where('member_id',$memberId)->update(['deleted_at'=>$now,'deleted_by'=>$actorId,'updated_by'=>$actorId]);
    }

    public function store(int $ownerUserId, array $data, bool $siteAdmin = false): array {
        return transaction(function () use ($ownerUserId, $data, $siteAdmin) {
            if (!$siteAdmin && $this->branchForUser($ownerUserId)) throw new RuntimeException('حساب شعبه اجازه ایجاد شعبه دیگری را ندارد.');
            $academy = $siteAdmin
                ? DB::table('academies')->where('academy_id', (int)($data['academy_id'] ?? 0))->whereNull('deleted_at')->first()
                : $this->academyForUser($ownerUserId);
            if (!$academy) throw new RuntimeException('آموزشگاه مقصد معتبر نیست.');
            $academyId = (int)$academy['academy_id'];
            DB::table('academies')->where('academy_id', $academyId)->update(['updated_by' => $ownerUserId]);
            $this->lockAcademy($academyId);
            $data = $this->validate($data);
            $hasBranches = DB::table('academy_branches')->where('academy_id', $academyId)->whereNull('deleted_at')->count() > 0;
            $isMain = !$hasBranches;

            $branchUserId = DB::table('users')->insertGetId([
                'username' => $data['username'],
                'email' => !empty($data['email']) ? strtolower(trim((string)$data['email'])) : null,
                'phone' => !empty($data['phone']) ? preg_replace('/\D+/', '', (string)$data['phone']) : null,
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'type' => 'branch', 'status' => $this->activeStatus($data['status'] ?? null) ? 'approved' : 'inactive',
                'locale' => 'fa', 'timezone' => 'Asia/Tehran', 'register_method' => !empty($data['email']) ? 'email' : 'phone',
                'visibility' => 'unlisted', 'created_by' => $ownerUserId, 'updated_by' => $ownerUserId,
                'approved_at' => date('Y-m-d H:i:s'), 'approved_by' => $ownerUserId,
            ]);
            if (!$branchUserId) throw new RuntimeException('ایجاد حساب شعبه ناموفق بود.');

            $branchId = DB::table('academy_branches')->insertGetId([
                'academy_id' => $academyId, 'user_id' => $branchUserId, 'is_main' => $isMain ? 1 : 0,
                'academy_branch_type_id' => $data['type_id'], 'mode' => $data['physical_type'],
                'timezone' => 'Asia/Tehran', 'created_by' => $ownerUserId, 'updated_by' => $ownerUserId,
            ]);
            if (!$branchId) throw new RuntimeException('ایجاد شعبه ناموفق بود.');
            $role = DB::table('access_system_roles')->where('name','academy_branch_owner')->whereNull('deleted_at')->first();
            if (!$role) throw new RuntimeException('نقش academy_branch_owner یافت نشد.');
            DB::table('user_roles')->insertGetId(['user_id'=>$branchUserId,'role_id'=>(int)$role['role_id'],'is_main'=>1,'granted_by'=>$ownerUserId,'created_by'=>$ownerUserId,'updated_by'=>$ownerUserId,'approved_at'=>date('Y-m-d H:i:s'),'approved_by'=>$ownerUserId]);
            $this->saveDetails($branchId, $branchUserId, $ownerUserId, $data);
            $notifications = app()->container()->make(UserNotificationService::class);
            $notifications->send(1, 'ثبت شعبه جدید در جدول academy_branches', 'انجام‌دهنده عملیات user_id=' . $ownerUserId . ' در جدول users سطر user_id=' . $branchUserId . ' و در جدول academy_branches سطر branch_id=' . $branchId . ' را اضافه کرد. ستون‌های users: username، email، phone، password، type، status، locale، timezone، register_method، visibility، created_by، updated_by. ستون‌های academy_branches: academy_id، user_id، is_main، academy_branch_type_id، mode، timezone، created_by، updated_by. همچنین ستون updated_by در جدول academies برای academy_id=' . $academyId . ' به‌روزرسانی شد.', 'academy_branches', $branchId, $ownerUserId);
            $notifications->send((int)$academy['user_id'], 'ثبت شعبه جدید آموزشگاه', 'انجام‌دهنده عملیات user_id=' . $ownerUserId . ' سطر branch_id=' . $branchId . ' را در جدول academy_branches و سطر user_id=' . $branchUserId . ' را در جدول users اضافه کرد. ستون updated_by در جدول academies برای academy_id=' . $academyId . ' نیز به‌روزرسانی شد.', 'academy_branches', $branchId, $ownerUserId);
            $notifications->send($branchUserId, 'ایجاد حساب کاربری شعبه', 'انجام‌دهنده عملیات user_id=' . $ownerUserId . ' سطر user_id=' . $branchUserId . ' را در جدول users و سطر branch_id=' . $branchId . ' را در جدول academy_branches اضافه کرد. نوع حساب در users.type برابر branch ثبت شد.', 'academy_branches', $branchId, $ownerUserId);
            return $this->findOwned($academyId, $branchId);
        });
    }

    public function update(int $ownerUserId, int $branchId, array $data, bool $siteAdmin = false): array {
        return transaction(function () use ($ownerUserId, $branchId, $data, $siteAdmin) {
            if (!$siteAdmin && !$this->canAccessBranch($ownerUserId, $branchId)) throw new RuntimeException('دسترسی ویرایش این شعبه را ندارید.');
            $globalBranch = ($siteAdmin || $this->branchForUser($ownerUserId)) ? DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first() : null;
            $academy = $globalBranch
                ? DB::table('academies')->where('academy_id', (int)$globalBranch['academy_id'])->whereNull('deleted_at')->first()
                : $this->academyForUser($ownerUserId);
            if (!$academy) throw new RuntimeException('آموزشگاه مرتبط یافت نشد.');
            $academyId = (int)$academy['academy_id'];
            $this->lockAcademy($academyId);
            $branch = $this->ownedRow($academyId, $branchId);
            $data['branch_user_id'] = (int)$branch['user_id'];
            $data = $this->validate($data);
            DB::table('academy_branches')->where('branch_id', $branchId)->update([
                'is_main' => (bool)$branch['is_main'] ? 1 : 0, 'academy_branch_type_id' => $data['type_id'],
                'mode' => $data['physical_type'], 'updated_by' => $ownerUserId,
            ]);
            DB::table('users')->where('user_id', (int)$branch['user_id'])->update([
                'username' => $data['username'], 'status' => $this->activeStatus($data['status'] ?? null) ? 'approved' : 'inactive', 'updated_by' => $ownerUserId,
            ]);
            $this->softDeleteDetails((int)$branch['user_id'], $ownerUserId);
            $this->saveDetails($branchId, (int)$branch['user_id'], $ownerUserId, $data);
            if (!empty($data['manager_user_id'])) $this->assignBranchManager($academyId, $branchId, (int)$data['manager_user_id'], $ownerUserId);
            return $this->findOwned($academyId, $branchId);
        });
    }

    public function delete(int $ownerUserId, int $branchId, bool $siteAdmin = false): void {
        transaction(function () use ($ownerUserId, $branchId, $siteAdmin) {
            if (!$siteAdmin && $this->branchForUser($ownerUserId)) throw new RuntimeException('حساب شعبه اجازه حذف شعبه را ندارد.');
            if (!$siteAdmin && !$this->canAccessBranch($ownerUserId, $branchId)) throw new RuntimeException('دسترسی حذف این شعبه را ندارید.');
            $globalBranch = ($siteAdmin || $this->branchForUser($ownerUserId)) ? DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first() : null;
            $academyId = $globalBranch ? (int)$globalBranch['academy_id'] : (int)$this->academyForUser($ownerUserId)['academy_id'];
            $this->lockAcademy($academyId);
            $branch = $this->ownedRow($academyId, $branchId);
            if ((bool)$branch['is_main']) throw new RuntimeException('حذف شعبه اصلی آموزشگاه امکان‌پذیر نیست.');
            $now = date('Y-m-d H:i:s');
            DB::table('academy_branches')->where('branch_id', $branchId)->update(['deleted_at' => $now, 'deleted_by' => $ownerUserId, 'updated_by' => $ownerUserId]);
            DB::table('users')->where('user_id', (int)$branch['user_id'])->update(['deleted_at' => $now, 'deleted_by' => $ownerUserId, 'updated_by' => $ownerUserId]);
            $this->softDeleteDetails((int)$branch['user_id'], $ownerUserId);
        });
    }

    public function addType(int $ownerUserId, array $data): array {
        $title = trim((string)($data['title'] ?? ''));
        $summary = trim((string)($data['summary'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));
        if ($title === '' || mb_strlen($title) > 100) throw new RuntimeException('عنوان نوع آموزشی معتبر نیست.');
        if ($summary === '' || mb_strlen($summary) > 500) throw new RuntimeException('خلاصه نوع آموزشی الزامی است و حداکثر ۵۰۰ نویسه دارد.');
        if ($description === '' || mb_strlen($description) > 5000) throw new RuntimeException('شرح نوع آموزشی الزامی است و حداکثر ۵۰۰۰ نویسه دارد.');
        return transaction(function () use ($ownerUserId, $title, $summary, $description) {
            foreach ($this->types() as $type) if ($type['name'] === $title) return $type;
            $id = DB::table('academy_branch_types')->insertGetId(['type' => 'other', 'created_by' => $ownerUserId, 'updated_by' => $ownerUserId]);
            $this->setTypeTranslations($id, ['title'=>$title,'summary'=>$summary,'description'=>$description], $ownerUserId);
            return ['id' => $id, 'name' => $title, 'title'=>$title, 'summary'=>$summary, 'description'=>$description];
        });
    }

    public function updateType(int $ownerUserId,int $id,array $data): array {
        $type=DB::table('academy_branch_types')->where('academy_branch_type_id',$id)->whereNull('deleted_at')->first();
        if(!$type) throw new RuntimeException('نوع آموزشی یافت نشد.');
        $title=trim((string)($data['title']??''));$summary=trim((string)($data['summary']??''));$description=trim((string)($data['description']??''));
        if($title===''||mb_strlen($title)>100)throw new RuntimeException('عنوان نوع آموزشی معتبر نیست.');
        if($summary===''||mb_strlen($summary)>500)throw new RuntimeException('خلاصه نوع آموزشی الزامی است و حداکثر ۵۰۰ نویسه دارد.');
        if($description===''||mb_strlen($description)>5000)throw new RuntimeException('شرح نوع آموزشی الزامی است و حداکثر ۵۰۰۰ نویسه دارد.');
        return transaction(function()use($ownerUserId,$id,$title,$summary,$description){
            foreach($this->types() as $row)if((int)$row['id']!==$id&&$row['name']===$title)throw new RuntimeException('نوع آموزشی دیگری با این عنوان وجود دارد.');
            DB::table('academy_branch_types')->where('academy_branch_type_id',$id)->update(['updated_by'=>$ownerUserId]);
            $this->setTypeTranslations($id,['title'=>$title,'summary'=>$summary,'description'=>$description],$ownerUserId);
            return ['id'=>$id,'name'=>$title,'title'=>$title,'summary'=>$summary,'description'=>$description];
        });
    }

    public function deleteType(int $ownerUserId,int $id): void {
        $type=DB::table('academy_branch_types')->where('academy_branch_type_id',$id)->whereNull('deleted_at')->first();
        if(!$type)throw new RuntimeException('نوع آموزشی یافت نشد.');
        if(DB::table('academy_branches')->where('academy_branch_type_id',$id)->whereNull('deleted_at')->count()>0)throw new RuntimeException('این نوع آموزشی توسط یک یا چند شعبه استفاده می‌شود و تا زمان تغییر نوع آن شعبه‌ها قابل حذف نیست.');
        $now=date('Y-m-d H:i:s');
        DB::table('academy_branch_types')->where('academy_branch_type_id',$id)->update(['deleted_at'=>$now,'deleted_by'=>$ownerUserId,'updated_by'=>$ownerUserId]);
        DB::table('translations')->where('table_name','academy_branch_types')->where('table_id',$id)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$ownerUserId,'updated_by'=>$ownerUserId]);
    }

    private function ensureDefaultTypes(int $ownerUserId): void {
        $defaults = [
            'music' => ['fa'=>['title'=>'موسیقی','summary'=>'آموزش تخصصی موسیقی و مهارت‌های وابسته.','description'=>'این نوع آموزشی شامل آموزش ساز، آواز، مبانی نظری موسیقی و دوره‌های عملی مرتبط است.'],'en'=>['title'=>'Music','summary'=>'Professional music education and related skills.','description'=>'This educational type includes instruments, singing, music theory, and related practical courses.']],
            'poetry' => ['fa'=>['title'=>'شعر و ادبیات','summary'=>'آموزش شعر، ادبیات و مهارت‌های نوشتاری.','description'=>'این نوع آموزشی دوره‌های شعر، ادبیات فارسی، نگارش خلاق، نقد و خوانش متون ادبی را پوشش می‌دهد.'],'en'=>['title'=>'Poetry and Literature','summary'=>'Education in poetry, literature, and writing skills.','description'=>'This educational type covers poetry, Persian literature, creative writing, criticism, and literary reading.']],
        ];
        foreach ($defaults as $type=>$locales) {
            $row=DB::table('academy_branch_types')->where('type',$type)->first();
            $values=['updated_by'=>$ownerUserId,'deleted_at'=>null,'deleted_by'=>null];
            if($row){$id=(int)$row['academy_branch_type_id'];DB::table('academy_branch_types')->where('academy_branch_type_id',$id)->update($values);}
            else $id=DB::table('academy_branch_types')->insertGetId(['type'=>$type,'created_by'=>$ownerUserId]+$values);
            foreach($locales as $locale=>$fields)$this->setTypeTranslations($id,$fields,$ownerUserId,$locale);
        }
    }

    private function setTypeTranslations(int $id,array $fields,int $owner,string $locale='fa'): void {
        $tr=TranslationService::manager();
        foreach($fields as $field=>$value) if(!$tr->set('academy_branch_types',$id,$field,$value,$locale)) throw new RuntimeException('ذخیره ترجمه نوع آموزشی ناموفق بود.');
        DB::table('translations')->where('table_name','academy_branch_types')->where('table_id',$id)->where('locale',$locale)->update(['created_by'=>$owner,'updated_by'=>$owner,'deleted_at'=>null,'deleted_by'=>null]);
    }

    private function branches(int $academyId): array {
        $rows = DB::table('academy_branches')->leftJoin('users', 'academy_branches.user_id', '=', 'users.user_id')
            ->select('academy_branches.branch_id', 'academy_branches.academy_id', 'academy_branches.user_id', 'academy_branches.is_main', 'academy_branches.academy_branch_type_id', 'academy_branches.mode', 'users.status')
            ->where('academy_branches.academy_id', $academyId)->whereNull('academy_branches.deleted_at')->latest('academy_branches.branch_id')->get();
        return array_map(fn($row) => $this->decorate($row), $rows);
    }

    private function branchForUser(int $userId): ?array {
        return DB::table('academy_branches')->where('user_id', $userId)->whereNull('deleted_at')->first();
    }

    private function repairRegisteredMainBranchIdentity(array $academy, int $actorId): void {
        $branch = DB::table('academy_branches')->where('academy_id', (int)$academy['academy_id'])->where('is_main', 1)->whereNull('deleted_at')->first();
        if (!$branch) return;
        $tr = TranslationService::manager();
        $academyName = $tr->get('academies', (int)$academy['academy_id'], 'title', 'fa')
            ?: $tr->get('users', (int)$academy['user_id'], 'full_name', 'fa')
            ?: 'آموزشگاه';
        $managerId = (int)($academy['created_by'] ?: $actorId);
        $manager = DB::table('users')->where('user_id', $managerId)->first();
        $managerName = $tr->get('users', $managerId, 'full_name', 'fa') ?: ($manager['username'] ?? ('کاربر ' . $managerId));
        $branchId = (int)$branch['branch_id'];
        $branchUserId = (int)$branch['user_id'];
        $name = $tr->get('academy_branches', $branchId, 'name', 'fa') ?: $tr->get('users', $branchUserId, 'full_name', 'fa') ?: ($academyName . ' - شعبه اصلی');
        if (!$tr->get('academy_branches', $branchId, 'name', 'fa')) $tr->set('academy_branches', $branchId, 'name', $name, 'fa');
        if (!$tr->get('academy_branches', $branchId, 'manager', 'fa')) $tr->set('academy_branches', $branchId, 'manager', $managerName, 'fa');
        if (!$tr->get('users', $branchUserId, 'full_name', 'fa')) $tr->set('users', $branchUserId, 'full_name', $name, 'fa');
        DB::table('academy_branches')->where('branch_id', $branchId)->update(['academy_branch_type_id'=>(int)($branch['academy_branch_type_id']?:1), 'mode'=>$branch['mode']?:'physical', 'updated_by'=>$actorId]);
    }

    private function canAccessBranch(int $actorId, int $branchId): bool {
        $branch = DB::table('academy_branches')->where('branch_id', $branchId)->whereNull('deleted_at')->first();
        if (!$branch) return false;
        $ownBranch = $this->branchForUser($actorId);
        if ($ownBranch) return (int)$ownBranch['branch_id'] === $branchId;
        try {
            return (int)$this->academyForUser($actorId)['academy_id'] === (int)$branch['academy_id'];
        } catch (RuntimeException) {
            return false;
        }
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
        $branchUser = DB::table('users')->where('user_id', $userId)->first();
        $academyId = (int)($row['academy_id'] ?? 0);
        $academy = $academyId ? DB::table('academies')->where('academy_id', $academyId)->first() : null;
        $academyUser = $academy ? DB::table('users')->where('user_id', (int)$academy['user_id'])->first() : null;
        $academyName = $academy
            ? ($tr->get('academies', $academyId, 'title', 'fa') ?: $tr->get('users', (int)$academy['user_id'], 'full_name', 'fa') ?: ($academyUser['username'] ?? 'آموزشگاه'))
            : 'بدون آموزشگاه';
        $type = $row['academy_branch_type_id'] ? DB::table('academy_branch_types')->where('academy_branch_type_id', (int)$row['academy_branch_type_id'])->first() : null;
        $typeName = $type ? ($tr->get('academy_branch_types', (int)$type['academy_branch_type_id'], 'title', 'fa') ?: $tr->get('academy_branch_types', (int)$type['academy_branch_type_id'], 'name', 'fa') ?: $this->typeLabel($type['type'])) : 'سایر';
        $contacts = DB::table('user_contacts')->where('user_id', $userId)->whereNull('deleted_at')->get();
        $phones = []; $links = [];
        foreach ($contacts as $contact) {
            $contactValue = $tr->get('user_contacts', (int)$contact['user_contact_id'], 'value', 'fa') ?: '';
            if (($contact['mode'] ?? '') === 'phone') $phones[] = ['number' => $contactValue, 'priority' => $contact['priority'] ?? 'primary', 'is_main' => (bool)($contact['is_main'] ?? false)];
            else $links[] = ['title' => $tr->get('user_contacts', (int)$contact['user_contact_id'], 'title', 'fa') ?: 'لینک', 'url' => $contactValue, 'mode' => $contact['mode'] ?? 'social', 'platform' => $contact['platform'] ?? 'other', 'priority' => $contact['priority'] ?? 'secondary', 'is_main' => (bool)($contact['is_main'] ?? false)];
        }
        if (!$phones && !empty($branchUser['phone'])) $phones[] = ['number'=>$branchUser['phone'], 'priority'=>'primary', 'is_main'=>true];
        if (!empty($branchUser['email']) && !array_filter($links, fn($link) => ($link['mode'] ?? '') === 'email')) {
            $links[] = ['title'=>'ایمیل', 'url'=>$branchUser['email'], 'mode'=>'email', 'platform'=>'email', 'priority'=>'primary', 'is_main'=>!$links];
        }
        $addresses = array_map(function ($address) use ($tr) {
            $province = $address['province_id'] ? DB::table('world_iran_provinces')->where('province_id', $address['province_id'])->first() : null;
            $county = $address['county_id'] ? DB::table('world_iran_counties')->where('county_id', $address['county_id'])->first() : null;
            return ['province' => $province['province_name'] ?? '', 'city' => $county['county_name'] ?? '', 'address' => $tr->get('user_addresses', (int)$address['address_id'], 'address', 'fa') ?: '', 'postal_code' => $address['postal_code'], 'lat' => $address['latitude'], 'lng' => $address['longitude'], 'is_main' => (bool)$address['is_main']];
        }, DB::table('user_addresses')->where('user_id', $userId)->whereNull('deleted_at')->get());
        $manager=$this->branchManager($branchId);
        return ['id' => $branchId, 'user_id'=>$userId, 'username'=>$branchUser['username']??'', 'email'=>$branchUser['email']??'', 'phone'=>$branchUser['phone']??'', 'academy_id' => $academyId, 'academy_name' => $academyName, 'name' => $tr->get('academy_branches', $branchId, 'name', 'fa') ?: $tr->get('users', $userId, 'full_name', 'fa') ?: 'شعبه', 'type' => $typeName, 'type_id' => $row['academy_branch_type_id'] ?? null, 'physical_type' => $row['mode'] ?? 'physical', 'is_main' => (bool)($row['is_main'] ?? false), 'slogan' => $tr->get('academy_branches', $branchId, 'slogan', 'fa') ?: '', 'short_description'=>$tr->get('academy_branches',$branchId,'short_description','fa')?:'', 'bio' => $tr->get('academy_branches', $branchId, 'description', 'fa') ?: '', 'manager_user_id'=>$manager['user_id']??null, 'manager' => $manager['name']??($tr->get('academy_branches', $branchId, 'manager', 'fa') ?: ''), 'classrooms' => DB::table('academy_branch_classrooms')->where('branch_id', $branchId)->whereNull('deleted_at')->count(), 'status' => ($row['status'] ?? null) === 'approved' ? 'فعال' : 'غیرفعال', 'phones' => $phones, 'links' => $links, 'addresses' => $addresses];
    }

    private function branchManager(int $branchId): ?array {
        $row=DB::table('academy_branch_members')->join('academy_branch_member_roles','academy_branch_member_roles.member_id','=','academy_branch_members.member_id')->join('users','users.user_id','=','academy_branch_members.user_id')->select('academy_branch_members.user_id','users.username')->where('academy_branch_members.branch_id',$branchId)->where('academy_branch_member_roles.role_id',16)->whereNull('academy_branch_members.deleted_at')->whereNull('academy_branch_member_roles.deleted_at')->orderBy('academy_branch_member_roles.member_role_id','DESC')->first();
        if(!$row)return null;$uid=(int)$row['user_id'];return['user_id'=>$uid,'name'=>TranslationService::manager()->get('users',$uid,'full_name','fa')?:$row['username']?:('کاربر '.$uid)];
    }

    private function assignBranchManager(int $academyId,int $branchId,int $managerUserId,int $actor): void {
        $candidate=DB::table('academy_branch_members')->where('academy_id',$academyId)->where('user_id',$managerUserId)->whereNull('deleted_at')->first();
        if(!$candidate)throw new RuntimeException('مدیر انتخاب‌شده عضو این آموزشگاه نیست.');
        $member=DB::table('academy_branch_members')->where('academy_id',$academyId)->where('branch_id',$branchId)->where('user_id',$managerUserId)->whereNull('deleted_at')->first();$now=date('Y-m-d H:i:s');
        if(!$member){$memberId=DB::table('academy_branch_members')->insertGetId(['academy_id'=>$academyId,'branch_id'=>$branchId,'user_id'=>$managerUserId,'status'=>'active','joined_at'=>date('Y-m-d'),'created_by'=>$actor,'updated_by'=>$actor,'approved_at'=>$now,'approved_by'=>$actor]);}else$memberId=(int)$member['member_id'];
        $branchMemberIds=array_map(fn($m)=>(int)$m['member_id'],DB::table('academy_branch_members')->where('academy_id',$academyId)->where('branch_id',$branchId)->whereNull('deleted_at')->get());
        if($branchMemberIds)DB::table('academy_branch_member_roles')->whereIn('member_id',$branchMemberIds)->where('role_id',16)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actor,'updated_by'=>$actor]);
        $role=DB::table('academy_branch_member_roles')->where('member_id',$memberId)->where('role_id',16)->first();$values=['is_main'=>1,'updated_by'=>$actor,'approved_at'=>$now,'approved_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];
        if($role)DB::table('academy_branch_member_roles')->where('member_role_id',(int)$role['member_role_id'])->update($values);else DB::table('academy_branch_member_roles')->insertGetId(['member_id'=>$memberId,'role_id'=>16,'created_by'=>$actor]+$values);
        $name=TranslationService::manager()->get('users',$managerUserId,'full_name','fa')?:('کاربر '.$managerUserId);$this->setTranslations('academy_branches',$branchId,['manager'=>$name],$actor);
    }

    private function types(): array {
        $tr = TranslationService::manager();
        return array_map(function($row)use($tr){$id=(int)$row['academy_branch_type_id'];$title=$tr->get('academy_branch_types',$id,'title','fa')?:$tr->get('academy_branch_types',$id,'name','fa')?:$this->typeLabel($row['type']);return ['id'=>$id,'name'=>$title,'title'=>$title,'summary'=>$tr->get('academy_branch_types',$id,'summary','fa')?:'','description'=>$tr->get('academy_branch_types',$id,'description','fa')?:''];}, DB::table('academy_branch_types')->whereNull('deleted_at')->get());
    }

    private function typeLabel(?string $type): string { return ['music'=>'موسیقی','poetry'=>'شعر و ادبیات','painting'=>'نقاشی','hybrid'=>'ترکیبی','other'=>'سایر'][$type ?? 'other'] ?? 'سایر'; }
    private function activeStatus(mixed $status): bool { return in_array(trim((string)$status), ['active', 'approved', 'فعال'], true); }

    private function validate(array $data): array {
        if (trim((string)($data['name'] ?? '')) === '') throw new RuntimeException('نام شعبه الزامی است.');
        if (!in_array($data['physical_type'] ?? '', ['online','physical','hybrid'], true)) throw new RuntimeException('نوع ارائه معتبر نیست.');
        $type = DB::table('academy_branch_types')->where('academy_branch_type_id', (int)($data['type_id'] ?? 0))->whereNull('deleted_at')->first();
        if (!$type) throw new RuntimeException('نوع آموزشی معتبر نیست.');
        $username=trim((string)($data['username']??''));if(!preg_match('/^[A-Za-z0-9_]{3,100}$/',$username))throw new RuntimeException('نام کاربری شعبه معتبر نیست.');
        $usernameOwner=DB::table('users')->where('username',$username)->whereNull('deleted_at')->first();
        if($usernameOwner&&!empty($data['branch_user_id'])&&(int)$usernameOwner['user_id']!==(int)$data['branch_user_id'])throw new RuntimeException('این نام کاربری قبلاً ثبت شده است.');
        if($usernameOwner&&empty($data['branch_user_id']))throw new RuntimeException('این نام کاربری قبلاً ثبت شده است.');
        if(empty($data['branch_user_id'])){$email=strtolower(trim((string)($data['email']??'')));$phone=preg_replace('/\D+/','',(string)($data['phone']??''));if($email===''&&$phone==='')throw new RuntimeException('ایمیل یا شماره همراه شعبه الزامی است.');if($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('ایمیل شعبه معتبر نیست.');if($phone!==''&&!preg_match('/^09\d{9}$/',$phone))throw new RuntimeException('شماره همراه شعبه معتبر نیست.');if($email!==''&&DB::table('users')->where('email',$email)->whereNull('deleted_at')->first())throw new RuntimeException('این ایمیل قبلاً ثبت شده است.');if($phone!==''&&DB::table('users')->where('phone',$phone)->whereNull('deleted_at')->first())throw new RuntimeException('این شماره همراه قبلاً ثبت شده است.');if(strlen((string)($data['password']??''))<8)throw new RuntimeException('رمز عبور شعبه باید حداقل ۸ کاراکتر باشد.');if(!hash_equals((string)$data['password'],(string)($data['password2']??'')))throw new RuntimeException('تکرار رمز عبور شعبه مطابقت ندارد.');$data['email']=$email?:null;$data['phone']=$phone?:null;}
        $data['type_id'] = (int)$data['type_id']; $data['name'] = trim($data['name']);
        $data['username']=$username;$data['short_description']=mb_substr(trim((string)($data['short_description']??'')),0,500);
        $data['phones'] = is_array($data['phones'] ?? null) ? $data['phones'] : []; $data['links'] = is_array($data['links'] ?? null) ? $data['links'] : []; $data['addresses'] = is_array($data['addresses'] ?? null) ? $data['addresses'] : [];
        return $data;
    }

    private function saveDetails(int $branchId, int $userId, int $ownerUserId, array $data): void {
        $this->setTranslations('academy_branches', $branchId, ['name'=>$data['name'],'slogan'=>$data['slogan'] ?? '','short_description'=>$data['short_description']??'','description'=>$data['bio'] ?? '','manager'=>$data['manager'] ?? ''], $ownerUserId);
        $this->setTranslations('users', $userId, ['full_name'=>$data['name'],'short_description'=>$data['short_description']??'','biography'=>$data['bio']??''], $ownerUserId);
        foreach ($data['phones'] as $phone) if (trim((string)($phone['number'] ?? '')) !== '') { $id=DB::table('user_contacts')->insertGetId(['user_id'=>$userId,'mode'=>'phone','platform'=>'other','priority'=>$phone['priority'] ?? 'primary','is_main'=>!empty($phone['is_main'])?1:0,'status'=>'active','created_by'=>$ownerUserId,'updated_by'=>$ownerUserId]); $this->setTranslations('user_contacts',$id,['value'=>trim($phone['number'])],$ownerUserId); }
        foreach ($data['links'] as $link) if (trim((string)($link['url'] ?? '')) !== '') { $id=DB::table('user_contacts')->insertGetId(['user_id'=>$userId,'mode'=>in_array($link['mode'] ?? '', ['email','social'],true)?$link['mode']:'social','platform'=>$link['platform'] ?? 'other','priority'=>$link['priority'] ?? 'secondary','is_main'=>!empty($link['is_main'])?1:0,'status'=>'active','created_by'=>$ownerUserId,'updated_by'=>$ownerUserId]); $this->setTranslations('user_contacts',$id,['title'=>$link['title'] ?? 'لینک','value'=>trim($link['url'])],$ownerUserId); }
        foreach ($data['addresses'] as $address) { $province=DB::table('world_iran_provinces')->where('province_name',$address['province'] ?? '')->first(); $county=DB::table('world_iran_counties')->where('county_name',$address['city'] ?? '')->first(); $id=DB::table('user_addresses')->insertGetId(['user_id'=>$userId,'country_id'=>1,'province_id'=>$province['province_id'] ?? null,'county_id'=>$county['county_id'] ?? null,'is_main'=>!empty($address['is_main'])?1:0,'latitude'=>($address['lat'] ?? '') !== '' ? $address['lat'] : null,'longitude'=>($address['lng'] ?? '') !== '' ? $address['lng'] : null,'postal_code'=>$address['postal_code'] ?? null,'created_by'=>$ownerUserId,'updated_by'=>$ownerUserId]); $this->setTranslations('user_addresses',$id,['address'=>$address['address'] ?? ''],$ownerUserId); }
    }

    private function setTranslations(string $table,int $id,array $values,int $owner): void { $tr=TranslationService::manager(); foreach($values as $field=>$value)$tr->set($table,$id,$field,$value,'fa'); DB::table('translations')->where('table_name',$table)->where('table_id',$id)->update(['created_by'=>$owner,'updated_by'=>$owner]); }
    private function normalizeMain(int $academyId,int $owner): void { $branches=DB::table('academy_branches')->where('academy_id',$academyId)->whereNull('deleted_at')->orderBy('branch_id')->get(); if(!$branches)return; $main=(int)$branches[0]['branch_id']; foreach($branches as $branch){$expected=(int)$branch['branch_id']===$main?1:0;if((int)$branch['is_main']!==$expected)DB::table('academy_branches')->where('branch_id',(int)$branch['branch_id'])->update(['is_main'=>$expected,'updated_by'=>$owner]);} }
    private function lockAcademy(int $academyId): void { $statement=db()->prepare('SELECT academy_id FROM academies WHERE academy_id = ? FOR UPDATE'); $statement->execute([$academyId]); }
    private function ownedRow(int $academyId,int $branchId): array { $row=DB::table('academy_branches')->where('academy_id',$academyId)->where('branch_id',$branchId)->whereNull('deleted_at')->first(); if(!$row)throw new RuntimeException('شعبه یافت نشد.'); return $row; }
    private function findOwned(int $academyId,int $branchId): array { return $this->decorate($this->ownedRow($academyId,$branchId)); }
    private function softDeleteDetails(int $userId,int $owner): void { $now=date('Y-m-d H:i:s'); DB::table('user_contacts')->where('user_id',$userId)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$owner,'updated_by'=>$owner]); DB::table('user_addresses')->where('user_id',$userId)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$owner,'updated_by'=>$owner]); }
}
