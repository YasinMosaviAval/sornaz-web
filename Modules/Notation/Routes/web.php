<?php
use Core\router\Router;
use Modules\Notation\Controllers\NotationController as C;
Router::get('/music-sheets',[C::class,'page']);
foreach(['/music-sheets/api','/api/sornaz/v1/music-sheets'] as $prefix){
 Router::group(['prefix'=>$prefix],function(){
  Router::get('',[C::class,'index']);Router::post('',[C::class,'create']);
  Router::get('/{id}',[C::class,'show']);Router::post('/{id}',[C::class,'update']);
  Router::post('/{id}/delete',[C::class,'remove']);Router::post('/{id}/bookmark',[C::class,'bookmark']);
 });
}