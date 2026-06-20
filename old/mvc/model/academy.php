<?php

require_once 'old/old_model_academy.php';
require_once 'new/model_academy.php';

class AcademyModel  extends BaseModel {

    use OldModelAcademyTrait;
    use ModelAcademyTrait;

}
