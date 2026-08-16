<?php

namespace Core\http;

final class DownloadResponse implements ResponseInterface
{
    public function __construct(private string $path, private string $filename, private string $mime='application/octet-stream') {}
    public function send(): void
    {
        if(!is_file($this->path)){http_response_code(404);echo 'File not found';return;}
        header('Content-Type: '.$this->mime);
        header('Content-Length: '.filesize($this->path));
        header('Content-Disposition: attachment; filename="'.str_replace(['"',"\r","\n"],'',$this->filename).'"');
        header('X-Content-Type-Options: nosniff');
        readfile($this->path);
    }
}
