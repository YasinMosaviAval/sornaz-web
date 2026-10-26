<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademySubscriptionService;

class AcademySubscriptionController
{
    public function __construct(private AcademySubscriptionService $s)
    {
    }

    public function index()
    {
        try {
            return ResponseFactory::json(['success' => true, 'data' => $this->s->index((int) auth()->id())]);
        } catch (\Throwable$e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function exportAccess()
    {
        try {
            $data = (new \Modules\Academy\Services\AcademyExportAccessService())->status((int) auth()->id(), $_GET);
            return ResponseFactory::json(['success' => true, 'data' => $data]);
        } catch (\Throwable $error) {
            $code = in_array($error->getCode(), [401,403,422], true) ? $error->getCode() : 503;
            return ResponseFactory::json(['success' => false, 'message' => $code === 503 ? 'دریافت وضعیت اشتراک خروجی‌ها امکان‌پذیر نیست.' : $error->getMessage()], $code);
        }
    }

    public function start(int $id)
    {
        try {
            $raw = base64_decode(strtr((string) request()->input('payload_b64', ''), '-_', '+/'), true);
            $data = $raw === false ? [] : (json_decode($raw, true) ?: []);
            return ResponseFactory::json(['success' => true, 'data' => $this->s->start((int) auth()->id(), $id, (int) ($data['months'] ?? 1))]);
        } catch (\Throwable$e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function callback()
    {
        try {
            $r = $this->s->callback((string) ($_GET['token'] ?? ''), (string) ($_GET['Authority'] ?? ''), (string) ($_GET['Status'] ?? ''));
            return redirect('/analytics/admin-panel?' . http_build_query(['subscription_payment' => !empty($r['requiresReview']) ? 'review' : ($r['success'] ? 'success' : 'failed'), 'ref' => $r['referenceId'] ?? '']) . '#finance');
        } catch (\Throwable$e) {
            return redirect('/analytics/admin-panel?' . http_build_query(['subscription_payment' => 'failed', 'message' => $e->getMessage()]) . '#finance');
        }
    }
}
