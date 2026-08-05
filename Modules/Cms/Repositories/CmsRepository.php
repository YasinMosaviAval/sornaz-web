<?php

namespace Modules\Cms\Repositories;

use Core\database\Repository;
use Modules\Cms\Models\CmsModel;

class CmsRepository extends Repository {

    protected ?string $model = CmsModel::class;
    protected string $table = 'cmss';
    protected string $primaryKey = 'cms_id';

}
