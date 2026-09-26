<?php
namespace Modules\System\Services {
    final class SiteAdminAccess { public static function allows($user): bool { return false; } }
}
namespace {
    $language = ($argv[1] ?? 'fa') === 'en' ? 'en' : 'fa';
    function locale() { return $GLOBALS['language']; }
    function direction() { return locale() === 'en' ? 'ltr' : 'rtl'; }
    function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
    function base_path($path = '') { return dirname(__DIR__).'/'.$path; }
    function csrf_token() { return 'fixture-csrf'; }
    function auth() { return new class { function user() { return null; } function check() { return false; } }; }
    function trans($key, $fallback = '') { return $fallback; }
    function component($name) { \Core\view\View::component($name); }
    require base_path('core/view/View.php');
    require base_path('Modules/CourseMarket/Services/CourseTranslations.php');
    $app = $app ?? ($argv[2] ?? 'course');
    $mode = $argv[3] ?? 'catalog';
    if ($app === 'chat') {
        echo (new \Core\view\View('Social::chat-frame'))->render();
        return;
    } elseif ($app === 'social') {
        $view = new \Core\view\View('Social::community', ['boot'=>[
            'api'=>'/community/api','userId'=>(int)($socialUser ?? 1),'csrf'=>csrf_token(),'locale'=>locale(),
        ]]);
    } else {
        $course = ['id'=>9,'title'=>'Music course','description'=>'Learn music','price'=>1200,'cover_id'=>null,'access'=>true,'files'=>[],
            'curriculum'=>[['title'=>'Basics','lessons'=>[['title'=>'First lesson','text'=>'Practice','media'=>[]]]]]];
        $view = new \Core\view\View('CourseMarket::index', ['mode'=>$mode,'items'=>[],'sales'=>[],
            'course'=>$mode === 'edit' ? null : $course,'order'=>['reference_id'=>'123','course_id'=>9],'message'=>'Fixture error']);
    }
    echo $view->layout('public-app')->render();
}
