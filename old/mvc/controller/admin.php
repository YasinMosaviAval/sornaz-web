<?php

require_once 'admin/pages.php';
require_once 'admin/select_queries.php';
require_once 'admin/create_queries.php';
require_once 'admin/update_queries.php';
require_once 'admin/delete_queries.php';
require_once 'admin/api.php'; 

class AdminController extends BaseController {

    public function __construct() {
        // // grantAdmin();
        // grantPanel();
        // // grantAcademyManager();
    }

    use AdminPagesTrait;
    use AdminSelectQueriesTrait;
    use AdminCreateQueriesTrait;
    use AdminUpdateQueriesTrait;
    use AdminDeleteQueriesTrait;
    use AdminApiTrait;

}



// public static function delete_academy($academy_id){
//   return Db::getInstance()->modify("DELETE FROM academies WHERE academy_id=:academy_id", array('academy_id' => $academy_id));
// }



// public static function soft_delete_academy(int $academy_id, int $deleted_by, string $deteled_at){
//     return Db::getInstance()->modify("UPDATE academies SET deleted_by=:deleted_by, deleted_at=:deleted_at, updated_by=:updated_by, updated_at=:updated_at WHERE academy_id=:academy_id"
//     , array(
//         'academy_id' => $academy_id,
//         'deleted_by' => $deleted_by,
//         'deleted_at' => $deteled_at,
//         'updated_by' => $deleted_by,
//         'updated_at' => $deteled_at
//     ));
// }