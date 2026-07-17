<?php

namespace Modules\Availability\Services;

use Core\Translation\TranslationService;
use Modules\Availability\Repositories\AvailabilityRepository;
use Modules\Availability\Repositories\AvailabilityExceptionRepository;

class AvailabilityService {



    public function __construct(
        protected AvailabilityRepository $repository,
        protected AvailabilityExceptionRepository $exceptionRepository
    ){
    }


    public function all(int $userId):array {
        return $this->repository->allForUser($userId);
    }



    public function save(int $userId,array $items):bool {
        $this->repository->deleteForUser($userId);
        foreach($items as $item){
            $item['user_id']=$userId;
            $this->repository->create($item);
        }
        return true;
    }





    public function weekly(int $userId): array {
        return $this->repository->findByUser($userId);
    }

    
    public function saveWeekly(int $userId, array $availability): bool {
        /*
        |--------------------------------------------------------------------------
        | حذف برنامه قبلی
        |--------------------------------------------------------------------------
        */
        $this->repository->deleteForUser($userId);
        /*
        |--------------------------------------------------------------------------
        | ثبت برنامه جدید
        |--------------------------------------------------------------------------
        */
        foreach ($availability as $day => $rows) {
            if (!is_array($rows)) {
                continue;
            }
            foreach ($rows as $row) {
                $start = trim($row['start_time'] ?? '');
                $end   = trim($row['end_time'] ?? '');
                /*
                |--------------------------------------------------------------------------
                | اگر سطر کاملاً خالی است
                |--------------------------------------------------------------------------
                */
                if ($start === '' && $end === '' && empty($row['is_closed'])) {
                    continue;
                }
                $this->repository->create([
                    'user_id'       => $userId,
                    'day_of_week'   => $day,
                    'start_time'    => $start ?: null,
                    'end_time'      => $end ?: null,
                    'timezone'      => config('app.timezone'),
                    'type'          => 'available',
                    'is_repeating'  => 1,
                    'repeat_period' => 'week',
                    'is_closed'     => !empty($row['is_closed']) ? 1 : 0,
                    'created_by'    => auth()->id(),
                    'updated_by'    => auth()->id()
                ]);
            }
        }
        return true;
    }
        
    public function saveWeekly7(int $userId, array $availability): bool {
        foreach ($availability as $day => $rows) {
            if (!is_array($rows)) {
                continue;
            }
            foreach ($rows as $row) {
                $start = trim($row['start_time'] ?? '');
                $end   = trim($row['end_time'] ?? '');
                if ($start==='' && $end==='' && empty($row['is_closed'])) {
                    continue;
                }
                $data = [
                    'user_id'       => $userId,
                    'day_of_week'   => $day,
                    'start_time'    => $start ?: null,
                    'end_time'      => $end ?: null,
                    'timezone'      => config('app.timezone'),
                    'type'          => 'available',
                    'is_repeating'  => 1,
                    'repeat_period' => 'week',
                    'is_closed'     => !empty($row['is_closed']) ? 1 : 0,
                    'created_by'    => auth()->id(),
                    'updated_by'    => auth()->id(),
                ];
                // dump(session()->all());
                dump(auth()->id());
                dump($data);
                $this->repository->create($data);
            }
        }
        die();
    }




    
    public function addException(int $userId,array $data): bool {
        return $this->exceptionRepository->createForUser($userId, $data);
    }


    public function saveException(int $userId,array $data): bool
    {
        $note=$data['note'] ?? '';

        unset($data['note']);

        $data['created_by']=auth()->id();
        $data['updated_by']=auth()->id();
        $id=$this->exceptionRepository->createException($userId,$data);

        if(!$id){
            return false;
        }

        TranslationService::manager()->set(
            'user_availability_exceptions',
            $id,
            'note',
            $note
        );

        return true;
    }


    public function deleteException(int $id): bool
    {
        TranslationService::manager()->delete(
            'user_availability_exceptions',
            $id,
            'note'
        );

        return $this->exceptionRepository->deleteException($id);
    }




    public function exceptions(int $userId): array {
        $items=$this->exceptionRepository->exceptions($userId);
        foreach($items as &$item){
            $item['note']=TranslationService::manager()->get(
                'user_availability_exceptions',
                $item['user_availability_exception_id'],
                'note'
            );
        }
        return $items;
    }

}