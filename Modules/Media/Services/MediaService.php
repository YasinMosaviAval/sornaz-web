<?php

namespace Modules\Media\Services;

use Core\Translation\TranslationService;
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

    public function uploadGallery(int $userId, array $files): array {
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



    protected function uploadDirectory(string $collection): string
    {
        $directory = config('media.directory.' . $collection);

        if ($directory === null) {
            throw new \RuntimeException(
                "Upload directory for collection [$collection] is not defined."
            );
        }

        return $directory;
    }


    protected function generateFilename(
        array $file
    ): string
    {
        return uniqid().
            '_' .
            time().
            '.'.
            strtolower(
                pathinfo(
                    $file['name'],
                    PATHINFO_EXTENSION
                )
            );
    }

    protected function moveUploadedFile(
        array $file,
        string $destination
    ): bool
    {
        return move_uploaded_file(
            $file['tmp_name'],
            $destination
        );
    }

    protected function ensureDirectory(
        string $directory
    ): void
    {
        if(!is_dir($directory)){
            mkdir(
                $directory,
                0777,
                true
            );
        }
    }


    protected function upload(
        int $userId,
        array $file,
        string $collection
    ): ?array {
        if(empty($file['tmp_name'])){
            return null;
        }
        $directory=$this->uploadDirectory($collection);
        $absolute = public_path($directory);
        $this->ensureDirectory($absolute);
        $filename = $this->generateFilename($file);
        $destination = $absolute.'/'.$filename;
        if(!$this->moveUploadedFile($file, $destination)){
            return null;
        }
        $path = $directory.'/'.$filename;
        $type = str_starts_with($file['type'],'video/') ? 'video' : 'image';
        $this->repository->create([
            'user_id'=>$userId,
            'collection'=>$collection,
            'disk'=>'public',
            'directory'=>$directory,
            'filename'=>$filename,
            'original_filename'=>$file['name'],
            'extension'=>pathinfo($file['name'], PATHINFO_EXTENSION),
            'mime_type'=>$file['type'],
            'size'=>$file['size'],
            'path'=>$path,
            'type'=>$type
        ]);
        return $this->repository->logo($userId);
    }






    public function uploadIntroVideo(
        int $userId,
        array $file
    ): ?array
    {
        return $this->upload(
            $userId,
            $file,
            'intro_video'
        );
    }



    public function uploadAcademyVideos(
        int $userId,
        array $files
    ): array
    {
        $result = [];

        foreach($files['tmp_name'] as $index=>$tmp){

            if(empty($tmp)){
                continue;
            }

            $file = [

                'name'=>$files['name'][$index],

                'type'=>$files['type'][$index],

                'tmp_name'=>$tmp,

                'error'=>$files['error'][$index],

                'size'=>$files['size'][$index],

            ];

            $result[] = $this->upload(
                $userId,
                $file,
                'academy_video'
            );

        }

        return $result;
    }



    public function academyVideos(
        int $userId
    ): array
    {
        $items = $this->repository->academyVideos($userId);

        foreach ($items as &$item) {

            $item['note'] = TranslationService::manager()->get(
                'media_files',
                $item['media_file_id'],
                'note'
            );

        }

        return $items;
    }



    public function documents(
        int $userId
    ): array
    {
        return $this->repository->documents($userId);
    }


    public function uploadDocuments(
        int $userId,
        array $files
    ): array
    {
        $result=[];

        foreach($files['tmp_name'] as $index=>$tmp){

            if(empty($tmp)){
                continue;
            }

            $file=[

                'name'=>$files['name'][$index],

                'type'=>$files['type'][$index],

                'tmp_name'=>$tmp,

                'error'=>$files['error'][$index],

                'size'=>$files['size'][$index],

            ];

            $result[]=$this->upload(
                $userId,
                $file,
                'document'
            );

        }

        return $result;
    }









}