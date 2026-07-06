<?php


namespace Core\Http;

interface ResponseInterface
{
    public function send(): void;
}