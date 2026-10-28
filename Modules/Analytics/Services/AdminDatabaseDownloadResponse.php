<?php

namespace Modules\Analytics\Services;

use Core\http\ResponseInterface;

/** Owns a private, automatically deleted temporary stream; it is never a public media file. */
final class AdminDatabaseDownloadResponse implements ResponseInterface
{
    public function __construct(private mixed $stream, private string $filename, private string $consistency) {}

    public function send(): void
    {
        if (!is_resource($this->stream)) { http_response_code(410); return; }
        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $this->filename . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');
        header('Referrer-Policy: no-referrer');
        header("Content-Security-Policy: sandbox; default-src 'none'");
        header('X-Sornaz-Export-Consistency: ' . $this->consistency);
        header('Content-Length: ' . fstat($this->stream)['size']);
        if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
        try {
            rewind($this->stream);
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') return;
            while (!feof($this->stream) && !connection_aborted()) {
                $chunk = fread($this->stream, 65536);
                if ($chunk === false || $chunk === '') break;
                echo $chunk;
            }
        } finally { $this->close(); }
    }

    private function close(): void { if (is_resource($this->stream)) fclose($this->stream); }
    public function __destruct() { $this->close(); }
}
