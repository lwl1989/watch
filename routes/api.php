<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['format']], function () {
    Route::get('forgeUser','Users\UsersController@forgeUser');
    Route::post('webhook','Controller@webHook');
    Route::get('wx/login','Auth\WxController@login');
    Route::get('wx/token','Auth\WxController@getToken');
    //Route::get('userTag','Common\TagsController@getList');
});

Route::group(['middleware' => ['format','auth:c_api']], function () {
    Route::get('user/tags','Common\TagsController@getList');
    Route::get('user/menu','Common\TagsController@getMenu');
    //Route::group(['prefix' => 'static'],function (){
    //    Route::post('user/avatar', 'Resource\ImageUploadController@upload');
    //    Route::post('content/cover', 'Resource\ImageUploadController@upload');
    //    Route::post('content/images', 'Resource\ImageUploadController@upload');
    //    Route::post('content/videos', 'Resource\ImageUploadController@upload');
        //Route::post('user/avatar', 'Resource\ImageUploadController@upload');
        //Route::post('user/avatar', 'Resource\ImageUploadController@upload');
    //});


    Route::group(['prefix' => 'content'],function (){

    });



    Route::group(['prefix' => 'user'],function (){
      
    });

});
