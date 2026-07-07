<?php

use Core\View\View;

?>

<div class="sn-sidebar">
    <div class="sn-sidebar-header">
        <div class="sn-logo">
            SORNAZ
        </div>
    </div>

    <div class="sn-sidebar-menu">
        <?php
            View::component('layout.sidebar-item',[
                'icon'=>'home',
                'title'=>'داشبورد',
                'url'=>'/dashboard'
            ]);
            View::component('layout.sidebar-item',[
                'icon'=>'school',
                'title'=>'آموزشگاه‌ها',
                'url'=>'/academies'
            ]);
            View::component('layout.sidebar-item',[
                'icon'=>'branch',
                'title'=>'شعب',
                'url'=>'/branches'
            ]);
        ?>
    </div>
</div>