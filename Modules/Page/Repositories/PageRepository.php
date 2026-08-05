<?php

namespace Modules\Page\Repositories;

use Core\database\Repository;
use Modules\Page\Models\PageModel;

class PageRepository extends Repository {

    protected ?string $model = PageModel::class;
    protected string $table = 'pages';
    protected string $primaryKey = 'page_id';

}
