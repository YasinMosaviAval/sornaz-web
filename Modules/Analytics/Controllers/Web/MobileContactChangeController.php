<?php
namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\MobileContactChangeService;
use RuntimeException;
use Throwable;

final class MobileContactChangeController
{
    public function __construct(private MobileContactChangeService $service)
    {
    }

    public function send() { return $this->run(fn (int $actor, array $data) => $this->service->send($actor, (string) ($data['field'] ?? ''), (string) ($data['destination'] ?? ''))); }
    public function verify() { return $this->run(function (int $actor, array $data) { $this->service->verify($actor, (string) ($data['field'] ?? ''), (string) ($data['destination'] ?? ''), (string) ($data['code'] ?? '')); return null; }); }
    public function commit() { return $this->run(function (int $actor, array $data) { $this->service->commit($actor, (string) ($data['field'] ?? ''), (string) ($data['destination'] ?? '')); return null; }); }

    private function run(callable $action)
    {
        try {
            $raw = (string) request()->input('payload_b64', '');
            $decoded = base64_decode(strtr($raw, '-_', '+/'), true);
            $data = $decoded === false ? null : json_decode($decoded, true);
            if (!is_array($data)) throw new RuntimeException('اطلاعات تماس معتبر نیست.', 422);
            return ResponseFactory::json(['success' => true, 'data' => $action((int) auth()->id(), $data)]);
        } catch (Throwable $error) {
            $expected = $error instanceof RuntimeException && !($error instanceof \PDOException);
            $status = $expected && in_array($error->getCode(), [403, 422, 429], true) ? $error->getCode() : ($expected ? 422 : 500);
            return ResponseFactory::json(['success' => false, 'message' => $expected ? $error->getMessage() : 'انجام عملیات در حال حاضر ممکن نیست.'], $status);
        }
    }
}
