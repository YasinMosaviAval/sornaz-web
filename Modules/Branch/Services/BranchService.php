<?php

namespace Modules\Branch\Services;

use Core\Translation\TranslationService;
use Modules\Branch\Repositories\BranchRepository;
use Modules\System\Repositories\UserRepository;

use Modules\Address\Services\AddressService;
use Modules\Contact\Services\ContactService;
use Modules\Availability\Services\AvailabilityService;
use Modules\Media\Services\MediaService;

use Modules\Address\Repositories\AddressRepository;
use Modules\Contact\Repositories\ContactRepository;
use Modules\Availability\Repositories\AvailabilityRepository;
use Modules\Availability\Repositories\AvailabilityExceptionRepository;
use Modules\Media\Repositories\MediaRepository;


class BranchService {


    protected AddressService $addressService;
    protected ContactService $contactService;
    protected AvailabilityService $availabilityService;
    protected MediaService $mediaService;


    public function __construct(protected BranchRepository $repository, protected UserRepository $users){
        $this->addressService = new AddressService(new AddressRepository());
        $this->contactService = new ContactService(new ContactRepository());
        $this->availabilityService = new AvailabilityService(new AvailabilityRepository(), new AvailabilityExceptionRepository());
        $this->mediaService = new MediaService(new MediaRepository());
    }



    public function create(int $academyId, array $data, array $files=[]): bool {
        $this->users->create([
            'username'=>$data['username'],
            'email'=>$data['email'],
            'phone'=>$data['phone'],
            'status'=>'approved',
            'type'=>'branch',
            'gender'=>'branch',
            'timezone'=>'Asia/Tehran',
            'created_by'=>auth()->id(),
            'updated_by'=>auth()->id(),
        ]);
        $user=$this->users
            ->query()
            ->where('username',$data['username'])
            ->first();
        if(!$user){
            return false;
        }
        if(
            !$this->repository->create([
                'academy_id'=>$academyId,
                'user_id'=>$user['user_id'],
                'academy_branch_type_id'=>$data['academy_branch_type_id'],
                'mode'=>$data['mode'],
                'is_main'=>$data['is_main'] ?? 0,
                'timezone'=>'Asia/Tehran',
            ])
        ){
            return false;
        }
        $branch=$this->repository
            ->query()
            ->where('user_id',$user['user_id'])
            ->first();
        if(!$branch){
            return false;
        }
        $this->saveAddress($user['user_id'], $data);
        $this->saveContact($user['user_id'], $data);
        $this->saveAvailability($user['user_id'], $data);
        $this->saveMedia($user['user_id'], $files);
        $this->saveTranslations($branch['branch_id'], $data);
        return true;
    }


    public function list(int $academyId): array {
        return $this->repository->academyBranches($academyId);
    }



    public function editData(int $branchId): array {
        $branch = $this->repository->findById($branchId);
        if(!$branch){
            return [];
        }
        $userId = $branch['user_id'];
        $tr = TranslationService::manager();
        return [
            'branch'=>$branch,
            'address'=>$this->addressService->findByUserId($userId),
            'contact'=>$this->contactService->findByUserId($userId),
            'availability'=>$this->availabilityService->weekly($userId),
            'availabilityExceptions'=>$this->availabilityService->exceptions($userId),
            'logo'=>$this->mediaService->logo($userId),
            'cover'=>$this->mediaService->cover($userId),
            'gallery'=>$this->mediaService->gallery($userId),
            'academyVideos'=>$this->mediaService->uploadAcademyVideos($userId),
            'documents'=>$this->mediaService->documents($userId),
            'name_fa'=>$tr->get('academy_branches',$branchId,'name','fa'),
            'name_en'=>$tr->get('academy_branches',$branchId,'name','en'),
            'short_description_fa'=>$tr->get('academy_branches',$branchId,'short_description','fa'),
            'short_description_en'=>$tr->get('academy_branches',$branchId,'short_description','en'),
            'description_fa'=>$tr->get('academy_branches',$branchId,'description','fa'),
            'description_en'=>$tr->get('academy_branches',$branchId,'description','en'),
            'slogan_fa'=>$tr->get('academy_branches',$branchId,'slogan','fa'),
            'slogan_en'=>$tr->get('academy_branches',$branchId,'slogan','en'),
        ];
    }



    public function update(int $branchId, array $data, array $files=[]): bool {
        $branch=$this->repository->findById($branchId);
        if(!$branch){
            return false;
        }
        $userId=$branch['user_id'];
        $this->users->update(
            $userId,
            [
                'username'=>$data['username'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'status'=>$data['status'],
                'timezone'=>$data['timezone']
            ]
        );
        $this->repository->update(
            $branchId,
            [
                'academy_branch_type_id'=>$data['academy_branch_type_id'],
                'mode'=>$data['mode'],
                'is_main'=>$data['is_main'] ?? 0,
                'timezone'=>$data['timezone']
            ]
        );
        $this->saveAddress($userId, $data);
        $this->saveContact($userId, $data);
        $this->saveAvailability($userId, $data);
        $this->saveMedia($userId, $files);
        $this->saveTranslations($branchId, $data);
        return true;
    }


    public function delete(int $branchId): bool {
        $branch = $this->repository->findById($branchId);
        if(!$branch){
            return false;
        }
        TranslationService::manager()->deleteAll('academy_branches', $branchId);
        $this->users->delete($branch['user_id']);
        return $this->repository->delete($branchId);
    }



    // protected function saveTranslations(int $branchId, array $data): void {
    //     $tr = TranslationService::manager();
    //     $fields = [
    //         'name',
    //         'short_description',
    //         'description',
    //         'slogan',
    //     ];
    //     foreach($fields as $field){
    //         $tr->set(
    //             'academy_branches',
    //             $branchId,
    //             $field,
    //             $data[$field.'_fa'] ?? '',
    //             'fa'
    //         );
    //         $tr->set(
    //             'academy_branches',
    //             $branchId,
    //             $field,
    //             $data[$field.'_en'] ?? '',
    //             'en'
    //         );
    //     }
    // }




    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function saveAddress(int $userId,array $data): void {
        $this->addressService->save(
            $userId,
            [
                'country_id'=>$data['country_id'] ?? null,
                'province_id'=>$data['province_id'] ?? null,
                'county_id'=>$data['county_id'] ?? null,
                'postal_code'=>$data['postal_code'] ?? null,
                'latitude'=>$data['latitude'] ?? null,
                'longitude'=>$data['longitude'] ?? null,
                'address'=>$data['address'] ?? '',
            ]
        );
    }



    protected function saveContact(int $userId,array $data): void {
        $this->contactService->save(
            $userId,
            [
                'telephone'=>$data['telephone'] ?? null,
                'mobile'=>$data['mobile'] ?? null,
                'whatsapp'=>$data['whatsapp'] ?? null,
                'telegram'=>$data['telegram'] ?? null,
                'instagram'=>$data['instagram'] ?? null,
                'website'=>$data['website'] ?? null,
            ]
        );
    }



    protected function saveAvailability(int $userId,array $data): void {
        $this->availabilityService->saveWeekly($userId, $data['availability'] ?? []);
        $this->availabilityService->saveExceptions($userId, $data['exceptions'] ?? []);
    }



    protected function saveMedia(int $userId,array $files): void {
        if(isset($files['logo']) && $files['logo']['error']==UPLOAD_ERR_OK){
            $this->mediaService->uploadLogo($userId, $files['logo']);
        }
        if(isset($files['cover']) && $files['cover']['error']==UPLOAD_ERR_OK){
            $this->mediaService->uploadCover($userId, $files['cover']);
        }
        if(isset($files['gallery']) && !empty($files['gallery']['name'][0])){
            $this->mediaService->uploadGallery($userId, $files['gallery']);
        }
        if(isset($files['intro_video']) && $files['intro_video']['error']==UPLOAD_ERR_OK){
            $this->mediaService->uploadIntroVideo($userId, $files['intro_video']);
        }
        if(isset($files['academy_video']) && !empty($files['academy_video']['name'][0])){
            $this->mediaService->uploadAcademyVideos($userId, $files['academy_video']);
        }
        if(isset($files['documents']) && !empty($files['documents']['name'][0])){
            $this->mediaService->uploadDocuments($userId, $files['documents']);
        }
    }



    protected function saveTranslations(int $branchId,array $data): void {
        $tr=TranslationService::manager();
        $fields=[
            'name',
            'slogan',
            'short_description',
            'description',
            'rules',
            'registration',
            'meta_title',
            'meta_description',
            'keywords',
        ];
        foreach($fields as $field){
            if(isset($data[$field.'_fa'])){
                $tr->set(
                    'academy_branches',
                    $branchId,
                    $field,
                    $data[$field.'_fa'],
                    'fa'
                );
            }
            if(isset($data[$field.'_en'])){
                $tr->set(
                    'academy_branches',
                    $branchId,
                    $field,
                    $data[$field.'_en'],
                    'en'
                );
            }
        }
    }



}