<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class AdminTestDataService {
    private const USERNAME_PREFIX = 'test_academy_manager_';
    private const TOTAL = 50;

    public function seedAcademyManagers(): array {
        return transaction(function () {
            $catalog = $this->syncMusicCatalog();
            $passwordHash = password_hash('123456789', PASSWORD_DEFAULT);
            $created = 0;
            $updated = 0;

            foreach ($this->people() as $index => $person) {
                $number = $index + 1;
                $username = self::USERNAME_PREFIX . sprintf('%02d', $number);
                $existing = DB::table('users')->where('username', $username)->first();
                $createdAt = $existing['created_at'] ?? date('Y-m-d H:i:s', strtotime('-' . (540 - $index * 9) . ' days'));
                $values = $this->userValues($person, $index, $createdAt, $passwordHash);

                if ($existing) {
                    $userId = (int)$existing['user_id'];
                    DB::table('users')->where('user_id', $userId)->update($values);
                    $updated++;
                } else {
                    $userId = DB::table('users')->insertGetId(['username' => $username] + $values);
                    if (!$userId) throw new RuntimeException('ایجاد کاربر آزمایشی شماره ' . $number . ' ناموفق بود.');
                    $created++;
                }

                DB::table('users')->where('user_id', $userId)->update([
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
                $this->setFullNameTranslation($userId, $person['name'], $createdAt);
                $this->syncAddresses($userId, $index, $createdAt);
                $this->syncContacts($userId, $index, $username, $createdAt);
                $this->syncMusicExperience($userId, $index, $createdAt, $catalog);
                $this->syncDefaultUserMedia($userId, $index, $createdAt);
                $this->syncUserAvailability($userId, $index, $createdAt);
            }
            $this->syncEnglishTestTranslations();
            $this->syncAdminFrameworkTranslations();

            return [
                'created' => $created,
                'updated' => $updated,
                'total' => self::TOTAL,
                'message' => "تست مدیران آموزشگاه تکمیل شد: {$created} کاربر ایجاد و {$updated} کاربر آزمایشی همگام‌سازی شد.",
            ];
        });
    }

    private function syncEnglishTestTranslations(): void {
        $users=DB::table('users')->whereRaw("username LIKE '".self::USERNAME_PREFIX."%'")->get();$userIds=array_map(fn(array $u)=>(int)$u['user_id'],$users);
        if(!$userIds)return;
        $targets=['users'=>$userIds];
        foreach(['user_addresses'=>'address_id','user_contacts'=>'user_contact_id','user_instruments'=>'user_instrument_id','user_lessons'=>'user_lesson_id','user_availabilities'=>'user_availability_id','user_availability_exceptions'=>'user_availability_exception_id'] as $table=>$key){$rows=DB::table($table)->whereIn('user_id',$userIds)->get();$targets[$table]=array_map(fn(array $r)=>(int)$r[$key],$rows);}
        foreach($targets as $table=>$ids){if(!$ids)continue;$rows=DB::table('translations')->where('table_name',$table)->whereIn('table_id',$ids)->where('locale','fa')->whereNull('deleted_at')->get();foreach($rows as $row)$this->setTranslation($table,(int)$row['table_id'],(int)($row['created_by']?:1),(string)$row['field'],$this->englishTestValue($table,(string)$row['field'],(string)$row['value']),(string)$row['created_at'],(string)$row['updated_at'],'en');}
        foreach(['instruments'=>'instrument_id','lessons'=>'lesson_id','levels'=>'level_id'] as $table=>$key){$rows=DB::table($table)->whereNull('deleted_at')->get();$ids=array_map(fn(array $r)=>(int)$r[$key],$rows);if(!$ids)continue;foreach(DB::table('translations')->where('table_name',$table)->whereIn('table_id',$ids)->where('locale','fa')->whereNull('deleted_at')->get() as $row)$this->setTranslation($table,(int)$row['table_id'],1,(string)$row['field'],$this->englishTestValue($table,(string)$row['field'],(string)$row['value']),(string)$row['created_at'],(string)$row['updated_at'],'en');}
    }

    private function englishTestValue(string $table,string $field,string $value): string {
        $names=['علی'=>'Ali','مریم'=>'Maryam','رضا'=>'Reza','سارا'=>'Sara','امیر'=>'Amir','نگار'=>'Negar','حسین'=>'Hossein','الهام'=>'Elham','مهدی'=>'Mehdi','نرگس'=>'Narges','محمد'=>'Mohammad','لیلا'=>'Leila','سعید'=>'Saeed','مهسا'=>'Mahsa','آرش'=>'Arash','نازنین'=>'Nazanin','محمدی'=>'Mohammadi','احمدی'=>'Ahmadi','رضایی'=>'Rezaei','کریمی'=>'Karimi','حسینی'=>'Hosseini','مرادی'=>'Moradi','قاسمی'=>'Ghasemi','اکبری'=>'Akbari','صادقی'=>'Sadeghi','نوری'=>'Nouri'];
        if($table==='users'&&$field==='full_name'){foreach($names as $fa=>$en)$value=str_replace($fa,$en,$value);return $value;}
        $titles=['تار'=>'Tar','سه‌تار'=>'Setar','سنتور'=>'Santur','پیانو'=>'Piano','ویولن'=>'Violin','نی'=>'Ney','دف'=>'Daf','تنبک'=>'Tombak','هارمونی'=>'Harmony','سلفژ'=>'Solfège','تئوری موسیقی'=>'Music Theory','خیلی تازه‌کار'=>'Absolute Beginner','تازه‌کار'=>'Beginner','مقدماتی'=>'Elementary','پایه'=>'Foundation','متوسط'=>'Intermediate','متوسط رو به بالا'=>'Upper Intermediate','نیمه‌پیشرفته'=>'Pre-advanced','پیشرفته'=>'Advanced','حرفه‌ای'=>'Professional'];
        if($field==='title'&&isset($titles[$value]))return $titles[$value];
        if($field==='title')return 'Music course or instrument: '.substr(hash('sha1',$value),0,8);
        if($field==='summary')return match($table){'levels'=>'A concise description of this learning level.','user_availabilities'=>'Recurring or date-specific availability for this member.','user_availability_exceptions'=>'A scheduled leave, holiday, or availability exception.','user_instruments'=>'A concise summary of the member’s experience with this instrument.','user_lessons'=>'A concise summary of the member’s experience in this lesson.',default=>'Concise English information for this record.'};
        if($field==='description')return 'Detailed English description for this music education record, including its background, purpose, and relevant experience.';
        if($field==='address')return 'Sample registered address for the academy manager in Iran.';
        if($field==='note')return 'Additional sample notes for this record.';
        return $value;
    }

    private function syncAdminFrameworkTranslations(): void {
        foreach($this->adminUiDictionary() as $index=>$pair){$key='admin.ui.'.substr(hash('sha1',$pair[0]),0,16);$setting=DB::table('f_settings')->where('variable_name',$key)->first();$values=['page'=>'admin','sort_order'=>($index%250),'table_name'=>'admin_ui','value'=>null,'status'=>'active','updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>1,'deleted_at'=>null,'deleted_by'=>null];if($setting){$id=(int)$setting['setting_id'];DB::table('f_settings')->where('setting_id',$id)->update($values);}else{$id=DB::table('f_settings')->insertGetId(['variable_name'=>$key,'created_by'=>1]+$values);}foreach(['fa'=>$pair[0],'en'=>$pair[1]] as $locale=>$text){$tr=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('locale',$locale)->where('field','value')->first();$tv=['value'=>$text,'version'=>1,'updated_at'=>date('Y-m-d H:i:s'),'updated_by'=>1,'deleted_at'=>null,'deleted_by'=>null];if($tr)DB::table('f_translations')->where('translation_id',(int)$tr['translation_id'])->update($tv);else DB::table('f_translations')->insert(['table_name'=>'f_settings','table_id'=>$id,'locale'=>$locale,'field'=>'value','created_by'=>1]+$tv);}}
    }

    private function adminUiDictionary(): array {return [
        ['داشبورد','Dashboard'],['حساب کاربری','Account'],['شعبه‌ها','Branches'],['نقش‌ها و دسترسی‌ها','Roles & Access'],['کاربران','Users'],['نقش‌ها','Roles'],['دسترسی‌ها','Permissions'],['گالری','Gallery'],['کاور','Cover'],['لوگو','Logo'],['ویدیو معرفی','Introduction Video'],['مجموعه عکس‌ها و ویدیوها','Photo & Video Collection'],['پرسنل','Staff'],['هنرجویان','Students'],['کلاس‌ها','Classrooms'],['سازها','Instruments'],['درس‌ها','Lessons'],['دوره‌ها','Courses'],['ترم‌ها','Terms'],['برنامه زمانی','Scheduling'],['قوانین زمانبندی','Scheduling Rules'],['برنامه زمانی شعبه‌ها','Branch Schedules'],['برنامه زمانی اعضا','Member Schedules'],['تعطیلات و مرخصی‌ها','Holidays & Leaves'],['برنامه زمانی کلاس‌ها','Class Schedules'],['امور مالی','Finance'],['گزارش‌ها','Reports'],['مرکز تست‌ها','Test Center'],['خروج','Logout'],['افزودن','Add'],['ویرایش','Edit'],['حذف','Delete'],['ذخیره','Save'],['ذخیره تغییرات','Save Changes'],['انصراف','Cancel'],['بستن','Close'],['جستجو','Search'],['همه','All'],['فعال','Active'],['غیرفعال','Inactive'],['در انتظار تأیید','Pending Approval'],['جزئیات','Details'],['نام','Name'],['عنوان','Title'],['توضیحات','Description'],['خلاصه','Summary'],['وضعیت','Status'],['تاریخ','Date'],['ساعت','Time'],['روز','Day'],['شعبه','Branch'],['عضو','Member'],['نقش','Role'],['منطقه زمانی','Timezone'],['دوره تکرار','Repeat Period'],['قبلی','Previous'],['بعدی','Next'],['اول','First'],['آخر','Last'],['بله','Yes'],['خیر','No'],['افزودن زمان‌بندی عضو','Add Member Schedule'],['ویرایش زمان‌بندی عضو','Edit Member Schedule'],['افزودن تعطیل / مرخصی','Add Holiday / Leave'],['ویرایش تعطیل / مرخصی','Edit Holiday / Leave'],['برنامه سرناز','Sornaz Application'],['پنل مدیریت سایت','Site Admin Panel'],['پنل مدیریت آموزشگاه','Academy Admin Panel'],['مدیر ارشد','Senior Administrator'],['تم','Theme'],['زبان','Language'],['روشن','Light'],['تیره','Dark']
    ];}

    public function adminUiMap(string $locale): array {
        $map=[];foreach($this->adminUiDictionary() as $pair)$map[$pair[0]]=$locale==='en'?$pair[1]:$pair[0];
        $rows=DB::table('f_settings')->whereNull('deleted_at')->get();
        foreach($rows as $row){$id=(int)$row['setting_id'];$fa=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('field','value')->where('locale','fa')->whereNull('deleted_at')->first();$selected=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('field','value')->where('locale',$locale)->whereNull('deleted_at')->first();if($fa&&$selected)$map[(string)$fa['value']]=(string)$selected['value'];}
        return $map;
    }

    public function inlineTranslationCatalog(): array {
        $catalog=[];
        foreach(DB::table('f_settings')->whereNull('deleted_at')->get() as $setting){
            $id=(int)$setting['setting_id'];$values=[];
            foreach(DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('field','value')->whereNull('deleted_at')->get() as $translation)$values[(string)$translation['locale']]=(string)$translation['value'];
            $catalog[]=['key'=>(string)$setting['variable_name'],'fa'=>$values['fa']??'','en'=>$values['en']??''];
        }
        return $catalog;
    }

    public function saveInlineTranslation(string $key,string $fa,string $en,int $actorId): array {
        $key=trim($key);$fa=trim($fa);$en=trim($en);
        if(strlen($key)>100||!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*(?:\.[a-zA-Z0-9_-]+)+$/',$key))throw new RuntimeException('کلید ترجمه معتبر نیست.');
        if($fa===''||$en==='')throw new RuntimeException('متن فارسی و انگلیسی هر دو الزامی هستند.');
        if(mb_strlen($fa)>5000||mb_strlen($en)>5000)throw new RuntimeException('متن ترجمه بیش از حد طولانی است.');
        return transaction(function()use($key,$fa,$en,$actorId){$now=date('Y-m-d H:i:s');$setting=DB::table('f_settings')->where('variable_name',$key)->whereNull('deleted_at')->first();$isAdmin=str_starts_with($key,'admin.');$base=['page'=>$isAdmin?'admin':'site','table_name'=>$isAdmin?'admin_ui':'site_ui','status'=>'active','updated_at'=>$now,'updated_by'=>$actorId,'deleted_at'=>null,'deleted_by'=>null];if($setting){$id=(int)$setting['setting_id'];DB::table('f_settings')->where('setting_id',$id)->update($base);}else{$id=DB::table('f_settings')->insertGetId(['variable_name'=>$key,'sort_order'=>999,'created_at'=>$now,'created_by'=>$actorId]+$base);}if(!$id)throw new RuntimeException('ایجاد کلید ترجمه در دیتابیس ناموفق بود.');foreach(['fa'=>$fa,'en'=>$en] as $locale=>$value){$translation=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('field','value')->where('locale',$locale)->first();$values=['value'=>$value,'version'=>1,'updated_at'=>$now,'updated_by'=>$actorId,'deleted_at'=>null,'deleted_by'=>null];if($translation)DB::table('f_translations')->where('translation_id',(int)$translation['translation_id'])->update($values);else DB::table('f_translations')->insert(['table_name'=>'f_settings','table_id'=>$id,'field'=>'value','locale'=>$locale,'created_at'=>$now,'created_by'=>$actorId]+$values);} $stored=[];foreach(['fa','en'] as $locale){$row=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('field','value')->where('locale',$locale)->whereNull('deleted_at')->first();$stored[$locale]=(string)($row['value']??'');}if($stored['fa']!==$fa||$stored['en']!==$en)throw new RuntimeException('تأیید ذخیره ترجمه در دیتابیس ناموفق بود.');return ['success'=>true,'key'=>$key,'fa'=>$stored['fa'],'en'=>$stored['en'],'message'=>'ترجمه با موفقیت ذخیره شد.'];});
    }

    public function statistics(): array {
        $users = DB::table('users')->whereRaw("username LIKE '" . self::USERNAME_PREFIX . "%'")->get();
        $userIds = array_map(fn(array $user) => (int)$user['user_id'], $users);
        $stats = [
            'total' => count($users),
            'addresses' => $userIds ? DB::table('user_addresses')->whereIn('user_id', $userIds)->count() : 0,
            'contacts' => $userIds ? DB::table('user_contacts')->whereIn('user_id', $userIds)->count() : 0,
            'instruments' => $userIds ? DB::table('user_instruments')->whereIn('user_id', $userIds)->count() : 0,
            'lessons' => $userIds ? DB::table('user_lessons')->whereIn('user_id', $userIds)->count() : 0,
            'media' => $userIds ? DB::table('media_files')->whereIn('user_id', $userIds)->count() : 0,
            'availabilities' => $userIds ? DB::table('user_availabilities')->whereIn('user_id', $userIds)->whereNull('deleted_at')->count() : 0,
            'exceptions' => $userIds ? DB::table('user_availability_exceptions')->whereIn('user_id', $userIds)->whereNull('deleted_at')->count() : 0,
            'catalog_instruments' => DB::table('instruments')->whereNull('deleted_at')->count(),
            'catalog_lessons' => DB::table('lessons')->whereNull('deleted_at')->count(),
            'levels' => DB::table('levels')->whereNull('deleted_at')->count(),
            'pending' => 0, 'approved' => 0, 'other' => 0,
        ];
        $academyUsers = DB::table('users')->whereRaw("username LIKE 'test_academy_%' AND username NOT LIKE 'test_academy_manager_%'")->get();
        $academyUserIds = array_map(fn(array $user) => (int)$user['user_id'], $academyUsers);
        $sampleAcademies = $academyUserIds ? DB::table('academies')->whereIn('user_id', $academyUserIds)->get() : [];
        $academyIds = array_map(fn(array $academy) => (int)$academy['academy_id'], $sampleAcademies);
        $stats['academies'] = count($sampleAcademies);
        $stats['branches'] = $academyIds ? DB::table('academy_branches')->whereIn('academy_id', $academyIds)->whereNull('deleted_at')->count() : 0;
        $stats['academy_managers'] = count($users);
        $stats['academy_memberships'] = $academyIds
            ? DB::table('academy_branch_members')->whereIn('created_by', $userIds)->whereNull('deleted_at')->count()
            : 0;
        $extraBranchUsers = DB::table('users')->whereRaw("username LIKE 'test_extra_branch_%'")->get();
        $extraBranchUserIds = array_map(fn(array $user)=>(int)$user['user_id'], $extraBranchUsers);
        $extraBranches = $extraBranchUserIds ? DB::table('academy_branches')->whereIn('user_id',$extraBranchUserIds)->whereNull('deleted_at')->get() : [];
        $extraBranchIds = array_map(fn(array $branch)=>(int)$branch['branch_id'], $extraBranches);
        $networkUsers = DB::table('users')->whereRaw("username LIKE 'test_branch_member_%'")->get();
        $networkUserIds = array_map(fn(array $user)=>(int)$user['user_id'], $networkUsers);
        $networkMembers = $networkUserIds ? DB::table('academy_branch_members')->whereIn('user_id',$networkUserIds)->whereNull('deleted_at')->get() : [];
        $networkMemberIds = array_map(fn(array $member)=>(int)$member['member_id'], $networkMembers);
        $stats['extra_branches'] = count($extraBranches);
        $stats['network_teachers'] = 0; $stats['network_receptionists'] = 0; $stats['network_employees'] = 0; $stats['network_managers'] = 0; $stats['network_students'] = 0;
        if ($networkMemberIds) foreach (DB::table('academy_branch_member_contracts')->whereIn('member_id',$networkMemberIds)->whereNull('deleted_at')->get() as $contract) {
            $member = current(array_filter($networkMembers, fn(array $m)=>(int)$m['member_id']===(int)$contract['member_id']));
            $user = $member ? current(array_filter($networkUsers, fn(array $u)=>(int)$u['user_id']===(int)$member['user_id'])) : null;
            if ($user && str_contains((string)$user['username'],'_student_')) $stats['network_students']++;
            elseif ($contract['type']==='teacher') $stats['network_teachers']++;
            elseif ($contract['type']==='receptionist') $stats['network_receptionists']++;
            elseif ($contract['type']==='manager') $stats['network_managers']++;
            else $stats['network_employees']++;
        }
        $stats['network_memberships'] = count($networkMembers);
        $stats['network_contracts'] = $networkMemberIds ? DB::table('academy_branch_member_contracts')->whereIn('member_id',$networkMemberIds)->whereNull('deleted_at')->count() : 0;
        foreach ($users as $user) {
            $status = $user['status'] ?? '';
            if ($status === 'pending') $stats['pending']++;
            elseif ($status === 'approved') $stats['approved']++;
            else $stats['other']++;
        }
        return $stats;
    }

    public function scheduleFixtures(): array {
        $users=DB::table('users')->whereRaw("username LIKE '" . self::USERNAME_PREFIX . "%'")->get(); $names=[];
        foreach($users as $user)$names[(int)$user['user_id']]=$this->translatedValue('users',(int)$user['user_id'],'full_name')?:$user['username'];
        $days=['saturday'=>'شنبه','sunday'=>'یکشنبه','monday'=>'دوشنبه','tuesday'=>'سه‌شنبه','wednesday'=>'چهارشنبه','thursday'=>'پنجشنبه','friday'=>'جمعه'];
        $repeat=['week'=>'هفتگی','2-week'=>'دو هفته','3-week'=>'سه هفته','4-week'=>'چهار هفته','month'=>'ماهانه','year'=>'سالانه','none'=>'بی‌تکرار'];
        $status=['available'=>'فعال','unavailable'=>'غیرفعال','reserved'=>'پر شده','pending'=>'در انتظار تأیید'];
        $schedules=[];$exceptions=[];
        foreach($users as $user){$uid=(int)$user['user_id'];
            foreach(DB::table('user_availabilities')->where('user_id',$uid)->whereNull('deleted_at')->get() as $row){$id=(int)$row['user_availability_id'];$schedules[]=['id'=>$id,'memberId'=>$uid,'name'=>$names[$uid],'role'=>'مدیر','day'=>$row['date']?:($days[$row['day_of_week']]??'—'),'timeLabel'=>substr((string)$row['start_time'],0,5).'-'.substr((string)$row['end_time'],0,5),'time'=>substr((string)$row['start_time'],0,5).'-'.substr((string)$row['end_time'],0,5),'branchId'=>0,'branchName'=>'محل ثبت‌شده کاربر','status'=>$status[$row['type']]??'فعال','repeatPeriod'=>$repeat[$row['repeat_period']]??'هفتگی','repeatDate'=>$row['date']?:'','timezone'=>$row['timezone'],'summary'=>$this->translatedValue('user_availabilities',$id,'summary'),'description'=>$this->translatedValue('user_availabilities',$id,'description')];}
            foreach(DB::table('user_availability_exceptions')->where('user_id',$uid)->whereNull('deleted_at')->get() as $row){$id=(int)$row['user_availability_exception_id'];$typeLabels=['holiday'=>'تعطیل رسمی','closed'=>'تعطیل','unavailable'=>'مرخصی','busy'=>'ماموریت','vacation'=>'مرخصی','blocked'=>'عدم حضور'];$label=$row['start_time']?substr((string)$row['start_time'],0,5).'-'.substr((string)$row['end_time'],0,5):'تمام‌روز';$exceptions[]=['id'=>$id,'memberId'=>$uid,'name'=>$names[$uid],'date'=>$row['date'],'timeLabel'=>$label,'time'=>$label,'branchId'=>0,'branchName'=>'محل ثبت‌شده کاربر','status'=>'فعال','type'=>$row['type'],'typeLabel'=>$typeLabels[$row['type']]??'عدم حضور','timezone'=>'Asia/Tehran','summary'=>$this->translatedValue('user_availability_exceptions',$id,'summary'),'description'=>$this->translatedValue('user_availability_exceptions',$id,'description')];}
        }
        return ['schedules'=>$schedules,'exceptions'=>$exceptions];
    }

    public function deleteAvailability(int $id, int $actorId): array {
        return transaction(function() use($id,$actorId){
            $row=DB::table('user_availabilities')->where('user_availability_id',$id)->whereNull('deleted_at')->first();
            if(!$row)throw new RuntimeException('برنامه زمانی موردنظر یافت نشد.');
            $now=date('Y-m-d H:i:s');
            DB::table('translations')->where('table_name','user_availabilities')->where('table_id',$id)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actorId,'updated_at'=>$now,'updated_by'=>$actorId]);
            DB::table('user_availabilities')->where('user_availability_id',$id)->update(['deleted_at'=>$now,'deleted_by'=>$actorId,'updated_at'=>$now,'updated_by'=>$actorId]);
            return ['success'=>true,'id'=>$id,'message'=>'برنامه زمانی حذف شد.'];
        });
    }

    public function deleteAvailabilityException(int $id, int $actorId): array {
        return transaction(function() use($id,$actorId){
            $row=DB::table('user_availability_exceptions')->where('user_availability_exception_id',$id)->whereNull('deleted_at')->first();
            if(!$row)throw new RuntimeException('تعطیلی یا مرخصی موردنظر یافت نشد.');
            $now=date('Y-m-d H:i:s');
            DB::table('translations')->where('table_name','user_availability_exceptions')->where('table_id',$id)->whereNull('deleted_at')->update(['deleted_at'=>$now,'deleted_by'=>$actorId,'updated_at'=>$now,'updated_by'=>$actorId]);
            DB::table('user_availability_exceptions')->where('user_availability_exception_id',$id)->update(['deleted_at'=>$now,'deleted_by'=>$actorId,'updated_at'=>$now,'updated_by'=>$actorId]);
            return ['success'=>true,'id'=>$id,'message'=>'تعطیلی یا مرخصی حذف شد.'];
        });
    }

    public function deleteAcademyManagers(): array {
        return transaction(function () {
            $users = DB::table('users')->whereRaw("username LIKE '" . self::USERNAME_PREFIX . "%'")->get();
            $userIds = array_map(fn(array $user) => (int)$user['user_id'], $users);
            if (!$userIds) {
                return ['deleted' => 0, 'message' => 'هیچ مدیر آموزشگاه آزمایشی برای حذف وجود ندارد.'];
            }

            $addresses = DB::table('user_addresses')->whereIn('user_id', $userIds)->get();
            $addressIds = array_map(fn(array $address) => (int)$address['address_id'], $addresses);
            if ($addressIds) {
                DB::table('translations')->where('table_name', 'user_addresses')->whereIn('table_id', $addressIds)->delete();
                DB::table('user_addresses')->whereIn('address_id', $addressIds)->delete();
            }
            $contacts = DB::table('user_contacts')->whereIn('user_id', $userIds)->get();
            $contactIds = array_map(fn(array $contact) => (int)$contact['user_contact_id'], $contacts);
            if ($contactIds) {
                DB::table('translations')->where('table_name', 'user_contacts')->whereIn('table_id', $contactIds)->delete();
                DB::table('user_contacts')->whereIn('user_contact_id', $contactIds)->delete();
            }
            foreach ([['user_instruments', 'user_instrument_id'], ['user_lessons', 'user_lesson_id']] as [$table, $key]) {
                $rows = DB::table($table)->whereIn('user_id', $userIds)->get();
                $ids = array_map(fn(array $row) => (int)$row[$key], $rows);
                if ($ids) {
                    DB::table('translations')->where('table_name', $table)->whereIn('table_id', $ids)->delete();
                    DB::table($table)->whereIn($key, $ids)->delete();
                }
            }
            foreach ([['user_availabilities', 'user_availability_id'], ['user_availability_exceptions', 'user_availability_exception_id']] as [$table, $key]) {
                $rows=DB::table($table)->whereIn('user_id',$userIds)->get(); $ids=array_map(fn(array $row)=>(int)$row[$key],$rows);
                if($ids){DB::table('translations')->where('table_name',$table)->whereIn('table_id',$ids)->delete();DB::table($table)->whereIn($key,$ids)->delete();}
            }
            DB::table('media_files')->whereIn('user_id', $userIds)->whereRaw("path LIKE 'assets/media/users/%'")->update([
                'user_id' => null, 'fileable_id' => null, 'updated_by' => null,
            ]);
            DB::table('translations')->where('table_name', 'users')->whereIn('table_id', $userIds)->delete();
            DB::table('users')->whereIn('user_id', $userIds)->delete();

            return [
                'deleted' => count($userIds),
                'message' => count($userIds) . ' مدیر آموزشگاه آزمایشی و ترجمه‌های مرتبط با موفقیت حذف شدند.',
            ];
        });
    }

    private function syncMusicCatalog(): array {
        $now = date('Y-m-d H:i:s');
        $instrumentIds = [];
        foreach ($this->instrumentCatalog() as $item) {
            $instrumentIds[] = $this->syncCatalogRow('instruments', 'instrument_id', $item['title'], [
                'summary' => $item['summary'], 'description' => $item['description'],
            ], [], $now);
        }

        $lessonIds = [];
        $lessons = [];
        foreach ($this->instrumentCatalog() as $item) {
            $lessons[] = ['title' => $item['title'], 'summary' => 'آموزش نوازندگی ' . $item['title'] . ' از مبانی تا اجرای حرفه‌ای.',
                'description' => 'این درس به آموزش اصولی نوازندگی ' . $item['title'] . ' می‌پردازد و مباحث شناخت ساز، وضعیت صحیح بدن، تکنیک، خواندن نت، تربیت گوش، اجرای رپرتوار و آمادگی صحنه را به‌صورت مرحله‌ای پوشش می‌دهد.'];
        }
        foreach ($this->theoryLessons() as $item) $lessons[] = $item;
        foreach ($lessons as $item) {
            $lessonIds[] = $this->syncCatalogRow('lessons', 'lesson_id', $item['title'], [
                'summary' => $item['summary'], 'description' => $item['description'],
            ], [], $now);
        }

        $levelIds = [];
        foreach ($this->learningLevels() as $sort => $item) {
            $levelIds[] = $this->syncCatalogRow('levels', 'level_id', $item['title'], ['summary' => $item['summary']], [
                'type' => 'learning', 'sort_order' => $sort + 1, 'is_active' => 1,
            ], $now);
        }
        return ['instruments' => $instrumentIds, 'lessons' => $lessonIds, 'levels' => $levelIds];
    }

    private function syncDefaultUserMedia(int $userId, int $userIndex, string $registeredAt): void {
        $number = $userIndex + 1;
        $avatarId = $this->syncMediaFile($userId, $registeredAt, 'profiles', sprintf('user-%02d.jpg', $number), 'teacher_avatar', 0, 720, 720);
        DB::table('users')->where('user_id', $userId)->update(['avatar_file_id' => $avatarId]);
        $this->syncMediaFile($userId, $registeredAt, 'covers', sprintf('user-%02d.jpg', $number), 'cover', 0, 1280, 720);
        for ($i = 1; $i <= 3; $i++) $this->syncMediaFile($userId, $registeredAt, 'gallery', sprintf('user-%02d-%02d.jpg', $number, $i), 'teacher_gallery', $i, 1200, 900);
        if ($number <= 20) $this->syncMediaFile($userId, $registeredAt, 'intro-videos', sprintf('user-%02d.mp4', $number), 'intro_video', 0, null, null, 'video');
    }

    private function syncUserAvailability(int $userId, int $userIndex, string $registeredAt): void {
        $days=['saturday','sunday','monday','tuesday','wednesday','thursday','friday'];
        $dayLabels=['شنبه','یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه'];
        $addresses=DB::table('user_addresses')->where('user_id',$userId)->get();
        $locationIds=array_map(fn(array $row)=>(int)$row['address_id'],$addresses);
        foreach($days as $dayIndex=>$day){
            $parts=1+(($userIndex+$dayIndex)%3); $ranges=$this->availabilityRanges($userIndex,$dayIndex,$parts);
            foreach($ranges as $partIndex=>$range){
                $locationId=$locationIds ? $locationIds[($dayIndex+$partIndex)%count($locationIds)] : 0;
                $location=$locationId ? $this->translatedValue('user_addresses',$locationId,'address') : 'محل فعالیت کاربر';
                $this->syncAvailabilityRow($userId,$registeredAt,['date'=>null,'day_of_week'=>$day,'start_time'=>$range[0].':00','end_time'=>$range[1].':00',
                    'timezone'=>'Asia/Tehran','type'=>'available','is_repeating'=>1,'repeat_period'=>'week','is_closed'=>0,'priority'=>$partIndex+1],
                    'حضور هفتگی در ' . $dayLabels[$dayIndex] . ' از ' . $range[0] . ' تا ' . $range[1],
                    'بازه حضور دوره‌ای کاربر در ' . $location . '؛ فاصله میان این بازه و بازه بعدی زمان استراحت یا جابه‌جایی است.');
            }
        }
        $specificDate=sprintf('2026-%02d-%02d',9+($userIndex%3),1+(($userIndex*3)%27));
        $this->syncAvailabilityRow($userId,$registeredAt,['date'=>$specificDate,'day_of_week'=>null,'start_time'=>'16:00:00','end_time'=>'19:00:00',
            'timezone'=>'Asia/Tehran','type'=>'available','is_repeating'=>0,'repeat_period'=>'none','is_closed'=>0,'priority'=>1],
            'حضور ویژه در تاریخ ' . $specificDate,'حضور غیرتکراری کاربر برای جلسه، ارزیابی یا برنامه ویژه در تاریخ مشخص‌شده.');

        for($i=0;$i<2+($userIndex%3);$i++){
            $date=sprintf('2026-%02d-%02d',9+(($userIndex+$i)%3),1+(($userIndex*5+$i*7)%27));
            $types=['vacation','unavailable','busy','holiday']; $type=$types[($userIndex+$i)%count($types)];
            $allDay=$i===0; $values=['date'=>$date,'start_time'=>$allDay?null:sprintf('%02d:00:00',10+(($userIndex+$i)%6)),
                'end_time'=>$allDay?null:sprintf('%02d:00:00',13+(($userIndex+$i)%6)),'type'=>$type];
            $this->syncAvailabilityExceptionRow($userId,$registeredAt,$values,
                ($allDay?'عدم حضور تمام‌روز':'عدم حضور ساعتی') . ' در تاریخ ' . $date,
                'این استثنا بر برنامه هفتگی مقدم است و برای مرخصی، تعطیلی یا مشغله کاربر در تاریخ مشخص ثبت شده است.');
        }
    }

    private function availabilityRanges(int $userIndex,int $dayIndex,int $parts): array {
        $start=8+(($userIndex+$dayIndex)%2); $ranges=[];
        for($i=0;$i<$parts;$i++){ $from=$start+$i*4; $ranges[]=[sprintf('%02d:00',$from),sprintf('%02d:00',$from+3)]; }
        return $ranges;
    }

    private function syncAvailabilityRow(int $userId,string $createdAt,array $values,string $summary,string $description): void {
        $query=DB::table('user_availabilities')->where('user_id',$userId)->where('start_time',$values['start_time'])->where('end_time',$values['end_time']);
        $query=$values['date']===null?$query->whereNull('date')->where('day_of_week',$values['day_of_week']):$query->where('date',$values['date']);
        $row=$query->first(); $base=$values+['created_at'=>$createdAt,'created_by'=>$userId,'updated_at'=>$createdAt,'updated_by'=>$userId,'deleted_at'=>null,'deleted_by'=>null];
        if($row){$id=(int)$row['user_availability_id'];DB::table('user_availabilities')->where('user_availability_id',$id)->update($base);}else{$id=DB::table('user_availabilities')->insertGetId(['user_id'=>$userId]+$base);}
        if(!$id)throw new RuntimeException('ثبت برنامه حضور آزمایشی ناموفق بود.');
        $this->setTranslation('user_availabilities',$id,$userId,'summary',$summary,$createdAt,$createdAt);$this->setTranslation('user_availabilities',$id,$userId,'description',$description,$createdAt,$createdAt);
    }

    private function syncAvailabilityExceptionRow(int $userId,string $createdAt,array $values,string $summary,string $description): void {
        $query=DB::table('user_availability_exceptions')->where('user_id',$userId)->where('date',$values['date']);
        $query=$values['start_time']===null?$query->whereNull('start_time'):$query->where('start_time',$values['start_time']); $row=$query->first();
        $base=$values+['created_at'=>$createdAt,'created_by'=>$userId,'updated_at'=>$createdAt,'updated_by'=>$userId,'deleted_at'=>null,'deleted_by'=>null];
        if($row){$id=(int)$row['user_availability_exception_id'];DB::table('user_availability_exceptions')->where('user_availability_exception_id',$id)->update($base);}else{$id=DB::table('user_availability_exceptions')->insertGetId(['user_id'=>$userId]+$base);}
        if(!$id)throw new RuntimeException('ثبت مرخصی آزمایشی ناموفق بود.');
        $this->setTranslation('user_availability_exceptions',$id,$userId,'summary',$summary,$createdAt,$createdAt);$this->setTranslation('user_availability_exceptions',$id,$userId,'description',$description,$createdAt,$createdAt);
    }

    private function translatedValue(string $table,int $id,string $field): string {
        $row=DB::table('translations')->where('table_name',$table)->where('table_id',$id)->where('locale','fa')->where('field',$field)->whereNull('deleted_at')->first(); return (string)($row['value']??'');
    }

    private function syncMediaFile(int $userId, string $createdAt, string $folder, string $filename, string $collection, int $sortOrder, ?int $width, ?int $height, string $type = 'image'): int {
        $relativePath = 'assets/media/users/' . $folder . '/' . $filename;
        $absolutePath = dirname(__DIR__, 3) . '/' . $relativePath;
        if (!is_file($absolutePath)) throw new RuntimeException('فایل رسانه پیش‌فرض یافت نشد: ' . $relativePath);
        $existing = DB::table('media_files')->where('path', $relativePath)->first();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $values = ['user_id'=>$userId, 'disk'=>'public', 'directory'=>'assets/media/users/' . $folder, 'filename'=>$filename,
            'extension'=>$extension, 'mime_type'=>$type === 'video' ? 'video/mp4' : 'image/jpeg', 'type'=>$type,
            'collection'=>$collection, 'thumbnail_path'=>null, 'original_filename'=>$filename, 'fileable_type'=>'users',
            'fileable_id'=>$userId, 'sort_order'=>$sortOrder, 'size'=>filesize($absolutePath), 'duration'=>null,
            'width'=>$width, 'height'=>$height, 'checksum'=>hash_file('sha256', $absolutePath), 'visibility'=>'public',
            'updated_at'=>date('Y-m-d H:i:s'), 'updated_by'=>$userId, 'deleted_at'=>null, 'deleted_by'=>null];
        if ($existing) { $id=(int)$existing['media_file_id']; DB::table('media_files')->where('media_file_id',$id)->update($values); return $id; }
        $id=DB::table('media_files')->insertGetId(['path'=>$relativePath, 'created_at'=>$createdAt, 'created_by'=>$userId] + $values);
        if (!$id) throw new RuntimeException('ثبت رسانه پیش‌فرض کاربر ناموفق بود.');
        return $id;
    }

    private function syncCatalogRow(string $table, string $key, string $title, array $fields, array $values, string $now): int {
        $titleTranslation = DB::table('translations')->where('table_name', $table)->where('locale', 'fa')
            ->where('field', 'title')->where('value', $title)->first();
        $row = $titleTranslation ? DB::table($table)->where($key, (int)$titleTranslation['table_id'])->first() : null;
        $base = $values + ['updated_at' => $now, 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null];
        if ($row) {
            $id = (int)$row[$key];
            DB::table($table)->where($key, $id)->update($base);
        } else {
            $id = DB::table($table)->insertGetId($base + ['created_at' => $now, 'created_by' => 1]);
            if (!$id) throw new RuntimeException('ایجاد داده مرجع موسیقی ناموفق بود: ' . $title);
        }
        $this->setTranslation($table, $id, 1, 'title', $title, $now, $now);
        foreach ($fields as $field => $value) $this->setTranslation($table, $id, 1, $field, $value, $now, $now);
        return $id;
    }

    private function syncMusicExperience(int $userId, int $userIndex, string $registeredAt, array $catalog): void {
        $instrumentCount = $userIndex % 6;
        $lessonCount = ($userIndex * 5 + 2) % 6;
        $this->syncExperienceGroup('user_instruments', 'user_instrument_id', 'instrument_id', $catalog['instruments'], $catalog['levels'], $userId, $userIndex, $registeredAt, $instrumentCount, true);
        $this->syncExperienceGroup('user_lessons', 'user_lesson_id', 'lesson_id', $catalog['lessons'], $catalog['levels'], $userId, $userIndex, $registeredAt, $lessonCount, $instrumentCount === 0);
    }

    private function syncExperienceGroup(string $table, string $key, string $foreignKey, array $items, array $levels, int $userId, int $userIndex, string $registeredAt, int $count, bool $canBePrimary): void {
        $wanted = [];
        for ($i = 0; $i < $count; $i++) {
            $itemId = $items[($userIndex * 7 + $i * 11) % count($items)];
            $wanted[] = $itemId;
            $createdAt = date('Y-m-d H:i:s', strtotime($registeredAt) + ($i + 1) * 7200);
            $existing = DB::table($table)->where('user_id', $userId)->where($foreignKey, $itemId)->first();
            $values = ['level_id' => $levels[($userIndex + $i) % count($levels)], 'start_date' => $this->jalaliStartDate($userIndex, $i),
                'is_primary' => $canBePrimary && $i === 0 ? 1 : 0, 'created_at' => $createdAt, 'created_by' => $userId,
                'updated_at' => $createdAt, 'updated_by' => $userId, 'deleted_at' => null, 'deleted_by' => null];
            if ($existing) {
                $id = (int)$existing[$key]; DB::table($table)->where($key, $id)->update($values);
            } else {
                $id = DB::table($table)->insertGetId(['user_id' => $userId, $foreignKey => $itemId] + $values);
                if (!$id) throw new RuntimeException('ثبت سابقه آموزشی آزمایشی ناموفق بود.');
            }
            $kind = $table === 'user_instruments' ? 'نوازندگی این ساز' : 'این درس موسیقی';
            $this->setTranslation($table, $id, $userId, 'summary', 'سابقه آزمایشی پیوسته در ' . $kind . ' با تمرین منظم هفتگی.', $createdAt, $createdAt);
            $this->setTranslation($table, $id, $userId, 'description', 'این سابقه برای آزمون پروفایل مدیر آموزشگاه ساخته شده است. کاربر از تاریخ درج‌شده آموزش را آغاز کرده، تمرین‌های تکنیکی و عملی را دنبال می‌کند و تجربه شرکت در کلاس، تمرین گروهی و اجرای هنرجویی دارد.', $createdAt, $createdAt);
        }
        $rows = DB::table($table)->where('user_id', $userId)->get();
        foreach ($rows as $row) if (!in_array((int)$row[$foreignKey], $wanted, true)) {
            DB::table('translations')->where('table_name', $table)->where('table_id', (int)$row[$key])->delete();
            DB::table($table)->where($key, (int)$row[$key])->delete();
        }
    }

    private function setTranslation(string $table, int $tableId, int $actorId, string $field, string $value, string $createdAt, string $updatedAt, string $locale = 'fa'): void {
        $translation = DB::table('translations')->where('table_name', $table)->where('table_id', $tableId)->where('locale', $locale)->where('field', $field)->first();
        $values = ['code' => null, 'value' => $value, 'version' => 1, 'updated_at' => $updatedAt, 'updated_by' => $actorId, 'deleted_at' => null, 'deleted_by' => null];
        if ($translation) DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values);
        else {
            $id = DB::table('translations')->insertGetId(['table_name' => $table, 'table_id' => $tableId, 'locale' => $locale, 'field' => $field, 'created_at' => $createdAt, 'created_by' => $actorId] + $values);
            if (!$id) throw new RuntimeException('ثبت ترجمه داده موسیقی ناموفق بود.');
        }
    }

    private function jalaliStartDate(int $userIndex, int $itemIndex): string {
        $year = 1384 + (($userIndex * 3 + $itemIndex * 2) % 20);
        $month = 1 + (($userIndex + $itemIndex * 3) % 12);
        $day = 1 + (($userIndex * 5 + $itemIndex * 7) % ($month <= 6 ? 31 : 30));
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    private function instrumentCatalog(): array {
        $groups = [
            'ایرانی زهی مضرابی' => ['تار','سه‌تار','سنتور','عود (بربت)','قانون','تنبور','دوتار','دیوان','شورانگیز','رباب ایرانی'],
            'ایرانی زهی آرشه‌ای' => ['کمانچه','قیچک','قیچک باس'],
            'ایرانی بادی' => ['نی','نی‌انبان','سرنا','کرنا','دوزله','بالابان'],
            'ایرانی کوبه‌ای' => ['تنبک','دف','دایره','دهل','نقاره','دمام'],
            'کلاسیک و جهانی' => ['پیانو','کیبورد','ویولن','ویولا','ویولنسل','کنترباس','گیتار کلاسیک','گیتار آکوستیک','گیتار الکتریک','گیتار باس','هارپ','ماندولین','یوکللی','آکاردئون','فلوت','پیکولو','کلارینت','ابوا','فاگوت','ساکسوفون','ترومپت','ترومبون','هورن','توبا','ریکوردر','سازدهنی','درامز','کاخن','زیلوفون','ماریمبا'],
        ];
        $items = [];
        foreach ($groups as $family => $titles) foreach ($titles as $title) $items[] = [
            'title'=>$title, 'summary'=>$title . ' سازی از خانواده «' . $family . '» است که در آموزش و اجرا کاربرد دارد.',
            'description'=>$title . ' در خانواده «' . $family . '» قرار می‌گیرد. شکل امروزی آن حاصل تحول شیوه‌های سازسازی و اجرا در دوره‌های مختلف است و در تک‌نوازی، همنوازی یا ارکستر به کار می‌رود. آموزش آن از شناخت ساختمان ساز، تولید صدای صحیح و ریتم‌خوانی آغاز می‌شود و سپس تکنیک، بیان موسیقایی، رپرتوار و اجرای صحنه‌ای را پوشش می‌دهد.'
        ];
        return $items;
    }

    private function theoryLessons(): array {
        $titles = ['سلفژ','تئوری موسیقی','هارمونی','کنترپوان','آهنگسازی','تنظیم موسیقی','ارکستراسیون','فرم و آنالیز موسیقی','تربیت شنوایی','ریتم‌خوانی','نت‌خوانی','بداهه‌نوازی','رهبری ارکستر','رهبری گروه کر','آواز کلاسیک','آواز ایرانی','صداسازی','موسیقی کودک','مبانی موسیقی ایرانی','ردیف موسیقی ایرانی','دستگاه‌شناسی موسیقی ایرانی','تصنیف‌سازی','موسیقی‌شناسی','تاریخ موسیقی ایران','تاریخ موسیقی جهان','نرم‌افزارهای موسیقی','ضبط و میکس صدا','مسترینگ','طراحی صدا','موسیقی فیلم','اجرای گروهی','گروه‌نوازی ایرانی','کر و همخوانی'];
        return array_map(fn(string $title) => ['title'=>$title,
            'summary'=>'درس ' . $title . ' برای پرورش دانش، گوش و مهارت عملی هنرجوی موسیقی.',
            'description'=>'درس ' . $title . ' بخشی از آموزش نظام‌مند موسیقی است. محتوای آن از مفاهیم پایه و تمرین‌های شنیداری یا نوشتاری آغاز می‌شود و به تحلیل، خلاقیت، اجرا و کاربرد حرفه‌ای می‌رسد. روند تاریخی این حوزه با تحول نظام‌های آموزشی، شیوه‌های نت‌نویسی، موسیقی ایرانی و دستاوردهای موسیقی جهان پیوند دارد.'
        ], $titles);
    }

    private function learningLevels(): array {
        return [
            ['title'=>'خیلی تازه‌کار','summary'=>'بدون تجربه قبلی؛ آشنایی اولیه با موسیقی و ساز.'], ['title'=>'تازه‌کار','summary'=>'در حال یادگیری مبانی، نت‌خوانی و تمرین‌های ابتدایی.'],
            ['title'=>'مقدماتی','summary'=>'توانایی اجرای تمرین‌ها و قطعات ساده با راهنمایی مدرس.'], ['title'=>'پایه','summary'=>'تسلط نسبی بر اصول و آمادگی ورود به رپرتوار متنوع‌تر.'],
            ['title'=>'متوسط','summary'=>'اجرای مستقل قطعات متوسط و شناخت مناسب تکنیک و تئوری.'], ['title'=>'متوسط رو به بالا','summary'=>'کنترل فنی بهتر و آمادگی برای تحلیل و اجرای جدی‌تر.'],
            ['title'=>'نیمه‌پیشرفته','summary'=>'تجربه اجرایی قابل اتکا و کار روی ظرافت‌های بیانی.'], ['title'=>'پیشرفته','summary'=>'تسلط بالا بر تکنیک، تفسیر و اجرای رپرتوار دشوار.'],
            ['title'=>'حرفه‌ای','summary'=>'توانایی اجرای حرفه‌ای، تدریس یا فعالیت تخصصی مستمر.'],
        ];
    }

    private function syncContacts(int $userId, int $userIndex, string $username, string $registeredAt): void {
        $templates = $this->contactTemplates($userIndex, $username);
        $contactCount = ($userIndex % 10) + 1;
        $seenModes = [];
        $registrationTimestamp = strtotime($registeredAt);

        for ($contactIndex = 0; $contactIndex < $contactCount; $contactIndex++) {
            $contact = $templates[$contactIndex];
            $mode = $contact['mode'];
            $isMain = !isset($seenModes[$mode]);
            $seenModes[$mode] = true;
            $contactCreatedAt = date('Y-m-d H:i:s', $registrationTimestamp + ($contactIndex + 1) * 6 * 3600 + ($userIndex % 5) * 3600);
            $approvedAt = date('Y-m-d H:i:s', strtotime($contactCreatedAt) + (($contactIndex % 12) + 1) * 3600);
            $lastCalledAt = date('Y-m-d H:i:s', strtotime($approvedAt) + (($contactIndex % 14) + 1) * 86400);
            $updatedAt = date('Y-m-d H:i:s', strtotime($lastCalledAt) + (($contactIndex % 6) + 1) * 3600);
            $contactId = $this->findContactByTranslatedValue($userId, $contact['value']);
            $values = [
                'mode' => $mode,
                'platform' => $contact['platform'],
                'priority' => $isMain ? 'primary' : $contact['priority'],
                'is_main' => $isMain ? 1 : 0,
                'status' => $contact['status'],
                'last_called_at' => $lastCalledAt,
                'created_at' => $contactCreatedAt,
                'created_by' => $userId,
                'updated_at' => $updatedAt,
                'updated_by' => $userId,
                'approved_at' => $approvedAt,
                'approved_by' => $userId,
                'deleted_at' => null,
                'deleted_by' => null,
            ];

            if ($contactId) {
                DB::table('user_contacts')->where('user_contact_id', $contactId)->update($values);
            } else {
                $contactId = DB::table('user_contacts')->insertGetId(['user_id' => $userId] + $values);
                if (!$contactId) throw new RuntimeException('ایجاد راه ارتباطی آزمایشی کاربر ناموفق بود.');
            }
            $this->setContactTranslation($contactId, $userId, 'value', $contact['value'], $contactCreatedAt, $updatedAt, $approvedAt);
            $this->setContactTranslation($contactId, $userId, 'note', $contact['note'], $contactCreatedAt, $updatedAt, $approvedAt);
        }
    }

    private function findContactByTranslatedValue(int $userId, string $value): int {
        $translations = DB::table('translations')->where('table_name', 'user_contacts')->where('locale', 'fa')
            ->where('field', 'value')->where('value', $value)->get();
        foreach ($translations as $translation) {
            $contact = DB::table('user_contacts')->where('user_contact_id', (int)$translation['table_id'])->where('user_id', $userId)->first();
            if ($contact) return (int)$contact['user_contact_id'];
        }
        return 0;
    }

    private function setContactTranslation(int $contactId, int $userId, string $field, string $value, string $createdAt, string $updatedAt, string $approvedAt): void {
        $translation = DB::table('translations')->where('table_name', 'user_contacts')->where('table_id', $contactId)
            ->where('locale', 'fa')->where('field', $field)->first();
        $values = [
            'code' => null, 'value' => $value, 'version' => 1,
            'created_at' => $createdAt, 'created_by' => $userId,
            'updated_at' => $updatedAt, 'updated_by' => $userId,
            'approved_at' => $approvedAt, 'approved_by' => $userId,
            'deleted_at' => null, 'deleted_by' => null,
        ];
        if ($translation) {
            DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values);
            return;
        }
        $translationId = DB::table('translations')->insertGetId([
            'table_name' => 'user_contacts', 'table_id' => $contactId, 'locale' => 'fa', 'field' => $field,
        ] + $values);
        if (!$translationId) throw new RuntimeException('ثبت ترجمه راه ارتباطی آزمایشی ناموفق بود.');
    }

    private function contactTemplates(int $userIndex, string $username): array {
        $number = $userIndex + 1;
        return [
            ['mode'=>$userIndex % 2 === 0 ? 'phone' : 'email','platform'=>'other','value'=>$userIndex % 2 === 0 ? sprintf('0991%07d', 1000000 + $number) : sprintf('sornaz.academy.manager%02d@gmail.com', $number),'priority'=>'primary','status'=>'active','note'=>'راه ارتباطی اصلی کاربر؛ کد تأیید یک‌بارمصرف با موفقیت ثبت شده است.'],
            ['mode'=>$userIndex % 2 === 0 ? 'email' : 'phone','platform'=>'other','value'=>$userIndex % 2 === 0 ? sprintf('sornaz.manager%02d@gmail.com', $number) : sprintf('0912%07d', 5000000 + $number),'priority'=>'support','status'=>'active','note'=>'راه ارتباطی پشتیبان برای زمان‌هایی که مورد اصلی در دسترس نیست.'],
            ['mode'=>'social','platform'=>'instagram','value'=>'https://instagram.com/' . $username,'priority'=>'secondary','status'=>'active','note'=>'صفحه عمومی اینستاگرام؛ پیام‌های کاری در ساعات اداری بررسی می‌شوند.'],
            ['mode'=>'social','platform'=>'telegram','value'=>'https://t.me/' . $username,'priority'=>'support','status'=>'active','note'=>'شناسه تلگرام برای پشتیبانی و ارسال فایل‌های آموزشی.'],
            ['mode'=>'phone','platform'=>'other','value'=>sprintf('0935%07d', 6000000 + $number),'priority'=>'emergency','status'=>'active','note'=>'شماره تماس اضطراری؛ فقط برای موارد فوری استفاده شود.'],
            ['mode'=>'social','platform'=>'website','value'=>'https://' . $username . '.sornaz.test','priority'=>'secondary','status'=>'active','note'=>'وب‌سایت آزمایشی معرفی مدیر و برنامه‌های آموزشگاه.'],
            ['mode'=>'email','platform'=>'other','value'=>sprintf('%s.office@gmail.com', $username),'priority'=>'ledger','status'=>'active','note'=>'ایمیل امور اداری و دریافت اسناد و صورت‌حساب‌ها.'],
            ['mode'=>'social','platform'=>'whats-app','value'=>'https://wa.me/98' . substr(sprintf('0919%07d', 7000000 + $number), 1),'priority'=>'support','status'=>'active','note'=>'واتساپ کاری؛ تماس صوتی فقط با هماهنگی قبلی انجام شود.'],
            ['mode'=>'social','platform'=>'linkedin','value'=>'https://linkedin.com/in/' . $username,'priority'=>'other','status'=>'inactive','note'=>'پروفایل حرفه‌ای قدیمی است و ممکن است با تأخیر به‌روزرسانی شود.'],
            ['mode'=>'social','platform'=>'google-meet','value'=>'https://meet.google.com/snz-' . sprintf('%04d', $number) . '-mgr','priority'=>'other','status'=>'deactive','note'=>'لینک جلسه آنلاین رزرو؛ در حال حاضر فقط با تعیین وقت قبلی فعال می‌شود.'],
        ];
    }

    private function syncAddresses(int $userId, int $userIndex, string $registeredAt): void {
        $locations = $this->locations();
        $addressCount = ($userIndex % 3) + 1;
        $registrationTimestamp = strtotime($registeredAt);

        for ($addressIndex = 0; $addressIndex < $addressCount; $addressIndex++) {
            $location = $locations[($userIndex * 3 + $addressIndex) % count($locations)];
            $province = DB::table('world_iran_provinces')->where('province_name', $location['province'])->first();
            if (!$province) throw new RuntimeException('استان آدرس آزمایشی یافت نشد: ' . $location['province']);
            $county = DB::table('world_iran_counties')
                ->where('county_name', $location['county'])
                ->where('province_id', (int)$province['province_id'])
                ->first();
            if (!$county) throw new RuntimeException('شهرستان آدرس آزمایشی با استان انتخاب‌شده تطابق ندارد: ' . $location['county']);

            $addressCreatedAt = date('Y-m-d H:i:s', $registrationTimestamp + ($addressIndex + 1) * 86400 + ($userIndex % 12) * 3600);
            $addressUpdatedAt = date('Y-m-d H:i:s', strtotime($addressCreatedAt) + (($addressIndex + 1) * 5) * 3600);
            $existing = DB::table('user_addresses')->where('user_id', $userId)->where('postal_code', $location['postal_code'])->first();
            $values = [
                'country_id' => 0,
                'province_id' => (int)$province['province_id'],
                'county_id' => (int)$county['county_id'],
                'is_main' => $addressIndex === 0 ? 1 : 0,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'postal_code' => $location['postal_code'],
                'created_at' => $addressCreatedAt,
                'created_by' => $userId,
                'updated_at' => $addressUpdatedAt,
                'updated_by' => $userId,
                'deleted_at' => null,
                'deleted_by' => null,
            ];

            if ($existing) {
                $addressId = (int)$existing['address_id'];
                DB::table('user_addresses')->where('address_id', $addressId)->update($values);
            } else {
                $addressId = DB::table('user_addresses')->insertGetId(['user_id' => $userId] + $values);
                if (!$addressId) throw new RuntimeException('ایجاد آدرس آزمایشی کاربر ناموفق بود.');
            }

            $this->setAddressTranslation($addressId, $userId, 'address', $location['address'], $addressCreatedAt, $addressUpdatedAt);
            $this->setAddressTranslation($addressId, $userId, 'note', $location['note'], $addressCreatedAt, $addressUpdatedAt);
        }
    }

    private function setAddressTranslation(int $addressId, int $userId, string $field, string $value, string $createdAt, string $updatedAt): void {
        $translation = DB::table('translations')->where('table_name', 'user_addresses')->where('table_id', $addressId)
            ->where('locale', 'fa')->where('field', $field)->first();
        $values = [
            'code' => null, 'value' => $value, 'version' => 1,
            'created_at' => $createdAt, 'created_by' => $userId,
            'updated_at' => $updatedAt, 'updated_by' => $userId,
            'deleted_at' => null, 'deleted_by' => null,
        ];
        if ($translation) {
            DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values);
            return;
        }
        $translationId = DB::table('translations')->insertGetId([
            'table_name' => 'user_addresses', 'table_id' => $addressId, 'locale' => 'fa', 'field' => $field,
        ] + $values);
        if (!$translationId) throw new RuntimeException('ثبت ترجمه آدرس آزمایشی ناموفق بود.');
    }

    private function locations(): array {
        return [
            ['province'=>'تهران','county'=>'تهران','address'=>'تهران، بزرگراه شیخ فضل‌الله نوری، ورودی بزرگراه شهید همت، برج میلاد','latitude'=>35.7448416,'longitude'=>51.3753212,'postal_code'=>'1449614531','note'=>'نشانی اصلی آزمایشی؛ مراجعه حضوری بهتر است پیش از ساعت ۱۸ هماهنگ شود.'],
            ['province'=>'فارس','county'=>'شیراز','address'=>'شیراز، بلوار گلستان، حدفاصل چهارراه ادبیات و چهارراه حافظیه، آرامگاه حافظ','latitude'=>29.6259365,'longitude'=>52.5585667,'postal_code'=>'7136419151','note'=>'نشانی دوم آزمایشی در محدوده گردشگری؛ در روزهای تعطیل احتمال شلوغی وجود دارد.'],
            ['province'=>'اصفهان','county'=>'اصفهان','address'=>'اصفهان، میدان امام حسین، خیابان سپه، میدان نقش جهان','latitude'=>32.6573073,'longitude'=>51.6775612,'postal_code'=>'8146414848','note'=>'محل در محدوده تاریخی است و دسترسی خودرو در بعضی ساعت‌ها محدود می‌شود.'],
            ['province'=>'آذربایجان شرقی','county'=>'تبریز','address'=>'تبریز، محله ششگلان، خیابان ثقةالاسلام، جنب خیابان عارف، مقبره‌الشعرا','latitude'=>38.0820297,'longitude'=>46.2919108,'postal_code'=>'5138663411','note'=>'نشانی اجاره‌ای آزمایشی؛ ممکن است بین ساعت ۱۳ تا ۱۵ پاسخ‌گویی حضوری انجام نشود.'],
            ['province'=>'خراسان رضوی','county'=>'مشهد','address'=>'مشهد، خیابان امام رضا، میدان بیت‌المقدس، ورودی باب‌الرضا حرم مطهر رضوی','latitude'=>36.2879029,'longitude'=>59.6157291,'postal_code'=>'9137913316','note'=>'به علت محدودیت ترافیکی مرکز شهر، استفاده از حمل‌ونقل عمومی پیشنهاد می‌شود.'],
            ['province'=>'گیلان','county'=>'رشت','address'=>'رشت، میدان شهرداری، مجموعه تاریخی شهرداری رشت','latitude'=>37.2759338,'longitude'=>49.5883064,'postal_code'=>'4136934364','note'=>'این نشانی برای تحویل مرسوله در ساعات اداری مناسب‌تر است.'],
            ['province'=>'یزد','county'=>'یزد','address'=>'یزد، خیابان امام خمینی، میدان امیرچخماق، مجموعه تاریخی امیرچخماق','latitude'=>31.8972362,'longitude'=>54.3686977,'postal_code'=>'8916736918','note'=>'نشانی در بافت تاریخی قرار دارد؛ پیش از مراجعه تلفنی هماهنگ شود.'],
            ['province'=>'کرمان','county'=>'کرمان','address'=>'کرمان، میدان ارگ، بازار گنجعلی‌خان، مجموعه گنجعلی‌خان','latitude'=>30.2924087,'longitude'=>57.0671107,'postal_code'=>'7616914111','note'=>'دسترسی مستقیم خودرو تا ورودی بازار ممکن نیست و بخشی از مسیر پیاده است.'],
            ['province'=>'همدان','county'=>'همدان','address'=>'همدان، میدان بوعلی سینا، آرامگاه بوعلی سینا','latitude'=>34.7988575,'longitude'=>48.5146239,'postal_code'=>'6516738695','note'=>'خانه آزمایشی نزدیک میدان است؛ ممکن است عصرها کسی در محل حضور نداشته باشد.'],
            ['province'=>'خوزستان','county'=>'اهواز','address'=>'اهواز، بلوار ساحلی شرقی، حدفاصل خیابان سلمان فارسی و میدان شهدا، پل سفید','latitude'=>31.3282914,'longitude'=>48.6706183,'postal_code'=>'6135714387','note'=>'برای ملاقات حضوری، ساعات خنک‌تر روز انتخاب شود و هماهنگی قبلی انجام گیرد.'],
        ];
    }

    private function userValues(array $person, int $index, string $createdAt, string $passwordHash): array {
        $createdTimestamp = strtotime($createdAt);
        $status = $this->statusFor($index);
        $approvedBySiteAdmin = $status === 'approved' && $index >= 20 && $index < 32;
        $phoneVerifiedAt = date('Y-m-d H:i:s', $createdTimestamp + (($index % 48) + 1) * 3600);
        $lastLoginAt = date('Y-m-d H:i:s', min(time(), $createdTimestamp + (($index % 90) + 4) * 86400));
        $updatedAt = date('Y-m-d H:i:s', min(time(), $createdTimestamp + (($index % 72) + 1) * 3600));
        $approvedAt = $approvedBySiteAdmin
            ? date('Y-m-d H:i:s', $createdTimestamp + (5 * 3600) + (($index * 19) % 235) * 3600)
            : null;

        return [
            'email' => sprintf('sornaz.academy.manager%02d@gmail.com', $index + 1),
            'phone' => sprintf('0991%07d', 1000000 + $index + 1),
            'phone_verified_at' => $phoneVerifiedAt,
            'last_login_at' => $lastLoginAt,
            'last_login_ip' => sprintf('185.51.%d.%d', 20 + ($index % 20), 10 + $index),
            'password' => $passwordHash,
            'national_code' => $this->nationalCode(730000001 + $index),
            'gender' => $person['gender'],
            'type' => 'human',
            'status' => $status,
            'locale' => 'fa',
            'timezone' => 'Asia/Tehran',
            'avatar_file_id' => null,
            'visibility' => $index < 30 ? 'public' : ($index < 40 ? 'private' : 'unlisted'),
            'birthday' => $this->birthdayFor($index),
            'register_time' => $createdAt,
            'register_method' => ['phone', 'email', 'google'][$index % 3],
            'created_at' => $createdAt,
            'created_by' => null,
            'updated_at' => $updatedAt,
            'updated_by' => null,
            'approved_at' => $approvedAt,
            'approved_by' => $approvedBySiteAdmin ? 1 : null,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }

    private function setFullNameTranslation(int $userId, string $fullName, string $createdAt): void {
        $translation = DB::table('translations')
            ->where('table_name', 'users')->where('table_id', $userId)
            ->where('locale', 'fa')->where('field', 'full_name')->first();
        $values = [
            'code' => null,
            'value' => $fullName,
            'version' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
        if ($translation) {
            DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values + [
                'created_by' => $userId,
            ]);
            return;
        }
        $translationId = DB::table('translations')->insertGetId([
            'table_name' => 'users', 'table_id' => $userId, 'locale' => 'fa', 'field' => 'full_name',
            'created_at' => $createdAt, 'created_by' => $userId,
        ] + $values);
        if (!$translationId) throw new RuntimeException('ثبت ترجمه نام کاربر آزمایشی ناموفق بود.');
    }

    private function statusFor(int $index): string {
        if ($index < 20) return 'pending';
        if ($index < 40) return 'approved';
        if ($index < 44) return 'rejected';
        if ($index < 47) return 'inactive';
        return 'banned';
    }

    private function birthdayFor(int $index): string {
        $ages = [25, 27, 29, 61, 65, 70, 75, 80, 63, 68];
        $age = $index < 40 ? 30 + (($index * 7) % 31) : $ages[$index - 40];
        return date('Y-m-d', strtotime('-' . $age . ' years -' . (($index * 17) % 330) . ' days'));
    }

    private function nationalCode(int $base): string {
        $digits = str_split(sprintf('%09d', $base));
        $sum = 0;
        foreach ($digits as $index => $digit) $sum += (int)$digit * (10 - $index);
        $remainder = $sum % 11;
        $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;
        return implode('', $digits) . $checkDigit;
    }

    private function people(): array {
        $maleFirstNames = ['علی', 'رضا', 'امیر', 'حسین', 'مهدی', 'محمد', 'سعید', 'آرش', 'پویان', 'کیوان', 'فرهاد', 'بهرام', 'نوید', 'بابک', 'کامران', 'کوروش', 'داریوش', 'سامان', 'رامین', 'شهاب', 'میلاد', 'محسن', 'مسعود', 'یاسر', 'جواد'];
        $femaleFirstNames = ['مریم', 'سارا', 'نگار', 'الهام', 'نرگس', 'لیلا', 'مهسا', 'نازنین', 'شبنم', 'پرستو', 'سپیده', 'ترانه', 'آزاده', 'سمیرا', 'بهاره', 'غزل', 'حدیث', 'رویا', 'مینا', 'شیوا', 'الهه', 'زهرا', 'فاطمه', 'ریحانه', 'هانیه'];
        $lastNames = ['محمدی', 'احمدی', 'رضایی', 'کریمی', 'حسینی', 'مرادی', 'قاسمی', 'اکبری', 'صادقی', 'نوری'];
        $people = [];
        for ($i = 0; $i < 25; $i++) {
            $people[] = ['name' => $maleFirstNames[$i] . ' ' . $lastNames[$i % 10], 'gender' => 'male'];
            $people[] = ['name' => $femaleFirstNames[$i] . ' ' . $lastNames[($i + 5) % 10], 'gender' => 'female'];
        }
        return $people;
    }
}
