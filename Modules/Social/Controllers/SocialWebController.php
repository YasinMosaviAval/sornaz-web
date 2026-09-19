<?php
namespace Modules\Social\Controllers;

use Core\http\ResponseFactory;

/** Cookie authentication is confined to /community/api; mobile remains bearer-only. */
class SocialWebController extends SocialController
{
    protected function viewer(bool $public = false): int
    {
        header('Cache-Control: private, no-store');
        $id = (int)auth()->id();
        $write = ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET';
        if (($write || !$public) && !$id) throw new \RuntimeException('برای ادامه وارد حساب شوید.', 401);
        if ($write) {
            $token = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
            if ($token === '' || !csrf()->verify($token)) throw new \RuntimeException('صفحه را تازه کنید و دوباره تلاش کنید.', 403);
        }
        return $id;
    }

    public function page(int $id = 0)
    {
        header('Cache-Control: private, no-store');
        return ResponseFactory::view('Social::community', ['boot' => [
            'api' => '/community/api', 'userId' => (int)auth()->id(),
            'csrf' => csrf_token(), 'locale' => locale() === 'en' ? 'en' : 'fa',
        ]]);
    }
}
