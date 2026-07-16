<?php

namespace Modules\Academy\Services;

use Core\Translation\TranslationService;
use Modules\Academy\Repositories\AcademyRepository;
use Modules\System\Repositories\UserRepository;
use Modules\Academy\Requests\AcademyIndexRequest;
use Modules\System\Models\UserModel;
use Modules\Address\Services\AddressService;
use Modules\Contact\Services\ContactService;
use Modules\World\Services\CountyService;
use Modules\World\Services\ProvinceService;
use Modules\Media\Services\MediaService;


class AcademyService {


    protected AcademyRepository $academyRepository;
    protected UserRepository $userRepository;
    protected AddressService $addressService;
    protected ContactService $contactService;
    protected ProvinceService $provinceService;
    protected CountyService $countyService;
    protected MediaService $mediaService;


    public function __construct(protected AcademyRepository $repository) {
        $this->academyRepository = $repository;
        $this->userRepository = app()->container()->make(UserRepository::class);
        $this->addressService = app()->container()->make(AddressService::class);
        $this->contactService = app()->container()->make(ContactService::class);
        $this->provinceService = app()->container()->make(ProvinceService::class);
        $this->countyService = app()->container()->make(CountyService::class);
        $this->mediaService = app()->container()->make(MediaService::class);
    }



    public function list(): array{return $this->repository->getActive();}



    public function all(): array{return $this->repository->getAll();}



    public function active(): array {return $this->repository->getActive();}



    public function find(int $id) {return $this->repository->find($id);}



    public function create(array $data): mixed {
        /*
        |--------------------------------------------------------------------------
        | ایجاد User
        |--------------------------------------------------------------------------
        */
        $this->userRepository->create([
            'username' => $data['username'],
            'email'    => $data['email'] ?? null,
            'phone'    => $data['phone'] ?? null,
            'type'     => 'academy',
            'status'   => $data['status'],
            'locale'   => $data['locale'],
            'timezone' => $data['timezone'],
        ]);
        $user = UserModel::query()->where('username', $data['username'])->first();
        if(!$user){
            return false;
        }
        /*
        |--------------------------------------------------------------------------
        | ایجاد Academy
        |--------------------------------------------------------------------------
        */
        $academy = $this->academyRepository->create(['user_id'=>$user->user_id]);
        $academy = $this->academyRepository->findByUserId($user->user_id);
        $id = $academy['academy_id'];

        $tr = TranslationService::manager();

        $tr->set('academies',$id,'name',$data['name_fa'],'fa');
        $tr->set('academies',$id,'name',$data['name_en'],'en');

        $tr->set('academies',$id,'slogan',$data['slogan_fa'],'fa');
        $tr->set('academies',$id,'slogan',$data['slogan_en'],'en');

        $tr->set('academies',$id,'short_description',$data['short_description_fa'],'fa');
        $tr->set('academies',$id,'short_description',$data['short_description_en'],'en');

        $tr->set('academies',$id,'description',$data['description_fa'],'fa');
        $tr->set('academies',$id,'description',$data['description_en'],'en');

        $tr->set('academies',$id,'rules',$data['rules_fa'],'fa');
        $tr->set('academies',$id,'rules',$data['rules_en'],'en');

        $tr->set('academies',$id,'registration',$data['registration_fa'],'fa');
        $tr->set('academies',$id,'registration',$data['registration_en'],'en');

        $tr->set('academies',$id,'meta_title',$data['meta_title_fa'],'fa');
        $tr->set('academies',$id,'meta_title',$data['meta_title_en'],'en');

        $tr->set('academies',$id,'meta_description',$data['meta_description_fa'],'fa');
        $tr->set('academies',$id,'meta_description',$data['meta_description_en'],'en');

        $tr->set('academies',$id,'keywords',$data['keywords_fa'],'fa');
        $tr->set('academies',$id,'keywords',$data['keywords_en'],'en');

        if($academy){
            $this->saveTranslations(
                $academy['academy_id'],
                $data
            );
        }
        return $academy;
    }


    public function update(int $academyId, array $data): bool {
        $academy = $this->repository->findById($academyId);
        // $academy = $this->academyRepository->findByUserId($user->user_id);
        $id = $academy['academy_id'];

        $tr = TranslationService::manager();

        $tr->set('academies',$id,'name',$data['name_fa'],'fa');
        $tr->set('academies',$id,'name',$data['name_en'],'en');

        $tr->set('academies',$id,'slogan',$data['slogan_fa'],'fa');
        $tr->set('academies',$id,'slogan',$data['slogan_en'],'en');

        $tr->set('academies',$id,'short_description',$data['short_description_fa'],'fa');
        $tr->set('academies',$id,'short_description',$data['short_description_en'],'en');

        $tr->set('academies',$id,'description',$data['description_fa'],'fa');
        $tr->set('academies',$id,'description',$data['description_en'],'en');

        $tr->set('academies',$id,'rules',$data['rules_fa'],'fa');
        $tr->set('academies',$id,'rules',$data['rules_en'],'en');

        $tr->set('academies',$id,'registration',$data['registration_fa'],'fa');
        $tr->set('academies',$id,'registration',$data['registration_en'],'en');

        $tr->set('academies',$id,'meta_title',$data['meta_title_fa'],'fa');
        $tr->set('academies',$id,'meta_title',$data['meta_title_en'],'en');

        $tr->set('academies',$id,'meta_description',$data['meta_description_fa'],'fa');
        $tr->set('academies',$id,'meta_description',$data['meta_description_en'],'en');

        $tr->set('academies',$id,'keywords',$data['keywords_fa'],'fa');
        $tr->set('academies',$id,'keywords',$data['keywords_en'],'en');
        if (!$academy) {
            return false;
        }
        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */
        $userUpdated = $this->userRepository->update(
            $academy['user_id'],
            [
                'username' => $data['username'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'status'   => $data['status'],
                'locale'   => $data['locale'],
                'timezone' => $data['timezone'],
            ]
        );
        /*
        |--------------------------------------------------------------------------
        | Address
        |--------------------------------------------------------------------------
        */
        $this->addressService->save(
            $academy['user_id'],
            [
                'country_id'  => $data['country_id'] ?? 1,
                'province_id' => $data['province_id'] ?? null,
                'county_id'   => $data['county_id'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'latitude'    => $data['latitude'] ?? null,
                'longitude'   => $data['longitude'] ?? null,
                'address'     => $data['address'] ?? '',
            ]
        );
        /*
        |--------------------------------------------------------------------------
        | Contact
        |--------------------------------------------------------------------------
        */
        $this->contactService->save(
            $academy['user_id'],
            [
                'telephone' => $data['telephone'] ?? null,
                'mobile'    => $data['mobile'] ?? null,
                'whatsapp'  => $data['whatsapp'] ?? null,
                'telegram'  => $data['telegram'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'website'   => $data['website'] ?? null,
            ]
        );
        $this->saveTranslations($academyId, $data);


        return $userUpdated;
    }

    public function delete(int $id): bool {return $this->repository->delete($id);}



    public function paginate(AcademyIndexRequest $request): array {
        return $this->repository->paginateList($request);
    }



    public function findById(int $id): mixed {
        return $this->repository->findById($id);
    }



    public function editData(int $academyId): array {
        $academy = $this->findById($academyId);
        $tr = TranslationService::manager();

        $academy['name_fa']=$tr->get('academies',$academyId,'name','fa');
        $academy['name_en']=$tr->get('academies',$academyId,'name','en');

        $academy['slogan_fa']=$tr->get('academies',$academyId,'slogan','fa');
        $academy['slogan_en']=$tr->get('academies',$academyId,'slogan','en');

        $academy['short_description_fa']=$tr->get('academies',$academyId,'short_description','fa');
        $academy['short_description_en']=$tr->get('academies',$academyId,'short_description','en');

        $academy['description_fa']=$tr->get('academies',$academyId,'description','fa');
        $academy['description_en']=$tr->get('academies',$academyId,'description','en');

        $academy['rules_fa']=$tr->get('academies',$academyId,'rules','fa');
        $academy['rules_en']=$tr->get('academies',$academyId,'rules','en');

        $academy['registration_fa']=$tr->get('academies',$academyId,'registration','fa');
        $academy['registration_en']=$tr->get('academies',$academyId,'registration','en');

        $academy['meta_title_fa']=$tr->get('academies',$academyId,'meta_title','fa');
        $academy['meta_title_en']=$tr->get('academies',$academyId,'meta_title','en');

        $academy['meta_description_fa']=$tr->get('academies',$academyId,'meta_description','fa');
        $academy['meta_description_en']=$tr->get('academies',$academyId,'meta_description','en');

        $academy['keywords_fa']=$tr->get('academies',$academyId,'keywords','fa');
        $academy['keywords_en']=$tr->get('academies',$academyId,'keywords','en');


        
        if (!$academy) {
            return [];
        }
        
        $address = $this->addressService->findByUserId($academy['user_id']);
        $translation = \Core\Translation\TranslationService::manager();

        $text=[];
        foreach([
            'title',
            'title_en',
            'short_description',
            'description',
            'slogan',
            'seo_keywords',
            'seo_description'
        ] as $field){
            $text[$field] = $translation->get(
                'academies',
                $academyId,
                $field
            );
        }
        return [
            'academy'=>$academy,
            'address'=>$address,
            'text'=>$text,
            'contact'=> $this->contactService->findByUserId($academy['user_id']),
            'provinces'=> $this->provinceService->options(),
            'counties'=> !empty($address['province_id']) ? $this->countyService->options((int)$address['province_id']) : [],
            'logo'=>$this->mediaService->logo($academy['user_id']),
            'cover'=>$this->mediaService->cover($academy['user_id']),
            'gallery'=>$this->mediaService->gallery($academy['user_id']),
        ];
    }



    protected function saveTranslations(int $academyId,array $data): void {
        $translation = \Core\Translation\TranslationService::manager();
        $fields = [
            'title',
            'title_en',
            'short_description',
            'description',
            'slogan',
            'seo_keywords',
            'seo_description',
        ];
        foreach ($fields as $field){
            if(array_key_exists($field,$data)){
                $translation->set(
                    'academies',
                    $academyId,
                    $field,
                    $data[$field]
                );
            }
        }
    }





}




