<?php
namespace Modules\Education\Controllers\Api;

use Core\http\ResponseFactory;
use Modules\Education\Services\PracticeResultService;
use Modules\System\Repositories\UserRepository;
use Modules\System\Services\MobileAuthTokenService;

final class PracticeController
{
    public function index() { return $this->run('read'); }
    public function store() { return $this->run('write'); }
    public function importLegacy() { return $this->run('legacy'); }
    private function run(string $operation)
    {
        header('Cache-Control: no-store, private');
        try {
            $user = (new MobileAuthTokenService(new UserRepository()))->userFromRequest();
            if (!$user) throw new \RuntimeException('نشست معتبر نیست. دوباره وارد شوید.', 401);
            $actor = (int) $user['user_id'];
            $service = new PracticeResultService();
            if ($operation !== 'read') {
                $raw = $_POST['payload'] ?? null;
                if (!is_string($raw) || strlen($raw) > 262144) throw new \RuntimeException('حجم یا قالب درخواست معتبر نیست.', 422);
                $input = json_decode($raw, true);
                if (!is_array($input)) throw new \RuntimeException('قالب نتیجه معتبر نیست.', 422);
                $data = $operation === 'legacy' ? $service->importLegacy($actor, $input) : $service->store($actor, $input);
            } else {
                $data = isset($_GET['runId']) ? $service->details($actor, (string) $_GET['runId'], (int) ($_GET['after'] ?? 0)) : $service->progress($actor, (string) ($_GET['music'] ?? 'western'));
            }
            return ResponseFactory::json(['success' => true, 'data' => $data]);
        } catch (\Throwable $error) {
            $status = in_array($error->getCode(), [401, 404, 409, 422, 503], true) ? (int) $error->getCode() : 500;
            $message = $status === 500 ? 'ثبت یا دریافت نتایج تمرین انجام نشد.' : $error->getMessage();
            return ResponseFactory::json(['success' => false, 'message' => $message], $status);
        }
    }
}
