<?php

namespace Modules\Analytics\Controllers\Web;

use Core\http\DownloadResponse;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\AdminAccountService;
use Modules\Analytics\Services\AcademyScopedBackupService;

final class AdminAccountController
{
    public function __construct(private AdminAccountService $service,private AcademyScopedBackupService $backups){}
    public function show(){return$this->run(fn()=>['success'=>true,'data'=>$this->service->data((int)auth()->id())]);}
    public function profile(){return$this->mutate(fn($d)=>$this->service->saveProfile((int)auth()->id(),$d));}
    public function bio(){return$this->mutate(fn($d)=>$this->service->saveBio((int)auth()->id(),$d));}
    public function privacy(){return$this->mutate(fn($d)=>$this->service->savePrivacy((int)auth()->id(),$d));}
    public function security(){return$this->mutate(fn($d)=>$this->service->saveSecurity((int)auth()->id(),$d));}
    public function upload(string$kind){return$this->run(function()use($kind){if(!in_array($kind,['avatar','cover','document'],true))throw new \RuntimeException('نوع رسانه معتبر نیست.');$actor=(int)auth()->id();$media=$this->service->uploadMedia($actor,$_FILES['file']??[],$kind,$_POST);if($kind!=='document')$this->service->saveMediaMetadata($actor,(int)$media['id'],$_POST);return['success'=>true,'data'=>$media];});}
    public function deleteMedia(int$id){return$this->mutate(fn()=> $this->service->deleteMedia((int)auth()->id(),$id),false);}
    public function downloadMedia(int$id){try{$f=$this->service->downloadableMedia((int)auth()->id(),$id);return new DownloadResponse($f['path'],$f['filename'],$f['mime']);}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404);}}
    public function endSession(int$id){return$this->mutate(fn()=> $this->service->endTrackingSession((int)auth()->id(),$id),false);}
    public function backup(){return$this->run(fn()=>['success'=>true,'data'=>$this->backups->create((int)auth()->id())]);}
    public function download(int$id){try{$f=$this->backups->find((int)auth()->id(),$id);return new DownloadResponse($f['path'],$f['filename'],'application/sql');}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404);}}
    private function mutate(callable$cb,bool$payload=true){return$this->run(function()use($cb,$payload){$payload?$cb($this->payload()):$cb();return['success'=>true];});}
    private function payload():array{$raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$d=$raw===false?null:json_decode($raw,true);if(!is_array($d))throw new \RuntimeException('اطلاعات معتبر نیست.');return$d;}
    private function run(callable$cb){try{return ResponseFactory::json($cb());}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
