<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\NationalHolidayService;
use RuntimeException;
use Throwable;

final class NationalHolidayController
{
    public function __construct(private NationalHolidayService $service){}
    public function index(){return $this->run(fn()=>$this->service->index((int)auth()->id()));}
    public function store(){return $this->run(fn()=>$this->service->saveHoliday((int)auth()->id(),$this->payload()),201);}
    public function update(int $id){return $this->run(fn()=>$this->service->saveHoliday((int)auth()->id(),$this->payload(),$id));}
    public function delete(int $id){return $this->run(function()use($id){$this->service->deleteHoliday((int)auth()->id(),$id);return null;});}
    public function toggleStatus(int $id){return $this->run(fn()=>$this->service->toggleHolidayStatus((int)auth()->id(),$id));}
    public function saveAcademySetting(int $academyId){$data=$this->payload();return $this->run(fn()=>$this->service->saveAcademySetting((int)auth()->id(),$academyId,!empty($data['allowClasses'])));}
    private function payload():array{$raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$data=$raw===false?null:json_decode($raw,true);if(!is_array($data))throw new RuntimeException('اطلاعات ارسالی معتبر نیست.');return$data;}
    private function run(callable $callback,int$status=200){try{return ResponseFactory::json(['success'=>true,'data'=>$callback()],$status);}catch(Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
