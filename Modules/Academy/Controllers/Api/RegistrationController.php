<?php
namespace Modules\Academy\Controllers\Api;

use Core\database\DB;
use Core\http\ResponseFactory;
use Core\validation\ValidationException;
use Modules\System\Services\MobileAuthTokenService;

/** Native forms share website validation/OTP/services, with isolated flow state. */
class RegistrationController extends \Modules\Academy\Controllers\Web\AcademyRegistrationController {
    private const KEY='mobile_academy_registration';

    public function state() { return $this->run('state'); }
    public function sendCode() { return $this->run('send-code'); }
    public function submit() { return $this->run('submit'); }
    public function withoutBranch() { return $this->run('without-branch'); }

    private function run(string $operation) {
        header('Cache-Control: no-store, private');
        $oldLocale=app()->getLocale();
        app()->setLocale(str_starts_with(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']??''),'en')?'en':'fa');
        $otherOtp=session()->get('registration_otp');
        $flow=null;
        try {
            $user=app()->container()->make(MobileAuthTokenService::class)->userFromRequest();
            if (!$user && trim($_SERVER['HTTP_AUTHORIZATION']??'')!=='') return $this->failure(401);
            $actor=(int)($user['user_id']??0);
            $flow=session()->get(self::KEY);
            if ($flow && ($flow['expires_at']<time() || $flow['actor']!==$actor)) $flow=null;
            if (!$flow) {
                if ($operation!=='state') return $this->failure(409);
                $flow=['token'=>bin2hex(random_bytes(32)),'actor'=>$actor,'stage'=>'academy','expires_at'=>time()+86400];
            }
            if ($operation==='state') return $this->response($flow);
            if (!hash_equals($flow['token'],(string)($_SERVER['HTTP_X_ACADEMY_FLOW']??''))) return $this->failure(403);
            // Retried submissions return their previous result, without new records.
            $step=(string)($_POST['step']??'academy');
            if (!in_array($step,['academy','branch'],true)) return $this->failure(422);
            if ($flow['stage']==='complete' || ($operation==='submit' && $step==='academy' && $flow['stage']==='choice')) return $this->response($flow);
            if ($operation==='without-branch') {
                if ($flow['stage']!=='choice') return $this->failure(409);
                $flow['stage']='complete';$flow['without_branch']=true;unset($flow['otp']);
                return $this->response($flow);
            }
            if (($step==='academy'&&$flow['stage']!=='academy') || ($step==='branch'&&$flow['stage']!=='choice')) return $this->failure(409);
            session()->put('registration_otp',$flow['otp']??null);
            $data=$step==='academy'?$this->validatedData():$this->validatedBranchData();
            $bound=$this->otpData($data)+['mobile_stage'=>$step,'mobile_flow'=>$flow['token']];
            if ($operation==='send-code') {
                if ($actor===1) return ResponseFactory::json(['success'=>true,'otp_required'=>false]);
                $result=$this->otp->send($data['register_method'],(string)$data[$data['register_method']],$bound);
                $flow['otp']=session()->get('registration_otp');
                if (!$result['ok']) return ResponseFactory::json(['success'=>false,'message'=>isset($result['retry_after'])?$result['message']:$this->text('ارسال کد انجام نشد. دوباره تلاش کنید.','Could not send the code. Please try again.'),'retry_after'=>$result['retry_after']??0],isset($result['retry_after'])?429:503);
                return ResponseFactory::json(['success'=>true,'expires_in'=>$result['expires_in'],'retry_after'=>60]);
            }
            $verified=$actor===1 ? ['ok'=>true] : $this->otp->verify(trim((string)($_POST['otp']??'')),$bound);
            $flow['otp']=session()->get('registration_otp');
            if (!$verified['ok']) return ResponseFactory::json(['success'=>false,'message'=>$verified['message'],'errors'=>['otp'=>$verified['message']]],422);
            if ($step==='academy') {
                $id=$this->service->register($data,$actor);
                $academy=DB::table('academies')->where('academy_id',$id)->first();
                $flow['academy_id']=$id;$flow['manager_id']=(int)$academy['created_by'];
                $flow['academy_name']=$data['academy_name'];$flow['stage']='choice';
            } else {
                $flow['branch_id']=$this->service->registerMainBranch($flow['academy_id'],$flow['manager_id'],$data);
                $flow['stage']='complete';$flow['without_branch']=false;
            }
            unset($flow['otp']);$this->otp->clear();
            return $this->response($flow);
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success'=>false,'message'=>$this->text('اطلاعات فرم را بررسی کنید.','Please check the form.'),'errors'=>$e->getErrors()],422);
        } catch (\Throwable $e) {
            return $this->failure(500);
        } finally {
            if ($flow!==null) session()->put(self::KEY,$flow);
            if ($otherOtp===null) session()->forget('registration_otp');else session()->put('registration_otp',$otherOtp);
            app()->setLocale($oldLocale);
        }
    }
    private function response(array $flow) {
        $fallback=$this->text(
            "مدیر آموزشگاه مسئول صحت مشخصات، مجوزهای فعالیت و هویت عوامل معرفی‌شده است.\nاطلاعات دوره‌ها، شهریه، ظرفیت و زمان‌بندی باید شفاف و به‌روز باشد و حقوق مالکیت فکری رعایت شود.\nآموزشگاه باید در برابر درخواست‌ها و پرداخت‌های هنرجویان مطابق شرایط اعلام‌شده و قوانین جاری پاسخ‌گو باشد.\nاطلاعات هنرجویان و استادان فقط برای خدمات مجاز استفاده می‌شود و افشای آن بدون مجوز ممنوع است.\nسُرناز می‌تواند مدارک را بررسی و در صورت تخلف، اطلاعات نادرست یا شکایت معتبر، انتشار صفحه آموزشگاه را متوقف کند.",
            "The academy manager is responsible for accurate details, operating licenses and staff identities.\nCourse details, fees, capacity and schedules must be clear and current; intellectual property rights must be respected.\nAcademies must address student requests and payments under their published conditions and applicable rules.\nStudent and teacher information may only be used for authorized services and must not be disclosed without permission.\nSornaz may review documents and suspend publication for violations, incorrect information or valid complaints.");
        $terms=trans('academy.terms.content',$fallback);
        $terms=html_entity_decode(strip_tags(str_replace(['</p>','</h3>','<br>','<br/>'],"\n",$terms)),ENT_QUOTES|ENT_HTML5,'UTF-8');
        return ResponseFactory::json(['success'=>true,'otp_required'=>$flow['actor']!==1,'flow_token'=>$flow['token'],'stage'=>$flow['stage'],'academy_name'=>$flow['academy_name']??'','without_branch'=>$flow['without_branch']??false,'terms'=>$terms,'branch_terms'=>$this->text(
            "مدیر شعبه مسئول صحت اطلاعات تماس، نشانی، ساعات فعالیت و مجوزهای شعبه است.\nفعالیت‌های شعبه باید مطابق مقررات آموزشگاه مادر باشد.\nمدیر شعبه مسئول کیفیت خدمات، پاسخ‌گویی به هنرجویان و رعایت برنامه‌ها است.\nاطلاعات هنرجویان، استادان و کارکنان محرمانه است و فقط برای خدمات مجاز استفاده می‌شود.\nسُرناز و مدیر آموزشگاه می‌توانند در صورت تخلف یا شکایت معتبر فعالیت شعبه را بررسی یا محدود کنند.",
            "The branch manager is responsible for accurate contact details, address, opening hours and licenses.\nBranches must follow their academy's rules.\nManagers are responsible for service quality, student support and published schedules.\nStudent, teacher and staff information is confidential and may only be used for authorized services.\nSornaz and the academy manager may review or restrict branch activity for violations or valid complaints.")]);
    }
    private function text(string $fa,string $en):string{return locale()==='en'?$en:$fa;}
    private function failure(int $status){return ResponseFactory::json(['success'=>false,'message'=>match($status){401=>$this->text('برای ادامه دوباره وارد حساب شوید.','Please sign in again.'),403,409=>$this->text('نشست ثبت آموزشگاه معتبر نیست. صفحه را دوباره باز کنید.','Registration session expired. Please reopen the page.'),422=>$this->text('اطلاعات فرم را بررسی کنید.','Please check the form.'),default=>$this->text('ثبت درخواست انجام نشد. دوباره تلاش کنید.','Could not submit your request. Please try again.')}],$status);}
}
