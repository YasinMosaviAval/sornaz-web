<?php

namespace Modules\Media\Repositories;

use Core\Database\Repository;

class MediaRepository extends Repository {
    protected string $table = 'media_files';
    protected string $primaryKey = 'media_id';
    protected ?string $model = null;

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

    public function deleteCollection(int $userId,string $collection): bool
    {
        return $this->query()
            ->where('user_id',$userId)
            ->where('collection',$collection)
            ->delete();
    }
}