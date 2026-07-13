<?php

namespace Modules\Contact\Repositories;

use Core\Database\Repository;

class ContactRepository extends Repository
{

    protected string $table='user_contacts';

    protected string $primaryKey='contact_id';

    protected ?string $model=null;



    public function findByUserId(int $userId): mixed
    {
        return $this->query()
            ->where('user_id',$userId)
            ->first();
    }



    public function updateOrCreate(
        int $userId,
        array $data
    ): bool
    {

        $contact=$this->findByUserId($userId);

        if($contact){

            return $this->query()
                ->where('user_id',$userId)
                ->update($data);

        }

        $data['user_id']=$userId;

        return $this->create($data);

    }

}