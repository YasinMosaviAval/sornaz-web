<?php
namespace Modules\Academy\Controllers\Web;
use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyClassroomService;
use Modules\System\Services\SiteAdminAccess;
use RuntimeException;
use Throwable;

class AcademyClassroomController {


    public function __construct(
        protected AcademyClassroomService $s
    ){}


    private function admin() : bool {
        return SiteAdminAccess::allows(auth()->user());
    }


    private function data() : array {
        $raw = base64_decode((string)($_POST['payload_b64']??''),true);
        $d = json_decode($raw?:'',true);
        if(!is_array($d)) throw new RuntimeException('داده فرم نامعتبر است.');
        return $d;
    }


    private function run(callable $f, int $status=200) {
        try{
            return ResponseFactory::json(['success'=>true,'data'=>$f()],$status);
        }
        catch(Throwable $e){
            return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);
        }
    }


    public function index() {
        return $this->run(fn()=>$this->s->bootstrap((int)auth()->id(),$this->admin()));
    }


    public function store() {
        return $this->run(fn()=>$this->s->saveRoom((int)auth()->id(),$this->data(),$this->admin()),201);
    }


    public function update(int $id) {
        return $this->run(fn()=>$this->s->saveRoom((int)auth()->id(),$this->data(),$this->admin(),$id));
    }


    public function delete(int $id) {
        return $this->run(
            function() use($id) {
                $this->s->deleteRoom((int)auth()->id(),$id,$this->admin());
                return null;
            }
        );
    }


    public function storeType() {
        return $this->run(fn()=>$this->s->saveType((int)auth()->id(),$this->data(),$this->admin()),201);
    }


    public function updateType(int $id) {
        return $this->run(fn()=>$this->s->saveType((int)auth()->id(),$this->data(),$this->admin(),$id));
    }


    public function deleteType(int $id) {
        return $this->run(
            function() use($id) {
                $this->s->deleteType((int)auth()->id(),$id,$this->admin());
                return null;
            }
        );
    }

    public function cycleTypeStatus(int $id) {
        return $this->run(fn()=>$this->s->cycleStatus((int)auth()->id(),$id,$this->admin()));
    }

    public function storeTypeCategory() {
        return $this->run(fn()=>$this->s->createCategory((int)auth()->id(),$this->data(),$this->admin()),201);
    }

    public function updateTypeCategory(string $value) {
        return $this->run(fn()=>$this->s->updateCategory((int)auth()->id(),$value,$this->data(),$this->admin()));
    }

    public function deleteTypeCategory(string $value) {
        return $this->run(function()use($value){$this->s->deleteCategory((int)auth()->id(),$value,$this->admin());return null;});
    }

    public function realtimeVersion() {
        return $this->run(fn()=>$this->s->realtimeVersion((int)auth()->id(),$this->admin()));
    }


}
