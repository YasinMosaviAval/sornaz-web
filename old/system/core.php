<?php

spl_autoload_register(function (string $classname): void {

  if (str_ends_with($classname, 'Model')) {
    $filename = strtolower(str_replace('Model', '', $classname));
    $path = getcwd() . "/mvc/model/$filename.php";
    if (file_exists($path)) require $path;
    return;
  }

  if (str_ends_with($classname, 'Controller')) {
    $filename = strtolower(str_replace('Controller', '', $classname));

    // ابتدا دنبال sub-controller بگرد (مثل mvc/controller/page/api.php)
    $subPath = getcwd() . "/mvc/controller/$filename/";
    if (is_dir($subPath)) {
      // اگه پوشه وجود داشت، فایل page.php اصلی رو لود کن
      $path = getcwd() . "/mvc/controller/$filename.php";
    } else {
      $path = getcwd() . "/mvc/controller/$filename.php";
    }

    if (file_exists($path)) require $path;
    return;
  }

});
