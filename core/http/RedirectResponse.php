<?php

namespace Core\Http;

// class RedirectResponse implements ResponseInterface
// {
//     protected string $url;

//     protected int $status;

//     public function __construct(
//         string $url,
//         int $status = 302
//     ) {
//         $this->url = $url;
//         $this->status = $status;
//     }

//     public function send(): void
//     {
//         header(
//             'Location: ' . $this->url,
//             true,
//             $this->status
//         );

//         exit;
//     }
// }


class RedirectResponse implements ResponseInterface
{
    protected string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function send(): void
    {
        header("Location: {$this->url}");
        exit;
    }
}