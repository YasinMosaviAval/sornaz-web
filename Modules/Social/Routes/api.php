<?php
use Core\router\Router;
use Modules\Social\Controllers\SocialController as C;
// Every action validates the existing mobile Bearer token. Cookie authentication is never accepted.
Router::group(['prefix'=>'/api/sornaz/v1/social'],function(){
    Router::get('/home',[C::class,'home']);
    Router::get('/dashboard',[C::class,'dashboard']);
    Router::get('/bookmarks',[C::class,'bookmarks']);Router::post('/bookmarks',[C::class,'bookmark']);
    Router::get('/settings',[C::class,'settings']);Router::post('/settings',[C::class,'updateSettings']);
    Router::post('/courses/{id}/lessons/{postId}/progress',[C::class,'progress']);
    Router::get('/me',[C::class,'me']);Router::post('/me',[C::class,'updateProfile']);
    Router::get('/people',[C::class,'people']);Router::get('/users/{id}',[C::class,'profile']);
    Router::get('/users/{id}/followers',[C::class,'followers']);Router::get('/users/{id}/following',[C::class,'following']);Router::post('/users/{id}/follow',[C::class,'follow']);
    Router::get('/posts',[C::class,'posts']);Router::post('/posts',[C::class,'publish']);Router::get('/posts/{id}',[C::class,'post']);Router::post('/posts/{id}/delete',[C::class,'remove']);Router::post('/posts/{id}/react',[C::class,'react']);
    Router::post('/media',[C::class,'upload']);Router::get('/media/{id}',[C::class,'media']);
    Router::get('/notifications',[C::class,'notifications']);Router::post('/notifications/{id}/read',[C::class,'read']);
    Router::get('/conversations',[C::class,'conversations']);Router::post('/conversations',[C::class,'conversation']);Router::get('/conversations/{id}/messages',[C::class,'messages']);Router::post('/conversations/{id}/messages',[C::class,'send']);
    Router::post('/courses/{id}/lessons/{postId}/unlock',[C::class,'unlockLesson']);
    Router::get('/courses',[C::class,'courses']);Router::post('/courses',[C::class,'createCourse']);Router::get('/courses/{id}',[C::class,'course']);Router::get('/courses/{id}/edit',[C::class,'editCourse']);Router::post('/courses/{id}',[C::class,'updateCourse']);Router::post('/courses/{id}/media',[C::class,'courseUpload']);Router::post('/courses/{id}/buy',[C::class,'buy']);Router::get('/course-media/{id}',[C::class,'courseMedia']);
});
