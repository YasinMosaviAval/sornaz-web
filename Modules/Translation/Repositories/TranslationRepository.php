<?php

namespace Modules\Translation\Repositories;

use Core\database\Repository;
use Modules\Translation\Models\TranslationModel;

class TranslationRepository extends Repository {

    protected ?string $model = TranslationModel::class;
    protected string $table = 'translations';
    protected string $primaryKey = 'translation_id';

}
