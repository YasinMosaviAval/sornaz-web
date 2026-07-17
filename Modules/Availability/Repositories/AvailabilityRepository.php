<?php

namespace Modules\Availability\Repositories;

use Core\Database\Repository;

class AvailabilityRepository extends Repository {
    protected string $table = 'user_availabilities';
    protected string $primaryKey = 'user_availability_id';
    protected ?string $model = null;



    public function allForUser(int $userId): array {
        return $this->query()
            ->where('user_id',$userId)
            ->whereNull('deleted_at')
            ->orderBy('day_of_week')
            ->get();
    }



    public function deleteForUser(int $userId): bool {
        return $this->query()
            ->where('user_id',$userId)
            ->update([
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_by'=>auth()->id(),
                'deleted_at'=>date('Y-m-d H:i:s'),
                'deleted_by'=>auth()->id(),
            ]);
    }




    public function findByUser(int $userId): array {
        return $this->query()
            ->where('user_id',$userId)
            ->whereNull('deleted_at')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    public function createForUser(int $userId,array $data): bool {
        $data['user_id']=$userId;
        return $this->create($data);
    }

    public function updateItem(int $id,array $data): bool {
        $data['updated_by']=auth()->id();
        return $this->query()
            ->where('user_availability_id',$id)
            ->update($data);
    }

    public function deleteItem(int $id): bool {
        return $this->query()
            ->where('user_availability_id',$id)
            ->update([
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_by'=>auth()->id(),
                'deleted_at'=>date('Y-m-d H:i:s'),
                'deleted_by'=>auth()->id(),
            ]);
    }

    public function deleteByUser(int $userId): bool {
        return $this->query()
            ->where('user_id',$userId)
            ->delete();
    }








}