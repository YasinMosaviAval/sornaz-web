<?php
use Core\router\Router;
use Modules\Social\Controllers\SocialWebController as C;

Router::get('/community', [C::class, 'page']);
foreach (['direct','notifications','create','saved'] as $page) Router::get('/community/'.$page, [C::class,'page']);
foreach (['posts','stories','users','direct'] as $page) Router::get('/community/'.$page.'/{id}', [C::class,'page']);
Router::group(['prefix'=>'/community/api'], function () {
    Router::get('/me',[C::class,'me']);
    Router::get('/people',[C::class,'people']);
    Router::get('/users/{id}',[C::class,'profile']);
    Router::post('/users/{id}/follow',[C::class,'follow']);
    Router::get('/posts',[C::class,'posts']); Router::post('/posts',[C::class,'publish']);
    Router::get('/posts/{id}',[C::class,'post']);
    Router::post('/posts/{id}/delete',[C::class,'remove']);
    Router::post('/posts/{id}/react',[C::class,'react']);
    Router::get('/posts/{id}/comments',[C::class,'comments']);
    Router::post('/posts/{id}/comments',[C::class,'comment']);
    Router::post('/posts/{id}/comments/{commentId}/delete',[C::class,'deleteComment']);
    Router::post('/posts/{id}/comments/{commentId}/like',[C::class,'likeComment']);
    Router::post('/posts/{id}/share',[C::class,'share']);
    Router::post('/stories/{id}/reply',[C::class,'replyStory']);
    Router::post('/media',[C::class,'upload']); Router::get('/media/{id}',[C::class,'media']);
    Router::get('/notifications',[C::class,'notifications']); Router::post('/notifications/{id}/read',[C::class,'read']);
    Router::get('/conversations',[C::class,'conversations']); Router::post('/conversations',[C::class,'conversation']);
    Router::get('/conversations/{id}/messages',[C::class,'messages']); Router::post('/conversations/{id}/messages',[C::class,'send']);
});
