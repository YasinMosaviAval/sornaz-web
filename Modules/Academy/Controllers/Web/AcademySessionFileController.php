<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Core\security\PublicError;
use Modules\Academy\Services\AcademySessionFileService;

final class AcademySessionFileController
{
    public function __construct(private AcademySessionFileService $service) {}

    public function index(int $id) { return $this->run(fn () => ResponseFactory::json(['success' => true, 'data' => $this->service->index((int) auth()->id(), $id)])); }
    public function upload(int $id) { return $this->run(fn () => ResponseFactory::json(['success' => true, 'data' => $this->service->upload((int) auth()->id(), $id, $_FILES['file'] ?? [])])); }
    public function download(int $id, int $file) { return $this->run(fn () => $this->service->download((int) auth()->id(), $id, $file)); }
    public function delete(int $id, int $file) { return $this->run(function () use ($id, $file) { $this->service->delete((int) auth()->id(), $id, $file); return ResponseFactory::json(['success' => true]); }); }

    private function run(callable $work)
    {
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');
        try { return $work(); }
        catch (\Throwable $error) {
            $status = $error instanceof \RuntimeException && !($error instanceof \PDOException) && $error->getCode() === 413 ? 413 : PublicError::status($error);
            return ResponseFactory::json(['success' => false, 'message' => PublicError::message($error)], $status);
        }
    }
}
