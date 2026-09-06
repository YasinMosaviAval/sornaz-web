<?php
namespace Modules\Analytics\Controllers\Api;

use Core\http\ResponseFactory;
use Core\database\DB;
use Modules\Analytics\Services\ArticleApiService;
use Modules\Analytics\Services\PublicRatingService;
use Modules\System\Services\MobileAuthTokenService;
use Modules\System\Repositories\UserRepository;

class ArticleController
{
    public function __construct(private ArticleApiService $service) {}
    private function language(): string
    {
        $value = (string)($_GET['locale'] ?? $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? locale());
        return str_starts_with(strtolower($value), 'en') ? 'en' : 'fa';
    }
    private function viewer(bool $required = false): ?int
    {
        $user = (new MobileAuthTokenService(new UserRepository()))->userFromRequest();
        if (!$user && ($required || trim((string)($_SERVER['HTTP_AUTHORIZATION'] ?? '')) !== '')) throw new \RuntimeException('Please sign in again.', 401);
        return $user ? (int)$user['user_id'] : null;
    }
    private function payload(): array
    {
        $body=json_decode((string)file_get_contents('php://input'),true);return is_array($body)?$body:$_POST;
    }
    public function index(){return $this->run(fn()=>$this->service->articles($_GET,$this->language()));}
    public function show(int $id){return $this->run(fn()=>$this->service->show($id,$this->language()));}
    public function manifest(){return $this->run(fn()=>$this->service->manifest($this->language()));}
    public function categories(){return $this->run(fn()=>$this->service->categories($this->language()));}
    public function related(int $id){return $this->run(fn()=>$this->service->related($id,$_GET,$this->language()));}
    public function comments(int $id){return $this->run(fn()=>$this->service->comments($id,$_GET,$this->language(),$this->viewer()));}
    public function storeComment(int $id)
    {
        return $this->run(function() use ($id) {
            $actor=$this->viewer();
            // Prepare guest ownership storage before inserting a comment.
            if (!$actor) $this->service->prepareReceipts();
            $commentId=$this->service->storeComment($id,$this->payload(),$this->language(),$actor);
            return ['success'=>true,'id'=>$commentId,'status'=>'pending','receipt'=>$actor?null:$this->service->receipt($id,$commentId)];
        },201);
    }
    private function ratingTarget(string $type,int $id): void
    {
        if (!in_array($type,['post','comment'],true)) throw new \RuntimeException('Invalid rating target.',404);
        if ($type==='post') {$this->service->show($id,$this->language());return;}
        $row=DB::table('comments')->where('comment_id',$id)->whereNotNull('approved_at')->whereNull('deleted_at')->first();
        if (!$row) throw new \RuntimeException('Comment not found.',404);
        $this->service->show((int)$row['post_id'],$this->language());
    }
    public function rating(string $type,int $id){return $this->run(function()use($type,$id){$this->ratingTarget($type,$id);return (new PublicRatingService())->summary($type,$id,$this->viewer());});}
    public function rate(string $type,int $id){return $this->run(function()use($type,$id){$actor=$this->viewer(true);$this->ratingTarget($type,$id);return (new PublicRatingService())->rate($type,$id,(int)($this->payload()['score']??0),$actor);});}
    private function run(callable $callback,int $status=200)
    {
        try {return ResponseFactory::json($callback(),$status);}
        catch (\Throwable $e) {
            $code=in_array($e->getCode(),[401,403,404,422,503],true)?$e->getCode():(str_contains($e->getMessage(),'یافت نشد')?404:422);
            if (!($e instanceof \RuntimeException)) {error_log('Article API: '.$e->getMessage());$code=500;}
            $message=$code===500?($this->language()==='en'?'Article service is unavailable.':'سرویس مقالات در دسترس نیست.'):$e->getMessage();
            return ResponseFactory::json(['success'=>false,'message'=>$message],$code);
        }
    }
}
