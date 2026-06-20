<?php

require_once 'old/old_model_account.php';
require_once 'new/model_account.php';

class AccountModel {

    use OldModelAccountTrait;
    use ModelAccountTrait;

}
