<?php

function base_path(string $path = '') : string {
    return dirname(__DIR__,2)
        . DIRECTORY_SEPARATOR
        . $path;
}


function app() {
    return \Core\Application\Application::getInstance();
}