<?php

require_once 'old/old_model_admin.php';
require_once 'new/model_admin.php';

class AdminModel {

    use OldModelAdminTrait;
    use ModelAdminTrait;

}
