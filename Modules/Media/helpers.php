<?php

if(!function_exists('public_path')){

    function public_path(string $path=''): string{
        return base_path('public/'.$path);
    }

}