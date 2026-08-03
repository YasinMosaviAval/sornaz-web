<?php


namespace Core\http;

interface ResponseInterface {
    public function send(): void;
}