<?php
namespace Modules\Analytics\Services;

use Core\router\Router;

/** Declarative data contracts for native Material forms, not HTML templates. */
final class MobilePanelCatalog {
    private function f(string $key, string $fa, string $type = 'text', mixed $options = null, bool $required = false): array {
        return array_filter(['key'=>$key,'label'=>$fa,'type'=>$type,'options'=>$options,'required'=>$required], fn($v)=>$v!==null);
    }
    private function a(string $path, string $label, string $method='POST', array $fields=[], bool $row=true): array {
        return compact('path','label','method','fields','row');
    }
    private function s(string $key,string $label,string $en,string $path,string $rows,array $fields=[],string $access='management',bool $crud=true): array {
        $actions=['list'=>$this->a($path,'نمایش','GET',[],false)];
        if($crud) $actions += ['create'=>$this->a($path,'افزودن','POST',$fields,false),'update'=>$this->a($path.'/{id}/update','ویرایش','POST',$fields),'delete'=>$this->a($path.'/{id}/delete','حذف')];
        return compact('key','label','en','rows','fields','access','actions');
    }
    public function sections(): array {
        $f=fn(...$args)=>$this->f(...$args); $a=fn(...$args)=>$this->a(...$args); $s=fn(...$args)=>$this->s(...$args);
        $description=[$f('summary','خلاصه','multiline'),$f('description','توضیحات','multiline')];
        $organization=$f('organizationUserId','آموزشگاه یا شعبه','select',['source'=>'organizations','id'=>'user_id']);
        $status=$f('status','وضعیت','select',['pending'=>'در انتظار تأیید','active'=>'فعال','inactive'=>'غیرفعال']);
        $addressFields=[$f('address','نشانی','multiline'),$f('postal_code','کد پستی'),$f('city_id','شهر','select',['source'=>'catalog.cities']),$f('is_main','نشانی اصلی','bool')];
        $sections=[];
        $sections['dashboard']=$s('dashboard','داشبورد','Dashboard','/analytics/admin-dashboard','',[],'common',false);
        foreach(['my-courses'=>['دوره‌های من','My courses','courses'],'my-terms'=>['ترم‌های من','My terms','terms'],'my-classrooms'=>['کلاس‌های من','My classes','classes']]as$key=>$meta) $sections[$key]=$s($key,$meta[0],$meta[1],'/analytics/admin-learning',$meta[2],[],'common',false);
        $sections['account']=$s('account','حساب کاربری','Account','/analytics/admin-account','',[],'common',false);
        $sections['account']['actions'] += [
            'profile'=>$a('/analytics/admin-account/profile','ویرایش مشخصات','POST',[$f('name','نام','text',null,true),$f('email','ایمیل','email'),$f('phone','شماره تماس','phone'),$f('founded','تاریخ تأسیس','date'),$f('address','نشانی','multiline')],false),
            'bio'=>$a('/analytics/admin-account/bio','معرفی','POST',[$f('shortIntro','معرفی کوتاه','multiline'),$f('biography','درباره من','multiline')],false),
            'privacy'=>$a('/analytics/admin-account/privacy','حریم خصوصی','POST',[$f('showPublicProfile','نمایش عمومی پروفایل','bool')],false),
            'security'=>$a('/analytics/admin-account/security','تغییر رمز عبور','POST',[$f('password','رمز عبور جدید','password',null,true),$f('passwordConfirmation','تکرار رمز عبور','password',null,true)],false),
            'avatar'=>$a('/analytics/admin-account/media/avatar','عکس پروفایل','POST',[$f('file','تصویر','file'),$f('title','عنوان','text',null,true)],false),
            'cover'=>$a('/analytics/admin-account/media/cover','تصویر کاور','POST',[$f('file','تصویر','file'),$f('title','عنوان','text',null,true)],false),
            'document'=>$a('/analytics/admin-account/media/document','افزودن سند','POST',[$f('file','فایل','file'),$f('title','عنوان'),$f('document_type','نوع سند'),$f('document_number','شماره سند'),$f('issued_at','تاریخ صدور','date'),$f('expires_at','تاریخ انقضا','date')],false),
            'backup'=>$a('/analytics/admin-account/backups','پشتیبان‌گیری','POST',[],false),
            'merge'=>$a('/analytics/admin-account/merges','درخواست ادغام حساب','POST',[$f('targetUserId','کاربر مقصد','number'),$f('reason','دلیل','multiline')],false),
            'end-session'=>$a('/analytics/admin-account/sessions/{id}/end','پایان نشست'),
            'delete-media'=>$a('/analytics/admin-account/media/{id}/delete','حذف فایل'),
            'download-media'=>$a('/analytics/admin-account/media/{id}/download','دانلود فایل','GET')+['download'=>true],
            'download-backup'=>$a('/analytics/admin-account/backups/{id}/download','دانلود پشتیبان','GET')+['download'=>true],
            'cancel-merge'=>$a('/analytics/admin-account/merges/{id}/cancel','لغو ادغام'),
            'decide-merge'=>$a('/analytics/admin-account/merges/{id}/decision','بررسی ادغام','POST',[$f('decision','نتیجه','select',['approved'=>'تأیید','rejected'=>'رد']),$f('reason','توضیح','multiline')])+['admin'=>true],
        ];
        $sections['chat']=$s('chat','گفتگوها','Chats','/analytics/chat','conversations',[$f('type','نوع گفتگو','select',['direct'=>'خصوصی','group'=>'گروه']),$f('title','نام گروه'),$f('userIds','اعضا','multi',['source'=>'people'])],'common',false);
        $sections['chat']['actions'] += [
            'create'=>$a('/analytics/chat','گفتگوی جدید','POST',$sections['chat']['fields'],false),
            'messages'=>$a('/analytics/chat/{id}/messages','پیام‌ها','GET'),
            'details'=>$a('/analytics/chat/{id}/details','اطلاعات گفتگو','GET'),
            'send'=>$a('/analytics/chat/{id}/messages','ارسال پیام','POST',[$f('body','پیام','multiline'),$f('file','پیوست','file')]),
            'rename'=>$a('/analytics/chat/{id}/rename','تغییر نام','POST',[$f('title','نام گفتگو')]),
            'avatar'=>$a('/analytics/chat/{id}/avatar','تصویر گفتگو','POST',[$f('file','تصویر','file')]),
            'members'=>$a('/analytics/chat/{id}/members','افزودن عضو','POST',[$f('userIds','اعضا','multi',['source'=>'people'])]),
            'remove-member'=>$a('/analytics/chat/{id}/members/{userId}/remove','حذف عضو'),
            'leave'=>$a('/analytics/chat/{id}/leave','ترک گفتگو'),'delete'=>$a('/analytics/chat/{id}/delete','حذف گفتگو'),
            'like'=>$a('/analytics/chat/messages/{id}/like','پسندیدن پیام'),
            'edit-message'=>$a('/analytics/chat/messages/{id}/edit','ویرایش پیام','POST',[$f('body','متن پیام','multiline')]),
            'delete-message'=>$a('/analytics/chat/messages/{id}/delete','حذف پیام'),
            'forward'=>$a('/analytics/chat/messages/{id}/forward','ارسال به گفتگو','POST',[$f('conversationIds','گفتگوها','multi',['source'=>'conversations'])]),
            'file'=>$a('/analytics/chat/messages/{id}/file','دانلود پیوست','GET')+['download'=>true],
        ];
        $sections['messages']=$s('messages','پیام‌ها','Messages','/analytics/admin-messages','messages',[$f('receiverId','گیرنده','select',['source'=>'recipients']),$f('title','عنوان','text',null,true),$f('body','متن','multiline',null,true)],'member');
        $sections['notifications']=$s('notifications','اعلان‌ها','Notifications','/analytics/admin-notifications','items',[$f('title','عنوان','text',null,true),$f('body','متن','multiline',null,true),$f('audience','مخاطبان'),$f('asDraft','پیش‌نویس','bool')],'member');
        foreach(['messages','notifications'] as$key) foreach(['read'=>'خوانده‌شده','unread'=>'خوانده‌نشده'] as$op=>$label) $sections[$key]['actions'][$op]=$a('/analytics/admin-'.$key.'/{id}/'.$op,$label);
        $sections['branches']=$s('branches','شعبه‌ها','Branches','/academy/admin/branches','branches',[
            $f('academy_id','آموزشگاه','select',['source'=>'catalog.academies']),$f('name','نام شعبه','text',null,true),$f('username','نام کاربری'),$f('email','ایمیل','email'),$f('password','رمز عبور','password'),$f('password2','تکرار رمز','password'),$f('type_id','نوع شعبه','select',['source'=>'catalog.types']),$f('physical_type','نوع فعالیت','select',['physical'=>'حضوری','virtual'=>'مجازی','hybrid'=>'ترکیبی']),$f('short_description','معرفی کوتاه','multiline'),$f('slogan','شعار'),$f('bio','درباره شعبه','multiline'),$f('addresses','نشانی‌ها','rows',$addressFields),$status]);
        $sections['branch-types']=$s('branch-types','انواع شعبه','Branch types','/academy/admin/branches','catalog.types',[$f('title','عنوان','text',null,true),$description[1]],'admin',false);
        $sections['branch-types']['actions'] += ['create'=>$a('/academy/admin/branches/types','افزودن نوع','POST',$sections['branch-types']['fields'],false),'update'=>$a('/academy/admin/branch-types/{id}/update','ویرایش','POST',$sections['branch-types']['fields']),'delete'=>$a('/academy/admin/branch-types/{id}/delete','حذف')];
        $memberFields=[$organization,$f('name','نام و نام خانوادگی','text',null,true),$f('phone','شماره تماس','phone'),$f('nationalId','کد ملی'),$f('gender','جنسیت','select',['male'=>'مرد','female'=>'زن']),$f('birthDate','تاریخ تولد','date'),$f('roleId','نقش','select',['source'=>'staff_catalog.roles']),$f('startDate','شروع همکاری','date'),$f('endDate','پایان همکاری','date'),$f('profileVisibility','نمایش پروفایل','select',['public'=>'عمومی','private'=>'خصوصی']),$f('lessonId','ساز یا درس','select',['source'=>'staff_catalog.lessons']),$f('levelId','سطح','select',['source'=>'staff_catalog.levels']),$f('price','مبلغ قرارداد','number'),$f('currencyId','واحد پول','select',['source'=>'staff_catalog.currencies']),$f('contractTitle','عنوان قرارداد'),$f('contractDescription','شرح قرارداد','multiline')];
        $sections['members']=$s('members','اساتید و کارکنان','Teachers and staff','/academy/admin/members','members',$memberFields);
        $sections['members']['actions']['list']['path']='/academy/admin/members/data';
        $sections['students']=$s('students','هنرجویان','Students','/academy/admin/courses/student-options','',[$organization,$f('name','نام و نام خانوادگی','text',null,true),$f('phone','شماره تماس','phone'),$f('nationalId','کد ملی'),$f('birthDate','تاریخ تولد','date'),$f('termId','ترم','select',['source'=>'terms']),$f('registrationDate','تاریخ ثبت‌نام','date'),$f('fatherName','نام پدر'),$f('parentName','نام سرپرست'),$f('parentPhone','تماس سرپرست','phone'),$f('parentNationalId','کد ملی سرپرست'),$f('parentBirthDate','تولد سرپرست','date'),$f('addresses','نشانی‌ها','rows',$addressFields)],'management',false);
        $sections['students']['actions']['create']=$a('/academy/admin/students','ثبت‌نام هنرجو','POST',$sections['students']['fields'],false);
        $sections['classrooms']=$s('classrooms','کلاس‌ها','Classrooms','/academy/admin/classrooms','classrooms',[$f('branchId','شعبه','select',['source'=>'branches']),$f('typeId','نوع کلاس','select',['source'=>'types']),$f('name','نام کلاس','text',null,true),$f('capacity','ظرفیت','number'),$f('equipment','تجهیزات','multiline'),...$description,$status]);
        $sections['classroom-types']=$s('classroom-types','انواع کلاس','Classroom types','/academy/admin/classroom-types','types',[$f('title','عنوان','text',null,true),$f('type','دسته'),$f('capacity','ظرفیت','number'),...$description]);
        $sections['classroom-types']['actions']['list']['path']='/academy/admin/classrooms';
        $sections['courses']=$s('courses','دوره‌ها','Courses','/academy/admin/courses','courses',[$organization,$f('lesson_id','ساز یا درس','select',['source'=>'lessons']),$f('name','نام دوره','text',null,true),$f('teacher_capacity','ظرفیت مدرس','number'),$f('student_capacity','ظرفیت هنرجو','number'),$f('status','وضعیت','select',['pending'=>'در انتظار','open'=>'باز','ongoing'=>'در حال برگزاری','finished'=>'پایان یافته']),...$description]);
        $sections['course-levels']=$s('course-levels','سطوح دوره','Course levels','/academy/admin/course-levels','levels',[$f('title','عنوان','text',null,true),$f('sort_order','ترتیب','number'),...$description]);
        $sections['course-levels']['actions']['list']['path']='/academy/admin/courses';
        $sessionFields=[$f('date','تاریخ','date'),$f('startTime','شروع','time'),$f('endTime','پایان','time'),$f('classroomId','کلاس','select',['source'=>'classrooms'])];
        $sections['terms']=$s('terms','ترم‌ها','Terms','/academy/admin/terms','terms',[$f('courseId','دوره','select',['source'=>'courses']),$f('name','نام ترم','text',null,true),$f('classroomId','کلاس','select',['source'=>'classrooms']),$f('teachers','مدرسان','multi',['source'=>'teachers']),$f('students','هنرجویان','multi',['source'=>'students']),$f('sessions','جلسه‌ها','rows',$sessionFields),$f('repeatType','تکرار','select',['none'=>'بدون تکرار','weekly'=>'هفتگی']),$f('cost','شهریه','number'),$f('currencyId','واحد پول','select',['source'=>'currencies']),$f('installmentCount','تعداد اقساط','number'),$f('discountId','تخفیف','select',['source'=>'discounts']),...$description,$status]);
        foreach(['cancel'=>'درخواست لغو جلسه','cancel/approve'=>'تأیید لغو جلسه','cancel/reject'=>'رد لغو جلسه','restore'=>'بازگرداندن جلسه']as$op=>$label) $sections['terms']['actions'][str_replace('/','-',$op)]=$a('/academy/admin/terms/{id}/sessions/{sessionId}/'.$op,$label,'POST',[$f('reason','دلیل','multiline'),$f('makeupDate','تاریخ جبرانی','date'),$f('startTime','شروع','time'),$f('endTime','پایان','time')]);
        $sections['finance']=$s('finance','امور مالی و اقساط','Finance','/academy/admin/term-invoices','invoices',[$f('statusCode','وضعیت'),$f('dueDate','سررسید','date'),$f('title','عنوان'),$f('amount','مبلغ','number'),...$description],'management',false);
        $sections['finance']['actions'] += ['update'=>$a('/academy/admin/term-invoices/{id}/update','ویرایش صورتحساب','POST',$sections['finance']['fields']),'pay'=>$a('/academy/admin/term-invoices/{invoiceId}/installments/{installmentId}/zarinpal','پرداخت آنلاین'),'offline'=>$a('/academy/admin/term-invoices/{invoiceId}/installments/{installmentId}/offline','ثبت پرداخت','POST',[$f('amount','مبلغ','number'),$f('reference','شماره پیگیری'),$f('description','توضیح','multiline')])];
        $sections['subscriptions']=$s('subscriptions','اشتراک آموزشگاه','Subscriptions','/academy/admin/subscriptions','subscriptions',[],'management',false);
        $sections['subscriptions']['actions']['pay']=$a('/academy/admin/subscriptions/{id}/zarinpal','پرداخت اشتراک');
        $sections['schedules']=$s('schedules','برنامه کلاس‌ها و حضور و غیاب','Class schedule','/academy/admin/class-schedules','sessions',[],'management',false);
        $sections['schedules']['actions']['attendance']=$a('/academy/admin/class-schedules/{id}/attendance','حضور و غیاب','POST',[$f('students','هنرجویان','rows',[$f('id','هنرجو','select',['source'=>'students']),$f('status','وضعیت','select',['present'=>'حاضر','absent'=>'غایب','late'=>'تأخیر']),$f('note','یادداشت')])]);
        $ranges=[$f('start','شروع','time'),$f('end','پایان','time'),$f('status','وضعیت','select',['فعال'=>'فعال','غیرفعال'=>'غیرفعال'])];
        $sections['member-schedules']=$s('member-schedules','زمان‌های حضور اعضا','Member availability','/academy/admin/member-schedules','schedules',[$f('membershipId','عضو','select',['source'=>'members']),$f('day','روز هفته','select',['0'=>'شنبه','1'=>'یکشنبه','2'=>'دوشنبه','3'=>'سه‌شنبه','4'=>'چهارشنبه','5'=>'پنجشنبه','6'=>'جمعه']),$f('repeatDate','تاریخ','date'),$f('repeatPeriod','تکرار','select',['none'=>'بدون تکرار','weekly'=>'هفتگی']),$f('timezone','منطقه زمانی','select',['source'=>'staff_catalog.timezones']),$f('ranges','بازه‌های زمانی','rows',$ranges),...$description]);
        $sections['availability-exceptions']=$s('availability-exceptions','استثناهای حضور','Availability exceptions','/academy/admin/availability-exceptions','exceptions',[$organization,$f('membershipId','عضو','select',['source'=>'members']),$f('targetType','مربوط به','select',['member'=>'عضو','organization'=>'سازمان']),$f('date','تاریخ','date'),$f('allDay','تمام روز','bool'),$f('type','نوع استثنا'),$f('ranges','بازه‌های زمانی','rows',$ranges),...$description]);
        $sections['scheduling-rules']=$s('scheduling-rules','قوانین زمان‌بندی','Scheduling rules','/analytics/admin-scheduling-rules','rules',[$f('organizationKey','سازمان','select',['source'=>'organizations','id'=>'key']),$f('title','عنوان'),$f('value','مقدار','number'),$f('type','نوع قانون'),$f('valueUnit','واحد'),$status,...$description]);
        $sections['lessons']=$s('lessons','سازها و درس‌ها','Instruments and lessons','/academy/admin/branch-offerings/lessons','lessons',[$f('organization_user_id','آموزشگاه یا شعبه','select',['source'=>'organizations','id'=>'user_id']),$f('lesson_id','ساز یا درس','select',['source'=>'lessons_catalog']),$f('level_id','سطح','select',['source'=>'levels']),$f('is_primary','درس اصلی','bool'),$f('start_date','شروع','date'),$status,...$description]);
        $sections['lessons']['actions']['list']['path']='/academy/admin/branch-offerings';
        $sections['lessons']['actions']['delete']['path']='/academy/admin/branch-offerings/lessons/{id}/delete';
        $sections['gallery']=$s('gallery','گالری','Gallery','/analytics/admin-gallery','items',[$f('file','فایل','file'),$f('title','عنوان','text',null,true),$f('ownerId','مالک','select',['source'=>'owners']),$f('collection','مجموعه','select',['cover'=>'کاور','logo'=>'لوگو','intro_video'=>'ویدیوی معرفی','gallery'=>'گالری']),...$description]);
        $sections['points']=$s('points','امتیازها','Points','/analytics/admin-points','items',[$f('title','عنوان'),$f('action','عملیات'),$f('points','امتیاز','number'),$f('dailyCap','سقف روزانه','number'),$f('cooldownMinutes','فاصله زمانی','number'),$f('branchId','شعبه','select',['source'=>'branches']),$status]);
        $sections['users']=$s('users','کاربران و دسترسی‌ها','Users and access','/analytics/admin-user-access','users',[$f('roleIds','نقش‌ها','multi',['source'=>'roles']),$f('permissionIds','مجوزها','multi',['source'=>'permissions'])],'management',false);
        $sections['users']['actions']['update']=$a('/analytics/admin-user-access/{id}','ویرایش دسترسی','POST',$sections['users']['fields']);
        foreach(['awards'=>['پاداش‌ها و جوایز','Awards','title issuer date description'],'certificates'=>['گواهی‌ها','Certificates','title issuer issue_date expire_date certificate_url file_path description'],'experiences'=>['سوابق کاری','Experience','title organization start_date end_date address_id description'],'educations'=>['تحصیلات','Education','degree institution field start_date end_date description'],'events'=>['رویدادها','Events','title event_date event_type address_id description'],'publications'=>['آثار منتشرشده','Publications','title publisher published_date url content is_peer_reviewed'],'badges'=>['نشان‌ها','Badges','verification_level_id granted_at expires_at status'],'polls'=>['نظرسنجی‌ها','Polls','question description expires_at type is_anonymous status options']] as$key=>$meta) {
            $fields=[$f('user_id','سازمان','select',['source'=>'organizations'])];
            foreach(explode(' ',$meta[2])as$field)$fields[]=$this->profileField($field);
            $sections[$key]=$s($key,$meta[0],$meta[1],'/analytics/admin-profile-content/'.$key,'items',$fields);
            $sections[$key]['actions']['update']['path']='/analytics/admin-profile-content/'.$key.'/{id}';
            $sections[$key]['actions']['status']=$a('/analytics/admin-profile-content/'.$key.'/{id}/status','تغییر وضعیت تأیید');
        }
        $sections['polls']['actions']['vote']=$a('/analytics/admin-profile-content/polls/{id}/vote','رأی دادن','POST',[$f('options','گزینه‌ها','multi',['source'=>'options'])]);
        $sections['posts']=$s('posts','نوشته‌ها','Posts','/analytics/admin-posts','items',[$f('title','عنوان','text',null,true),$f('slug','نامک'),$f('type','نوع نوشته'),$f('status','وضعیت','select',['draft'=>'پیش‌نویس','published'=>'منتشرشده','pending'=>'در انتظار']),$f('visibility','نمایش','select',['public'=>'عمومی','private'=>'خصوصی']),$f('published_at','زمان انتشار','date'),$f('categories','دسته‌ها','multi',['source'=>'categories']),$f('cover','تصویر','file'),...$description,$f('content','متن نوشته','multiline')]);
        foreach(['trash'=>'انتقال به زباله‌دان','restore'=>'بازیابی']as$op=>$label)$sections['posts']['actions'][$op]=$a('/analytics/admin-posts/{id}/'.$op,$label);
        $sections['post-categories']=$s('post-categories','دسته‌بندی نوشته‌ها','Post categories','/analytics/admin-post-categories','items',[$f('title_fa','عنوان فارسی'),$f('title_en','عنوان انگلیسی'),$f('slug','نامک'),$f('group','گروه')]);
        $sections['comments']=$s('comments','دیدگاه‌ها','Comments','/analytics/admin-comments','items',[$f('content','متن دیدگاه','multiline'),$status]);
        $sections['comments']['actions']['reply']=$a('/analytics/admin-comments/{id}/reply','پاسخ','POST',[$f('content','متن پاسخ','multiline')]);
        $sections['media']=$s('media','رسانه‌ها','Media','/analytics/admin-media','items',[$f('file','فایل','file'),$f('title','عنوان'),...$description]);
        $sections['media']['actions']['create']['path']='/analytics/admin-media/upload';
        $sections['tracking']=$s('tracking','گزارش فعالیت‌ها','Activity reports','/analytics/admin-tracking','',[],'admin',false);
        $sections['national-holidays']=$s('national-holidays','تعطیلات رسمی','National holidays','/academy/admin/national-holidays','holidays',[$f('title','عنوان'),$f('date','تاریخ','date'),$f('is_closed','تعطیل','bool'),...$description],'admin');
        foreach(['roles'=>['نقش‌ها','Roles'],'permissions'=>['مجوزها','Permissions']]as$key=>$meta){
            $fields=[$f('name','نام','text',null,true),$f('translations','عنوان‌ها','object',[$f('fa','فارسی'),$f('en','انگلیسی')]),$f('scope','محدوده'),$f('type','نوع'),$f('group','گروه')];
            if($key==='roles')$fields=[...$fields,$f('level','سطح','number'),$f('parentId','نقش بالاتر','select',['source'=>'roles']),$f('color','رنگ'),$f('sortOrder','ترتیب','number'),$f('permissionIds','مجوزها','multi',['source'=>'permissions'])];
            else $fields=[...$fields,$f('resource','بخش'),$f('action','عملیات'),$f('risk','درجه حساسیت'),$f('approval','نیاز به تأیید','bool')];
            $sections[$key]=$s($key,$meta[0],$meta[1],'/analytics/admin-'.$key,$key,$fields,'admin');
            $sections[$key]['actions']['list']['path']='/analytics/admin-access-catalog';
            $sections[$key]['actions']['update']['path']='/analytics/admin-'.$key.'/{id}';
        }
        array_unshift($sections['terms']['fields'], $f('branchId','شعبه','select',['source'=>'branches'],true));
        foreach($sections['terms']['fields'] as&$field) {
            if($field['key']==='repeatType')$field['options']=['no-period'=>'بدون تکرار','week'=>'هفتگی','2-week'=>'دو هفته','3-week'=>'سه هفته','4-week'=>'چهار هفته','month'=>'ماهانه','year'=>'سالانه'];
            if($field['key']==='status')$field['options']=['pending'=>'در انتظار','open'=>'باز','ongoing'=>'در حال برگزاری','finished'=>'پایان یافته'];
        } unset($field);
        foreach(['create','update']as$op)$sections['terms']['actions'][$op]['fields']=$sections['terms']['fields'];
        $sections['terms']['actions']['discount']=$a('/academy/admin/term-discounts','افزودن تخفیف','POST',[$f('title','عنوان','text',null,true),$f('type','نوع','select',['percentage'=>'درصدی','fixed'=>'مبلغ ثابت']),$f('value','مقدار','number',null,true)],false);
        $sections['schedules']['rows']='schedules';
        $sections['schedules']['actions']['attendance']['fields']=[$f('attendance','حضور و غیاب','rows',[$f('memberId','عضو','select',['source'=>'attendance']),$f('status','وضعیت','select',['present'=>'حاضر','absent'=>'غایب','late'=>'تأخیر','leave'=>'مرخصی','excused_absence'=>'غیبت موجه','online'=>'آنلاین']),$f('note','توضیح')])];
        $sections['settings']=$s('settings','تنظیمات سایت','Site settings','/analytics/site-settings','',[],'management',false);
        $sections['settings']['actions']['save']=$a('/analytics/admin-settings','ویرایش تنظیمات','POST',[$f('primaryFont','قلم'),$f('fontScale','اندازه متن','number'),$f('language','زبان','select',['fa'=>'فارسی','en'=>'English']),$f('themeMode','ظاهر','select',['dark'=>'تیره','light'=>'روشن']),$f('colorTheme','تم رنگی','select',['indigo'=>'نیلی','emerald'=>'زمردی','rose'=>'رز','amber'=>'کهربایی'])],false);
        $sections['reports']=$s('reports','گزارش‌ها و نمودارها','Reports and charts','/analytics/admin-dashboard','',[],'management',false);
        $sections['guides']=$s('guides','راهنمای عملکردها','Guides','/analytics/admin-guides','items',[],'admin',false);
        $guideFields=[$f('fa','فارسی','object',[$f('title','عنوان'),$f('content','متن','multiline')]),$f('en','English','object',[$f('title','Title'),$f('content','Content','multiline')])];
        $sections['guides']['actions']['update']=$a('/analytics/admin-guides','ویرایش راهنما','POST',$guideFields);
        $sections['pages']=$s('pages','برگه‌ها','Pages','/analytics/admin-site-pages','pages',[],'admin',false);
        $sections['page-content']=$s('page-content','محتوای برگه','Page content','/analytics/site-page-content','items',[],'admin',false);
        $sections['page-content']['hidden']=true;
        $sections['page-content']['actions']['update']=$a('/analytics/admin-site-page-content','ویرایش محتوا','POST',[$f('fa','متن فارسی','multiline'),$f('en','English text','multiline'),$f('value','تصویر یا آیکن')]);
        $sections['notifications']['rows']='notifications';
        foreach(['publish'=>'انتشار','expire'=>'پایان انتشار']as$op=>$label)$sections['notifications']['actions'][$op]=$a('/analytics/admin-notifications/{id}/'.$op,$label);
        $sections['students']['actions']['list']['path']='/academy/admin/members/data';
        $sections['students']['rows']='members'; $sections['students']['where']=['type'=>'student'];
        $sections['members']['exclude']=['type'=>'student'];
        $sections['students']['optionActions']=['options'];
        $sections['students']['actions']['options']=$a('/academy/admin/courses/student-options','گزینه‌های ثبت‌نام','GET',[],false)+['hidden'=>true];
        $sections['students']['actions']['update']=$a('/academy/admin/members/{id}/update','ویرایش هنرجو','POST',$sections['students']['fields']);
        $sections['students']['actions']['delete']=$a('/academy/admin/members/{id}/delete','حذف هنرجو');
        $sections['students']['actions']['status']=$a('/academy/admin/members/{id}/status','تغییر وضعیت');
        $sections['branch-types']['rows']='types';
        foreach($sections as&$section)foreach($section['actions']as&$action)foreach($action['fields']as&$field){
            if(($field['options']['source']??'')==='catalog.academies')$field['options']['source']='academies';
            if(($field['options']['source']??'')==='catalog.types')$field['options']['source']='types';
            if($section['key']==='members'&&$field['key']==='organizationUserId')$field['options']['source']='staff_catalog.organizations';
            if(($field['options']['source']??'')==='staff_catalog.lessons')$field['options']['id']='lesson_id';
            if($section['key']==='gallery'&&$field['key']==='ownerId')$field['options']['id']='userId';
            if($section['key']==='classrooms'&&$field['key']==='typeId')$field['options']['source']='classroomTypes';
            if($section['key']==='classrooms'&&$field['key']==='equipment'){$field['type']='rows';$field['options']=[$f('name','نام وسیله'),$f('qty','تعداد','number')];}
            if($section['key']==='users'&&$field['key']==='roleIds')$field['initial']='roles';
            if($section['key']==='users'&&$field['key']==='permissionIds')$field['initial']='permissions';
            if($section['key']==='notifications'&&$field['key']==='audience'){$field['type']='select';$field['options']=array_combine(['همه','هنرجویان','اساتید','والدین','پرسنل'],['همه','هنرجویان','اساتید','والدین','پرسنل']);}
        } unset($section,$action,$field);
        foreach($sections['chat']['actions']as&$action)foreach($action['fields']as&$field)if(($field['options']['source']??'')==='people')$field['options']['source']='users';unset($action,$field);
        $sections['account']['actions']['merge']['fields']=[$f('userId','شناسه کاربر','number',null,true),$f('memberId','شماره عضویت','number',null,true),$f('reason','دلیل درخواست','multiline')];
        foreach($sections['account']['actions']['decide-merge']['fields']as&$field)if($field['key']==='reason')$field['key']='note';unset($field);
        $sections['finance']['actions']['offline']['fields']=[$f('method','روش پرداخت','select',['card_to_card'=>'کارت به کارت','pos'=>'کارت‌خوان'],true),$f('reference','شماره پیگیری','text',null,true),$f('payerName','نام پرداخت‌کننده','text',null,true),$f('bankCardType','بانک','select',['melli'=>'ملی','sepah'=>'سپه','mellat'=>'ملت','tejarat'=>'تجارت','saderat'=>'صادرات','refah'=>'رفاه','keshavarzi'=>'کشاورزی','maskan'=>'مسکن','post'=>'پست بانک','tosee_saderat'=>'توسعه صادرات','sanat_madan'=>'صنعت و معدن','eghtesad_novin'=>'اقتصاد نوین','parsian'=>'پارسیان','pasargad'=>'پاسارگاد','saman'=>'سامان','sarmayeh'=>'سرمایه','sina'=>'سینا','shahr'=>'شهر','ayandeh'=>'آینده','gardeshgari'=>'گردشگری','iran_zamin'=>'ایران زمین','resalat'=>'رسالت','melal'=>'ملل','karafarin'=>'کارآفرین','dey'=>'دی','middle_east'=>'خاورمیانه','other'=>'سایر'],true),$f('paymentDate','روز پرداخت','date',null,true),$f('paymentTime','ساعت پرداخت','time',null,true),$f('description','توضیحات','multiline')];
        $sections['posts']['rows']='posts';
        $sections['comments']['rows']='comments';
        foreach(['create','update']as$operation)foreach($sections['posts']['actions'][$operation]['fields']as&$field){
            if($field['key']==='categories'||$field['key']==='cover'){$field['type']='text';unset($field['options']);}
        }unset($field);
        foreach(['create','update']as$operation)foreach($sections['terms']['actions'][$operation]['fields']as&$field){
            if(in_array($field['key'],['teachers','students'],true))$field['options']=['source'=>'members','where'=>['type'=>$field['key']==='teachers'?'teacher':'student'],'match'=>['branchId'=>'branchId']];
            if(in_array($field['key'],['courseId','classroomId'],true))$field['options']['match']=['branchId'=>'branchId'];
        }unset($field);
        $categoryFields=[$f('value','نام سیستمی انگلیسی','text',null,true),$f('fa','عنوان فارسی','text',null,true),$f('en','عنوان انگلیسی','text',null,true)];
        $sections['classroom-categories']=$s('classroom-categories','دسته‌بندی انواع کلاس','Classroom categories','/academy/admin/classrooms','typeOptions',[],'management',false);
        $sections['classroom-categories']['actions'] += ['create'=>$a('/academy/admin/classroom-type-categories','افزودن دسته','POST',$categoryFields,false),'update'=>$a('/academy/admin/classroom-type-categories/{value}/update','ویرایش دسته','POST',array_slice($categoryFields,1)),'delete'=>$a('/academy/admin/classroom-type-categories/{value}/delete','حذف دسته')];
        foreach(['create','update']as$op)foreach($sections['classroom-types']['actions'][$op]['fields']as&$field)if($field['key']==='type'){$field['type']='select';$field['options']=['source'=>'typeOptions','id'=>'value'];}unset($field);
        foreach(['create','update']as$op)foreach($sections['polls']['actions'][$op]['fields']as&$field){
            if($field['key']==='type'){$field['type']='select';$field['options']=['single'=>'تک‌گزینه‌ای','multiple'=>'چندگزینه‌ای'];}
            if($field['key']==='status'){$field['type']='select';$field['options']=['active'=>'فعال','deactive'=>'غیرفعال','closed'=>'بسته'];}
        }unset($field);
        $sections['dashboard']['filters']=[$f('branchId','شعبه','select',['source'=>'branches'])];
        $sections['reports']['filters']=$sections['dashboard']['filters'];
        $sections['schedules']['filters']=[$f('branch','شعبه','select',['source'=>'branches']),$f('classroom','کلاس','select',['source'=>'classrooms']),$f('lesson','درس','select',['source'=>'lessons']),$f('date','تاریخ','date'),$f('timeFrom','از ساعت','time'),$f('timeTo','تا ساعت','time'),$f('status','وضعیت','select',['approved'=>'تأییدشده','pending'=>'در انتظار','cancelled'=>'لغوشده']),$f('mode','نوع برگزاری','select',['in-person'=>'حضوری','online'=>'آنلاین'])];
        $sections['posts']['filters']=[$f('status','وضعیت','select',['draft'=>'پیش‌نویس','published'=>'منتشرشده','pending'=>'در انتظار','trash'=>'زباله‌دان']),$f('type','نوع','select',['post'=>'نوشته','page'=>'برگه','article'=>'مقاله']),$f('visibility','نمایش','select',['public'=>'عمومی','private'=>'خصوصی'])];
        $sections['comments']['filters']=[$f('post','نوشته','select',['source'=>'posts']),$f('status','وضعیت','select',['pending'=>'در انتظار','approved'=>'تأییدشده','rejected'=>'ردشده'])];
        foreach($sections['comments']['actions']['update']['fields']as&$field)if($field['key']==='status')$field['options']=['pending'=>'در انتظار','approved'=>'تأییدشده','rejected'=>'ردشده'];unset($field);
        $sections['points']['rows']='rules';
        $nativeAddressFields=[$f('province','استان','select',['source'=>'provinces','id'=>'province_name','label'=>'province_name']),$f('city','شهر','select',['source'=>'counties','id'=>'county_name','label'=>'county_name']),$f('address','نشانی','multiline'),$f('postal_code','کد پستی'),$f('is_main','نشانی اصلی','bool')];
        foreach(['create','update']as$op) {
            $sections['members']['actions'][$op]['fields'][]=$f('type','نوع قرارداد','select',['teacher'=>'مدرس','receptionist'=>'پذیرش','manager'=>'مدیر','other'=>'سایر'],true);
            $sections['branches']['actions'][$op]['fields'][]=$f('phone','شماره همراه','phone');
            $sections['branches']['actions'][$op]['fields'][]=$f('phones','تلفن‌های تماس','rows',[$f('number','شماره تلفن','phone'),$f('is_main','اصلی','bool')]);
            $sections['branches']['actions'][$op]['fields'][]=$f('links','پیوندها','rows',[$f('title','عنوان'),$f('url','نشانی'),$f('mode','نوع','select',['email'=>'ایمیل','social'=>'شبکه اجتماعی']),$f('platform','شبکه اجتماعی'),$f('is_main','اصلی','bool')]);
            foreach($sections['branches']['actions'][$op]['fields']as&$field)if($field['key']==='physical_type')$field['options']=['physical'=>'حضوری','online'=>'آنلاین','hybrid'=>'ترکیبی'];unset($field);
            foreach(['branches','students']as$entity)foreach($sections[$entity]['actions'][$op]['fields']as&$field)if($field['key']==='addresses')$field['options']=$nativeAddressFields;unset($field);
        }
        $days=array_combine(['شنبه','یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه'],['شنبه','یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه']);
        $repeats=array_combine(['هفتگی','دو هفته','سه هفته','چهار هفته','ماهانه','سالانه','بی‌تکرار'],['هفتگی','دو هفته','سه هفته','چهار هفته','ماهانه','سالانه','بی‌تکرار']);
        $scheduleFields=[$organization,$f('day','روز هفته','select',$days),$f('repeatDate','تاریخ شروع','date'),$f('repeatPeriod','تکرار','select',$repeats),$f('timezone','منطقه زمانی'),$f('ranges','بازه‌های زمانی','rows',$ranges),...$description];
        $sections['availabilities']=$s('availabilities','برنامه زمانی سازمان','Organization availability','/academy/admin/branch-offerings/schedules','schedules',$scheduleFields);
        $sections['availabilities']['actions']['list']['path']='/academy/admin/branch-offerings';
        foreach(['create','update']as$operation) {
            foreach($sections['member-schedules']['actions'][$operation]['fields']as&$field){
                if($field['key']==='day')$field['options']=$days;
                if($field['key']==='repeatPeriod')$field['options']=$repeats;
                if($field['key']==='timezone'){$field['type']='text';unset($field['options']);}
            }unset($field);
            foreach($sections['availability-exceptions']['actions'][$operation]['fields']as&$field){
                if($field['key']==='organizationUserId')$field['options']['source']='staff_catalog.organizations';
                if($field['key']==='allDay')$field['initial']='fullDay';
                if($field['key']==='type'){$field['type']='select';$field['options']=['holiday'=>'تعطیلی','closed'=>'بسته','unavailable'=>'عدم حضور','busy'=>'مشغول','vacation'=>'مرخصی','blocked'=>'مسدود'];}
            }unset($field);
            foreach(['roles','permissions']as$entity)foreach($sections[$entity]['actions'][$operation]['fields']as&$field){
                if($field['key']==='translations')$field['options']=[$f('fa','فارسی','object',[$f('title','عنوان','text',null,true),$f('summary','خلاصه','multiline',null,true),$f('description','توضیحات','multiline',null,true)]),$f('en','English','object',[$f('title','Title','text',null,true),$f('summary','Summary','multiline',null,true),$f('description','Description','multiline',null,true)])];
                if($field['key']==='scope'){$field['type']='select';$field['options']=['platform'=>'سامانه','website'=>'وب‌سایت','academy'=>'آموزشگاه','branch'=>'شعبه','self'=>'خود کاربر'];}
                if($field['key']==='type'){$field['type']='select';$field['options']=$entity==='roles'?['system'=>'سیستمی','academy'=>'آموزشگاه','other'=>'سایر']:['menu'=>'منو','sidebar'=>'منوی کناری','topbar'=>'نوار بالا','header'=>'سربرگ'];}
                if($field['key']==='approval'){$field['type']='select';$field['options']=['confirm'=>'تأییدشده','pending'=>'در انتظار'];}
            }unset($field);
        }
        $sections['account']['actions']['privacy']['fields']=[...$sections['account']['actions']['privacy']['fields'],$f('showBranches','نمایش شعبه‌ها','bool'),$f('showTeachers','نمایش مدرسان','bool'),$f('showContact','نمایش اطلاعات تماس','bool'),$f('showStats','نمایش آمار','bool'),$f('indexable','نمایش در موتورهای جستجو','bool')];
        foreach($sections['account']['actions']['document']['fields']as&$field){$field['key']=['document_type'=>'documentType','document_number'=>'documentNumber','issued_at'=>'issuedAt','expires_at'=>'expiresAt'][$field['key']]??$field['key'];if($field['key']==='documentType'){$field['type']='select';$field['options']=['license'=>'مجوز','identity'=>'هویتی','statute'=>'اساسنامه','tax'=>'مالیاتی','contract'=>'قرارداد','certificate'=>'گواهی','other'=>'سایر'];}}unset($field);
        foreach($sections['settings']['actions']['save']['fields']as&$field){
            if($field['key']==='primaryFont'){$field['type']='select';$field['options']=['source'=>'fonts','id'=>'value'];}
            if($field['key']==='fontScale'){$field['type']='select';$field['options']=['-2'=>'کوچک‌تر','-1'=>'کوچک','0'=>'عادی','1'=>'بزرگ','2'=>'بزرگ‌تر'];}
        }unset($field);
        // Only registered operations are exposed; no synthetic CRUD or HTML routes.
        foreach($sections as&$section){
            $path=$section['actions']['create']['path']??$section['actions']['list']['path'];
            if($section['access']==='management' && isset($section['actions']['update']) && !isset($section['actions']['status'])) $section['actions']['status']=$a($path.'/{id}/status','تغییر وضعیت');
            $section['actions']=array_filter($section['actions'],fn($action)=>Router::dispatch($action['method'],preg_replace('/\{\w+\}/','1',$action['path']))!==null);
            foreach($section['actions']as&$action){$route=Router::dispatch($action['method'],preg_replace('/\{\w+\}/','1',$action['path']));if(in_array('site-admin',$route['middlewares'],true))$action['admin']=true;}unset($action);
        } unset($section);
        $sections['pages']['detail']=$sections['page-content'];
        return $sections;
    }
    private function profileField(string $key): array {
        $labels=['title'=>'عنوان','issuer'=>'صادرکننده','date'=>'تاریخ','description'=>'توضیحات','issue_date'=>'تاریخ صدور','expire_date'=>'تاریخ انقضا','certificate_url'=>'نشانی گواهی','file_path'=>'مسیر فایل','organization'=>'سازمان','start_date'=>'شروع','end_date'=>'پایان','address_id'=>'نشانی','degree'=>'مدرک','institution'=>'مؤسسه','field'=>'رشته','event_date'=>'زمان رویداد','event_type'=>'نوع رویداد','publisher'=>'ناشر','published_date'=>'تاریخ انتشار','url'=>'نشانی اینترنتی','content'=>'متن','is_peer_reviewed'=>'داوری علمی','verification_level_id'=>'نشان','granted_at'=>'تاریخ اعطا','expires_at'=>'تاریخ انقضا','status'=>'وضعیت','question'=>'پرسش','type'=>'نوع','is_anonymous'=>'ناشناس','options'=>'گزینه‌ها'];
        $type=in_array($key,['is_peer_reviewed','is_anonymous'],true)?'bool':(str_contains($key,'date')||str_ends_with($key,'_at')?'date':(in_array($key,['content','description'],true)?'multiline':'text'));
        $options=null;
        if($key==='options')$type='strings';
        if($key==='address_id'||$key==='verification_level_id'){$type='select';$options=['source'=>$key==='address_id'?'addresses':'levels'];}
        if($key==='event_type'){$type='select';$options=['concert'=>'کنسرت','festival'=>'جشنواره','competition'=>'مسابقه','workshop'=>'کارگاه','other'=>'سایر'];}
        return $this->f($key,$labels[$key]??$key,$type,$options);
    }
}
