<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test', function () {
    //echo App\Kdniao\Tracking::class; //App\Kdniao\Tracking
    $tracking = app(App\Kdniao\Tracking::class);// app 调用依赖注入

    $result = $tracking->track('DHL', ' 1397035161 ');//1397035161

    echo '<pre>';
    print_r($result);
    exit;


});

Route::get('/', function () {
    return view('welcome');
});
