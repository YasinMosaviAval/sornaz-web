<?php

namespace Modules\Media\Repositories;

use Core\database\Repository;
use Modules\Media\Models\MediaModel;

class MediaRepository extends Repository {

    protected ?string $model = MediaModel::class;
    protected string $table = 'medias';
    protected string $primaryKey = 'media_id';

}
