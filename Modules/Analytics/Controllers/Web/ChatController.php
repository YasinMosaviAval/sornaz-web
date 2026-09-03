<?php
namespace Modules\Analytics\Controllers\Web;
use Core\http\DownloadResponse;
use Core\http\ResponseFactory;
use Modules\Analytics\Services\ChatService;
final class ChatController
{
 public function __construct(private ChatService $service){}
 public function index(){return$this->run(fn()=>['success'=>true,'data'=>$this->service->index((int)auth()->id())]);}
 public function create(){return$this->run(fn()=>['success'=>true,'data'=>$this->service->create((int)auth()->id(),$this->payload())]);}
 public function messages(int$id){return$this->run(fn()=>['success'=>true,'data'=>$this->service->messages((int)auth()->id(),$id,(int)($_GET['after']??0))]);}
 public function send(int$id){return$this->run(fn()=>['success'=>true,'data'=>$this->service->send((int)auth()->id(),$id,(string)($_POST['body']??''),$_FILES['file']??[])]);}
 public function details(int$id){return$this->run(fn()=>['success'=>true,'data'=>$this->service->details((int)auth()->id(),$id)]);}
 public function rename(int$id){return$this->run(function()use($id){$d=$this->payload();$this->service->rename((int)auth()->id(),$id,(string)($d['title']??''));return['success'=>true];});}
 public function avatar(int$id){return$this->run(fn()=>['success'=>true,'data'=>$this->service->updateGroupAvatar((int)auth()->id(),$id,$_FILES['file']??[])]);}
 public function addMembers(int$id){return$this->run(function()use($id){$d=$this->payload();$this->service->addMembers((int)auth()->id(),$id,(array)($d['userIds']??[]));return['success'=>true];});}
 public function removeMember(int$id,int$userId){return$this->run(function()use($id,$userId){$this->service->removeMember((int)auth()->id(),$id,$userId);return['success'=>true];});}
 public function leave(int$id){return$this->run(function()use($id){$this->service->leaveGroup((int)auth()->id(),$id);return['success'=>true];});}
 public function delete(int$id){return$this->run(function()use($id){$this->service->deleteConversation((int)auth()->id(),$id);return['success'=>true];});}
 public function like(int$id){return$this->run(fn()=>['success'=>true,'data'=>$this->service->toggleLike((int)auth()->id(),$id)]);}
 public function editMessage(int$id){return$this->run(function()use($id){$d=$this->payload();$this->service->editMessage((int)auth()->id(),$id,(string)($d['body']??''));return['success'=>true];});}
 public function deleteMessage(int$id){return$this->run(function()use($id){$this->service->deleteMessage((int)auth()->id(),$id);return['success'=>true];});}
 public function forward(int$id){return$this->run(function()use($id){$d=$this->payload();$this->service->forwardMessage((int)auth()->id(),$id,(array)($d['conversationIds']??[]));return['success'=>true];});}
 public function file(int$id){try{$f=$this->service->file((int)auth()->id(),$id);$voice=preg_match('/^voice-\d+\.(webm|m4a|ogg)$/i',(string)$f['name']);$mime=$voice?match(strtolower(pathinfo((string)$f['name'],PATHINFO_EXTENSION))){'m4a'=>'audio/mp4','ogg'=>'audio/ogg',default=>'audio/webm'}:$f['mime'];return new DownloadResponse($f['path'],$f['name'],$mime,$voice||str_starts_with((string)$mime,'audio/'));}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],404);}}
 private function payload():array{$raw=base64_decode(strtr((string)request()->input('payload_b64',''),'-_','+/'),true);$d=$raw===false?null:json_decode($raw,true);if(!is_array($d))throw new \RuntimeException('اطلاعات معتبر نیست.');return$d;}
 private function run(callable$c){try{return ResponseFactory::json($c());}catch(\Throwable$e){return ResponseFactory::json(['success'=>false,'message'=>$e->getMessage()],422);}}
}
