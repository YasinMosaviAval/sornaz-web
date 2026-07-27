<?php

namespace Core\Http;

class RedirectResponse implements ResponseInterface {


    protected string $url;
    protected array $oldInput = [];
    protected array $errors = [];



    public function __construct(string $url) {
        $this->url = $url;
    }



    public function send(): void {
        register_shutdown_function(function () {
            session()->forget('_old_input');
            session()->forget('_errors');
        });
        header("Location: {$this->url}");
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