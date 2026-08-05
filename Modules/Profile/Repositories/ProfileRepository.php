<?php

namespace Modules\Profile\Repositories;

use Core\database\Repository;
use Modules\Profile\Models\ProfileModel;

class ProfileRepository extends Repository {

    protected ?string $model = ProfileModel::class;
    protected string $table = 'profiles';
    protected string $primaryKey = 'profile_id';

}
