<?php

namespace Modules\Academy\Controllers\Web;

use Core\database\DB;
use Core\http\ResponseFactory;
use Core\validation\ValidationException;
use Modules\Academy\Requests\AcademyRegistrationRequest;
use Modules\Academy\Services\AcademyRegistrationService;
use Modules\Academy\Services\AcademyBranchService;
use Modules\System\Services\RegistrationOtpService;
use Modules\System\Services\SiteAdminAccess;
use Throwable;

class AcademyRegistrationController {


    public function __construct(
        protected AcademyRegistrationService $service,
        protected RegistrationOtpService $otp,
        protected AcademyBranchService $branchService
    ) {}


    public function create() {
        return ResponseFactory::view('Academy::send-academy-request')->layout('main')->title(trans('academy.meta.title', 'سُرناز | ثبت آموزشگاه'));
    }

    public function createMainBranch() {
        $setup = session()->get('academy_branch_setup');
        if (!$setup || empty($setup['academy_id'])) return redirect('/academy/send-academy-request');
        return ResponseFactory::view('Academy::register-main-branch', ['academy' => $setup])->layout('main')->title('سُرناز | ثبت شعبه اصلی');
    }

    public function createAdminBranchDialog() {
        $academyId = (int)($_GET['academy_id'] ?? 0);
        if (!SiteAdminAccess::allows(auth()->user())) $academyId = (int)$this->branchService->academyForUser((int)auth()->id())['academy_id'];
        if ($academyId < 1 || !DB::table('academies')->where('academy_id', $academyId)->whereNull('deleted_at')->first()) abort(404);
        return ResponseFactory::view('Academy::admin-branch-registration-dialog', ['academyId' => $academyId]);
    }

    public function sendAdminBranchOtp() {
        try {
            $data = $this->validatedAdminBranchData();
            $result = $this->otp->send($data['register_method'], (string)$data[$data['register_method']], $this->otpData($data));
            return ResponseFactory::json(['success' => $result['ok']] + $result, $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 503));
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success' => false, 'message' => 'اطلاعات فرم را بررسی کنید.', 'errors' => $e->getErrors()], 422);
        }
    }

    public function storeAdminBranch() {
        try {
            $data = $this->validatedAdminBranchData();
            $verification = $this->otp->verify(trim((string)($_POST['otp'] ?? '')), $this->otpData($data));
            if (!$verification['ok']) throw new ValidationException(['otp' => $verification['message']]);
            $type = DB::table('academy_branch_types')->whereNull('deleted_at')->orderBy('academy_branch_type_id')->first();
            if (!$type) throw new \RuntimeException('نوع آموزشی معتبری برای ثبت شعبه یافت نشد.');
            $this->branchService->store((int)auth()->id(), [
                'academy_id'=>$data['academy_id'], 'name'=>$data['name'], 'username'=>$data['username'],
                'email'=>$data['email'], 'phone'=>$data['phone'], 'password'=>$data['password'], 'password2'=>$data['password2'],
                'type_id'=>(int)$type['academy_branch_type_id'], 'physical_type'=>'physical', 'status'=>'active',
                'slogan'=>$data['slogan'], 'short_description'=>$data['short_description'], 'bio'=>$data['biography'],
                'phones'=>[], 'links'=>[], 'addresses'=>[],
            ], SiteAdminAccess::allows(auth()->user()));
            $this->otp->clear();
            session()->flash('admin_test_message', 'شعبه جدید با موفقیت ثبت شد.');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            session()->flash('admin_test_error', reset($errors) ?: 'ثبت شعبه انجام نشد.');
        } catch (Throwable $e) {
            error_log('[Admin Branch Registration Error] ' . $e->getMessage());
            session()->flash('admin_test_error', $e->getMessage());
        }
        return redirect('/analytics/admin-panel#branches');
    }


    public function index() {
        return ResponseFactory::view('Analytics::academies', ['academies' => $this->service->all()])->layout('main')->title('سُرناز | آموزشگاه‌ها');
    }


    public function seedSamples() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->service->seedSamples((int)($_POST['academy_count'] ?? 10));
            session()->flash('admin_test_message', $result['message']);
            return redirect('/analytics/admin-panel');
        } catch (Throwable $e) {
            session()->flash('admin_test_error', 'ایجاد آموزشگاه‌های نمونه ناموفق بود: ' . $e->getMessage());
            return redirect('/analytics/admin-panel');
        }
    }


    public function seedBranchNetwork() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->service->seedBranchNetwork([
                'branches_min'=>(int)($_POST['branches_min']??0),'branches_max'=>(int)($_POST['branches_max']??5),
                'teachers_min'=>(int)($_POST['teachers_min']??1),'teachers_max'=>(int)($_POST['teachers_max']??5),
                'receptionists_min'=>(int)($_POST['receptionists_min']??1),'receptionists_max'=>(int)($_POST['receptionists_max']??5),
                'employees_min'=>(int)($_POST['employees_min']??0),'employees_max'=>(int)($_POST['employees_max']??3),
                'managers_min'=>(int)($_POST['managers_min']??0),'managers_max'=>(int)($_POST['managers_max']??3),
                'students_min'=>(int)($_POST['students_min']??0),'students_max'=>(int)($_POST['students_max']??5),
            ]);
            session()->flash('admin_test_message', $result['message']);
        } catch (Throwable $e) {
            session()->flash('admin_test_error', 'ایجاد شبکه شعب و اعضا ناموفق بود: ' . $e->getMessage());
        }
        return redirect('/analytics/admin-panel');
    }


    public function deleteBranchNetwork() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->service->deleteBranchNetwork();
            session()->flash('admin_test_message', $result['message']);
        } catch (Throwable $e) {
            session()->flash('admin_test_error', 'حذف معکوس تست ۳ ناموفق بود: ' . $e->getMessage());
        }
        return redirect('/analytics/admin-panel');
    }


    public function deleteSamples() {
        if (env('APP_ENV', 'production') !== 'local') abort(404);
        try {
            $result = $this->service->deleteSamples();
            session()->flash('admin_test_message', $result['message']);
            return redirect('/analytics/admin-panel');
        } catch (Throwable $e) {
            session()->flash('admin_test_error', 'حذف اطلاعات نمونه ناموفق بود: ' . $e->getMessage());
            return redirect('/analytics/admin-panel');
        }
    }


    public function store() {
        try {
            $data = $this->validatedData();
            $verification = $this->otp->verify(trim((string)($_POST['otp'] ?? '')), $this->otpData($data));
            if (!$verification['ok']) return $this->back(['otp' => $verification['message']]);
            $academyId = $this->service->register($data);
            $this->otp->clear();
            session()->put('academy_branch_setup', ['academy_id' => $academyId, 'manager_id' => (int)auth()->id()]);
            session()->flash('auth_success', 'آموزشگاه با موفقیت ثبت شد. اکنون اطلاعات شعبه اصلی را ثبت کنید.');
            return redirect('/academy/register-main-branch');
        } catch (ValidationException $e) {
            return $this->back($e->getErrors());
        } catch (Throwable $e) {
            return $this->back(['academy_name' => trans('academy.error.request_failed', 'ثبت درخواست انجام نشد. لطفاً دوباره تلاش کنید.')]);
        }
    }


    public function sendOtp() {
        try {
            $data = $this->validatedData();
            $method = $data['register_method'];
            $result = $this->otp->send($method, (string)$data[$method], $this->otpData($data));
            return ResponseFactory::json(['success' => $result['ok']] + $result, $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 503));
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success' => false, 'message' => trans('academy.error.review_form', 'اطلاعات فرم را بررسی کنید.'), 'errors' => $e->getErrors()], 422);
        }
    }

    public function sendMainBranchOtp() {
        try {
            $data = $this->validatedBranchData();
            $result = $this->otp->send($data['register_method'], (string)$data[$data['register_method']], $this->otpData($data));
            return ResponseFactory::json(['success' => $result['ok']] + $result, $result['ok'] ? 200 : (isset($result['retry_after']) ? 429 : 503));
        } catch (ValidationException $e) {
            return ResponseFactory::json(['success' => false, 'message' => 'اطلاعات فرم را بررسی کنید.', 'errors' => $e->getErrors()], 422);
        }
    }

    public function storeMainBranch() {
        try {
            $setup = session()->get('academy_branch_setup');
            if (!$setup || empty($setup['academy_id'])) return redirect('/academy/send-academy-request');
            $data = $this->validatedBranchData();
            $verification = $this->otp->verify(trim((string)($_POST['otp'] ?? '')), $this->otpData($data));
            if (!$verification['ok']) return redirect('/academy/register-main-branch')->withInput($_POST)->withErrors(['otp' => $verification['message']]);
            $this->service->registerMainBranch((int)$setup['academy_id'], (int)$setup['manager_id'], $data);
            $this->otp->clear();
            session()->forget('academy_branch_setup');
            session()->flash('auth_success', 'شعبه اصلی با موفقیت ثبت شد.');
            return redirect('/');
        } catch (ValidationException $e) { return redirect('/academy/register-main-branch')->withInput($_POST)->withErrors($e->getErrors());
        } catch (Throwable $e) {
            error_log('[Main Branch Registration Error] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect('/academy/register-main-branch')->withInput($_POST)->withErrors(['username' => 'ثبت شعبه انجام نشد. لطفاً دوباره تلاش کنید.']);
        }
    }



    private function validatedData(): array {
        $data = (new AcademyRegistrationRequest($_POST))->validated();
        if (empty($_POST['terms'])) throw new ValidationException(['terms' => trans('academy.error.terms_required', 'پذیرش قوانین ثبت آموزشگاه الزامی است.')]);
        $method = $data['register_method'];
        if ($method === 'email') {
            $data['email'] = strtolower(trim((string)($data['email'] ?? '')));
            $data['phone'] = null;
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) throw new ValidationException(['email' => trans('academy.error.email_invalid', 'ایمیل معتبر را وارد کنید.')]);
        } else {
            $data['phone'] = preg_replace('/\D+/', '', (string)($data['phone'] ?? ''));
            $data['email'] = null;
            if (!preg_match('/^09\d{9}$/', $data['phone'])) throw new ValidationException(['phone' => trans('academy.error.phone_invalid', 'شماره موبایل معتبر را وارد کنید.')]);
        }
        return $data;
    }


    private function otpData(array $data): array {
        unset($data['password2'], $data['terms'], $data['otp']);
        ksort($data);
        return $data;
    }

    private function validatedBranchData(): array {
        $method = ($_POST['register_method'] ?? 'email') === 'phone' ? 'phone' : 'email';
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $password2 = (string)($_POST['password2'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_]{3,100}$/', $username)) throw new ValidationException(['username' => 'نام کاربری باید بین ۳ تا ۱۰۰ کاراکتر و فقط شامل حروف انگلیسی، عدد و _ باشد.']);
        if (DB::table('users')->where('username', $username)->whereNull('deleted_at')->first()) throw new ValidationException(['username' => 'این نام کاربری قبلاً ثبت شده است.']);
        if (strlen($password) < 8 || $password !== $password2) throw new ValidationException(['password' => 'رمز عبور باید حداقل ۸ کاراکتر باشد و تکرار آن یکسان باشد.']);
        $value = trim((string)($_POST[$method] ?? ''));
        if ($method === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) throw new ValidationException(['email' => 'ایمیل معتبر را وارد کنید.']);
        if ($method === 'phone') { $value = preg_replace('/\D+/', '', $value); if (!preg_match('/^09\d{9}$/', $value)) throw new ValidationException(['phone' => 'شماره موبایل معتبر را وارد کنید.']); }
        if (DB::table('users')->where($method, $value)->whereNull('deleted_at')->first()) {
            throw new ValidationException([$method => $method === 'email' ? 'این ایمیل قبلاً ثبت شده است.' : 'این شماره موبایل قبلاً ثبت شده است.']);
        }
        if (empty($_POST['terms'])) throw new ValidationException(['terms' => 'پذیرش قوانین ثبت و فعالیت شعبه الزامی است.']);
        $name = trim((string)($_POST['academy_name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 255) throw new ValidationException(['academy_name' => 'نام شعبه الزامی است و حداکثر ۲۵۵ نویسه دارد.']);
        return ['register_method'=>$method, 'username'=>$username, 'password'=>$password, 'password2'=>$password2,
            'email'=>$method==='email'?$value:null, 'phone'=>$method==='phone'?$value:null,
            'name'=>$name, 'slogan'=>trim((string)($_POST['slogan']??'')),
            'short_description'=>trim((string)($_POST['short_description']??'')),
            'biography'=>trim((string)($_POST['biography']??''))];
    }

    private function validatedAdminBranchData(): array {
        $data = $this->validatedBranchData();
        $academyId = (int)($_POST['academy_id'] ?? 0);
        if (!SiteAdminAccess::allows(auth()->user())) $academyId = (int)$this->branchService->academyForUser((int)auth()->id())['academy_id'];
        if ($academyId < 1 || !DB::table('academies')->where('academy_id', $academyId)->whereNull('deleted_at')->first()) throw new ValidationException(['academy_id' => 'آموزشگاه مقصد معتبر نیست.']);
        $data['academy_id'] = $academyId;
        return $data;
    }


    private function back(array $errors) {
        return redirect('/academy/send-academy-request')->withInput($_POST)->withErrors($errors);
    }


}
