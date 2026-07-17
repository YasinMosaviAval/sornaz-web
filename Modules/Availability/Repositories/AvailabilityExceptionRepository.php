<?php

namespace Modules\Availability\Repositories;

use Core\Database\Repository;

class AvailabilityExceptionRepository extends Repository {
    protected string $table='user_availability_exceptions';
    protected string $primaryKey='user_availability_exception_id';
    protected ?string $model=null;



    public function allForUser(int $userId):array {
        return $this->query()
            ->where('user_id',$userId)
            ->whereNull('deleted_at')
            ->orderBy('date')
            ->get();
    }



    public function findByUser(int $userId): array {
        return $this->query()
            ->where('user_id',$userId)
            ->whereNull('deleted_at')
            ->orderBy('date')
            ->get();
    }

    public function createForUser(int $userId,array $data): bool {
        $data['user_id']=$userId;
        return $this->create($data);
    }
    
    public function updateItem(int $id,array $data): bool {
        $data['updated_by']=auth()->id();
        return $this->query()
            ->where('user_availability_exception_id',$id)
            ->update($data);
    }

    public function deleteItem(int $id): bool {
        return $this->query()
            ->where('user_availability_exception_id',$id)
            ->update([
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_by'=>auth()->id(),
                'deleted_at'=>date('Y-m-d H:i:s'),
                'deleted_by'=>auth()->id()
            ]);
    }



    public function exceptions(int $userId): array {
        return $this->query()
            ->where('user_id',$userId)
            ->orderBy('date')
            ->get();
    }



    public function createException(int $userId, array $data): bool {
        $data['user_id']=$userId;
        return $this->create($data);
    }



    public function deleteException(int $id): bool {
        return $this->query()
            ->where('user_availability_exception_id', $id)
            ->update([
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_by'=>auth()->id(),
                'deleted_at'=>date('Y-m-d H:i:s'),
                'deleted_by'=>auth()->id()
            ]);
    }






}