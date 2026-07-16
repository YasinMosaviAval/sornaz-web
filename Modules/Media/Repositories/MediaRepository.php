<?php

namespace Modules\Media\Repositories;

use Core\Database\Repository;

class MediaRepository extends Repository
{
    protected string $table='media_files';

    protected string $primaryKey='media_file_id';

    protected ?string $model=null;

    public function create(array $data): bool
    {
        return parent::create($data);
    }

    public function logo(int $userId): ?array
    {
        return $this->query()
            ->where('user_id',$userId)
            ->where('collection','logo')
            ->first();
    }

    public function cover(int $userId): ?array
    {
        return $this->query()
            ->where('user_id',$userId)
            ->where('collection','cover')
            ->first();
    }

    public function gallery(int $userId): array
    {
        return $this->query()
            ->where('user_id',$userId)
            ->where('collection','gallery')
            ->orderBy('sort_order')
            ->get();
    }
}