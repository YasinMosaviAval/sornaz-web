<?php

namespace Modules\Contact\Repositories;

use Core\Database\Repository;

class ContactRepository extends Repository {

    protected string $table='user_contacts';
    protected string $primaryKey='user_contact_id';
    protected ?string $model=null;



    public function allByUser(int $userId): array {
        return $this->query()->where('user_id',$userId)->get();
    }



    public function deleteByUser(int $userId): bool {
        return $this->query()->where('user_id',$userId)->delete();
    }





}