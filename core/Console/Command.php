<?php

namespace Core\Console;

abstract class Command {
    abstract public function handle(array $arguments): int;



    protected function info(string $message): void {
        echo "\033[32m{$message}\033[0m\n";
    }



    protected function warning(string $message): void {
        echo "\033[33m{$message}\033[0m\n";
    }



    protected function error(string $message): void {
        echo "\033[31m{$message}\033[0m\n";
    }


}