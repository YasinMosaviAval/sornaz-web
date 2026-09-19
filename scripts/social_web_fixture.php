<?php
$boot = ['api'=>'/community/api','userId'=>(int)($argv[2]??1),'csrf'=>'fixture-csrf','locale'=>($argv[1]??'fa')==='en'?'en':'fa'];
require __DIR__.'/../Modules/Social/Resources/Views/community.php';
