<?php
namespace Modules\Analytics\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminTestDataService;

class AdminTestController
{
    public function __construct(protected AdminTestDataService $tests) {}

    public function deleteAvailability(string $id)
    {
        try {
            return ResponseFactory::json($this->tests->deleteAvailability((int) $id, (int) auth()->id()));
        } catch (\Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function deleteAvailabilityException(string $id)
    {
        try {
            return ResponseFactory::json($this->tests->deleteAvailabilityException((int) $id, (int) auth()->id()));
        } catch (\Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function saveInlineTranslation()
    {
        try {
            return ResponseFactory::json($this->tests->saveInlineTranslation((string) ($_POST['key'] ?? ''), (string) ($_POST['fa'] ?? ''), (string) ($_POST['en'] ?? ''), (int) auth()->id()));
        } catch (\Throwable $e) {
            return ResponseFactory::json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function inlineTranslations()
    {
        return ResponseFactory::json(['success' => true, 'translations' => $this->tests->inlineTranslationCatalog()]);
    }

    public function publicInlineTranslations()
    {
        header('Cache-Control: no-store');
        return ResponseFactory::json(['success' => true, 'translations' => $this->tests->inlineTranslationCatalog(true)]);
    }
}
