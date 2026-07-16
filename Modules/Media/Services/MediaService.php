<?php

namespace Modules\Media\Services;

use Modules\Media\Repositories\MediaRepository;

class MediaService
{

    protected MediaRepository $repository;

    public function __construct(
        MediaRepository $repository
    ){
        $this->repository=$repository;
    }

    /*
    |--------------------------------------------------------------------------
    | Read
    |--------------------------------------------------------------------------
    */

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function logo(int $userId): ?array
    {
        return $this->repository->logo($userId);
    }

    public function cover(int $userId): ?array
    {
        return $this->repository->cover($userId);
    }

    public function gallery(int $userId): array
    {
        return $this->repository->gallery($userId);
    }

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    public function uploadLogo(
        int $userId,
        array $file
    ): ?array
    {
        return $this->upload(
            $userId,
            $file,
            'logo'
        );
    }

    public function uploadCover(
        int $userId,
        array $file
    ): ?array
    {
        return $this->upload(
            $userId,
            $file,
            'cover'
        );
    }

    public function uploadGallery(
        int $userId,
        array $files
    ): array
    {
        $result=[];

        foreach($files['tmp_name'] as $index=>$tmp){

            $file=[

                'name'=>$files['name'][$index],

                'type'=>$files['type'][$index],

                'tmp_name'=>$tmp,

                'error'=>$files['error'][$index],

                'size'=>$files['size'][$index]

            ];

            $result[]=$this->upload(
                $userId,
                $file,
                'gallery'
            );

        }

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Base Upload
    |--------------------------------------------------------------------------
    */

    protected function upload(
        int $userId,
        array $file,
        string $collection
    ): ?array
    {

    }

}