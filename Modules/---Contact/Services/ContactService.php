<?php

namespace Modules\Contact\Services;

use Modules\Contact\Repositories\ContactRepository;

class ContactService {

    protected array $map = [
        'telephone' => ['mode'=>'phone', 'platform'=>'other'],
        'mobile'    => ['mode'=>'phone', 'platform'=>'other'],
        'whatsapp'  => ['mode'=>'social', 'platform'=>'whats-app'],
        'telegram'  => ['mode'=>'social', 'platform'=>'telegram'],
        'instagram' => ['mode'=>'social', 'platform'=>'instagram'],
        'website'   => ['mode'=>'social', 'platform'=>'website'],
    ];
    protected ContactRepository $repository;

    public function __construct(ContactRepository $repository){
        $this->repository=$repository;
    }



    public function findByUserId(int $userId): array {
        $items=$this->repository->allByUser($userId);
        $result=[];
        foreach($items as $item){
            switch($item['platform']){
                case 'telegram':
                    $result['telegram']=$item['value'];
                    break;
                case 'instagram':
                    $result['instagram']=$item['value'];
                    break;
                case 'website':
                    $result['website']=$item['value'];
                    break;
                case 'whats-app':
                    $result['whatsapp']=$item['value'];
                    break;
                case 'other':
                    if($item['mode']=='phone'){
                        if(empty($result['telephone'])){
                            $result['telephone']=$item['value'];
                        }else{
                            $result['mobile']=$item['value'];
                        }
                    }
                    break;
            }
        }
        return $result;
    }

    

    public function save(int $userId, array $data): bool {
        $this->repository->deleteByUser($userId);
        foreach($this->map as $field=>$config){
            if(empty($data[$field])){
                continue;
            }
            $this->repository->create([
                'user_id'=>$userId,
                'mode'=>$config['mode'],
                'platform'=>$config['platform'],
                'priority'=>'primary',
                'status'=>'active',
                'is_main'=>$field==='telephone' ? 1 : 0,
                'value'=>$data[$field]
            ]);
        }
        return true;
    }





}