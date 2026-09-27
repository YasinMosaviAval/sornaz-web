<?php
namespace Modules\Analytics\Controllers\Web;

use Core\http\DownloadResponse;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\PublicAccountMediaService;

final class PublicAccountMediaController
{
    public function library(string $year, string $month, string $filename)
    {
        try {
            $file = (new PublicAccountMediaService())->library($year, $month, $filename, auth()->user());
            return new DownloadResponse($file['path'], $file['name'], $file['mime'], $file['inline']);
        } catch (\RuntimeException $e) {
            if ($e->getCode() !== 404) throw $e;
            return ResponseFactory::json(['success'=>false, 'message'=>'File not found'], 404);
        }
    }

    public function show(string $user, string $year, string $month, string $filename)
    {
        try {
            $file = (new PublicAccountMediaService())->find($user, $year, $month, $filename);
            return new DownloadResponse($file['path'], $file['name'], $file['mime'], true);
        } catch (\RuntimeException $e) {
            if ($e->getCode() !== 404) throw $e;
            return ResponseFactory::json(['success'=>false, 'message'=>'File not found'], 404);
        }
    }
}
