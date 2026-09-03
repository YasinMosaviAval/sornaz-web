<?php

namespace Core\http;

class RedirectResponse implements ResponseInterface {


    protected string $url;
    protected int $status;
    protected array $oldInput = [];
    protected array $errors = [];



    public function __construct(string $url, int $status = 302) {
        $this->url = $url;
        $this->status = in_array($status, [301, 302, 303, 307, 308], true) ? $status : 302;
    }



    public function send(): void {
        register_shutdown_function(function () {
            session()->forget('_old_input');
            session()->forget('_errors');
        });
        header("Location: {$this->url}", true, $this->status);
        exit;
    }



    public function withInput(array $input): static {
        session()->put('_old_input', $input);
        return $this;
    }



    public function withErrors(array $errors): static {
        session()->put('_errors', $errors);
        return $this;
    }





}
