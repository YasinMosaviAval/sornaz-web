<?php

namespace Core\http;

final class DownloadResponse implements ResponseInterface
{
    public function __construct(private string $path, private string $filename, private string $mime='application/octet-stream', private bool $inline=false) {}
    public function send(): void
    {
        if(!is_file($this->path)){http_response_code(404);echo 'File not found';return;}
        header('Content-Type: '.$this->mime);
        header('Content-Disposition: '.($this->inline?'inline':'attachment').'; filename="'.str_replace(['"',"\r","\n"],'',$this->filename).'"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        header("Content-Security-Policy: sandbox; default-src 'none'");
        if (session_status() === PHP_SESSION_ACTIVE) session_write_close();
        $size = filesize($this->path);
        $start = 0;
        $end = $size - 1;
        if ($this->inline) {
            header('Accept-Ranges: bytes');
            if (isset($_SERVER['HTTP_RANGE'])) {
                $valid = preg_match('/^bytes=(\d*)-(\d*)$/D', $_SERVER['HTTP_RANGE'], $range)
                    && ($range[1] !== '' || $range[2] !== '');
                if ($valid) {
                    if ($range[1] === '') $start = max(0, $size - (int)$range[2]);
                    else {
                        $start = (int)$range[1];
                        if ($range[2] !== '') $end = min($end, (int)$range[2]);
                    }
                }
                if (!$valid || $start > $end || $start >= $size) {
                    http_response_code(416);
                    header('Content-Range: bytes */'.$size);
                    return;
                }
                http_response_code(206);
                header("Content-Range: bytes $start-$end/$size");
            }
        }
        header('Content-Length: '.max(0, $end - $start + 1));
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') return;
        $file = fopen($this->path, 'rb');
        if ($file === false) { http_response_code(404); return; }
        fseek($file, $start);
        $remaining = $end - $start + 1;
        while ($remaining > 0 && !feof($file) && !connection_aborted()) {
            $chunk = fread($file, min(65536, $remaining));
            if ($chunk === false || $chunk === '') break;
            echo $chunk;
            $remaining -= strlen($chunk);
        }
        fclose($file);
    }
}
