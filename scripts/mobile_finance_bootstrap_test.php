<?php
namespace Core\http {
    final class ResponseFactory { public static function json(array $data): array { return $data; } }
}
namespace Modules\Academy\Services {
    class AcademyTermService {
        public function invoiceBootstrap(int $actor): array { return ['invoices'=>[['id'=>15,'amount'=>100]], 'actor'=>$actor]; }
    }
    class AcademySubscriptionService {
        public static array $actors=[];
        public static bool $fail=false;
        public function index(int $actor): array {
            self::$actors[]=$actor;
            if (self::$fail) throw new \RuntimeException('Fixture storage unavailable');
            return ['periods'=>[['id'=>1,'statusCode'=>'free','amount'=>0],['id'=>2,'statusCode'=>'pending','amount'=>2000000]],'plans'=>[['months'=>1,'amount'=>2000000]]];
        }
    }
}
namespace {
    require __DIR__.'/../Modules/Academy/Controllers/Web/AcademyTermController.php';
    function auth(): object { return new class { public function id(): int { return 91; } }; }
    function verify(bool $value, string $message): void { if (!$value) throw new \RuntimeException($message); }
    $controller=new \Modules\Academy\Controllers\Web\AcademyTermController(new \Modules\Academy\Services\AcademyTermService());
    $legacy=$controller->invoices()['data'];
    verify(!isset($legacy['subscriptions']) && \Modules\Academy\Services\AcademySubscriptionService::$actors===[], 'Legacy invoice requests changed');
    $_SERVER['HTTP_X_SORNAZ_PANEL_FEATURES']='3';
    $mobile=$controller->invoices()['data'];
    verify($mobile['invoices']===$legacy['invoices'], 'Subscription integration changed invoice amounts');
    verify(array_column($mobile['subscriptions']['periods'],'statusCode')===['free','pending'], 'Native finance lost subscription periods');
    verify(\Modules\Academy\Services\AcademySubscriptionService::$actors===[91], 'Subscription scope did not use authenticated actor');
    \Modules\Academy\Services\AcademySubscriptionService::$fail=true;
    $partial=$controller->invoices()['data'];
    verify($partial['invoices']===$legacy['invoices'] && $partial['subscriptionUnavailable']===true, 'Subscription failure hid existing invoices');
    echo "Native finance: opt-in aggregation, actor scope, subscription periods, legacy compatibility and partial failure passed.\n";
}
