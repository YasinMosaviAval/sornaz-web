<?php
namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Core\security\PublicError;
use Modules\Analytics\Services\AdminGuideDocumentService;
use Modules\Analytics\Services\AdminGuideService;
use RuntimeException;

final class AdminGuideDocumentController
{
    public function __construct(private AdminGuideDocumentService $files, private AdminGuideService $guides) {}

    public function read() { return $this->run(fn () => $this->files->read(1, $this->text($_GET, 'path'))); }

    public function update()
    {
        return $this->run(function () {
            $document = $this->files->update(1, $this->text($_POST, 'path'), $this->text($_POST, 'content'), $this->text($_POST, 'version'));
            return $document + ['html' => $this->guides->documentHtml($document['content'])];
        });
    }

    public function delete()
    {
        return $this->run(function () {
            $this->files->delete(1, $this->text($_POST, 'path'), $this->text($_POST, 'version'));
            return null;
        });
    }

    private function text(array $data, string $key): string
    {
        if (!is_string($data[$key] ?? null)) throw new RuntimeException('اطلاعات فایل راهنما معتبر نیست.', 422);
        return $data[$key];
    }

    private function run(callable $work)
    {
        header('Cache-Control: private, no-store');
        if ((int) auth()->id() !== 1) return ResponseFactory::json(['success' => false, 'message' => 'این عملیات فقط برای مدیر سایت مجاز است.'], 403);
        try { return ResponseFactory::json(['success' => true, 'data' => $work()]); }
        catch (\Throwable $error) { return ResponseFactory::json(['success' => false, 'message' => PublicError::message($error)], PublicError::status($error)); }
    }
}
