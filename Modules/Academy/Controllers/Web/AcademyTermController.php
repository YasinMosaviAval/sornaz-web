<?php
namespace Modules\Academy\Controllers\Web;

use Core\http\ResponseFactory;
use Modules\Academy\Services\AcademyTermService;
use RuntimeException;
use Throwable;

class AcademyTermController {


    public function __construct(private AcademyTermService $service) {}


    public function index() {
        return $this->run(fn()=>['success'=>true,'data'=>$this->service->bootstrap((int)auth()->id())]);
    }


    public function invoices() {
        return $this->run(fn()=>['success'=>true,'data'=>$this->service->invoiceBootstrap((int)auth()->id())]);
    }


    public function updateInvoice(int $id) {
        return $this->run(
            function() use($id) {
                $this->service->updateInvoice((int)auth()->id(),$id,$this->payload());
                return['success'=>true];
            }
        );
    }


    public function payInstallment(int $id, int $installmentId) {
        return $this->run(
            function() use($id, $installmentId) {
                $this->service->payInstallment((int)auth()->id(),$id,$installmentId);
                return['success'=>true];
            }
        );
    }


    public function store() {
        return $this->save();
    }


    public function update(int $id) {
        return $this->save($id);
    }


    public function storeDiscount() {
        return $this->run(fn()=>['success'=>true,'data'=>$this->service->saveDiscount((int)auth()->id(),$this->payload())]);
    }


    public function destroy(int $id) {
        return $this->run(function()use($id){$this->service->delete((int)auth()->id(),$id);return['success'=>true];});
    }


    private function save(int$id=0) {
        return $this->run(function() use ($id) {
            $data = $this->payload();
            $sessionCount = count($data['sessions'] ?? []);
            $installmentCount = max(1, (int) ($data['installmentCount'] ?? 1));
            $maximum = max(2, $sessionCount);
            if ($installmentCount > $maximum) {
                throw new RuntimeException("تعداد اقساط نمی‌تواند بیشتر از {$maximum} باشد.");
            }
            return ['success'=>true,'data'=>$this->service->save((int)auth()->id(),$data,$id)];
        });
    }


    private function payload() : array {
        $raw = base64_decode(strtr((string) request()->input('payload_b64', ''), '-_', '+/'), true);
        $data = $raw === false ? null : json_decode($raw, true);
        if(!is_array($data)) throw new RuntimeException('اطلاعات ارسالی معتبر نیست.');
        return $data;
    }


    private function run(callable$callback) {
        try{
            return ResponseFactory::json($callback());
        }
        catch(Throwable $e) {
            return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);
        }
    }


}
