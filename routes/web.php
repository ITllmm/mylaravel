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

// EMS: RL731215399CN  LW656033244CN
// DHL: 7264962434   1397035161     1058584015
// FEDEX 787571061915
//SF 959608756720   959608044298 959608045071

Route::get('test', function () {

   $tracking = app(App\Kdniao\Tracking::class);// app 调用依赖注入

   foreach (config("shippercode") as $code) {
    try {
        $result = $tracking->track($code, 'RL731215399CN');

    } catch (\Exception $e) {
        continue;
    }

    if ($result['state'] > 0) {
            echo '<pre>';
            print_r($result);
            exit;

        }

   }

});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect', function () {

    $query = http_build_query([
        'client_id' => '4',
        'redirect_uri' => 'http://mylaravel.com/callback',
        'response_type' => 'code',
        'scope' => '',
    ]);

    return redirect(url('/oauth/authorize?'.$query));

});

 Route::get('/callback','OauthController@oauth');

 Route::get('/fractal','FractalController@test');

// Route::get('/callback', function (Illuminate\Http\Request $request) {
//     $http = new GuzzleHttp\Client;

//     return $request->code;
// });

//  {"data":{"id":3,"title":"hogchild","links":{"rel":"self","uri":"\/books\/3"}}}
// {"data":[{"id":1,"title":"hogfather","links":{"rel":"self","uri":"\/books\/1"}},{"id":2,"title":"hogmother","links":{"rel":"self","uri":"\/books\/2"}}]}
// HTTP/1.0 403 Forbidden A-Header: a value Another-Header: another value Cache-Control: no-cache, private Content-Type: application/json Date: Sun, 10 Sep 2017 09:51:32 GMT {"data":[{"id":1,"title":"hogfather","links":{"rel":"self","uri":"\/books\/1"}},{"id":2,"title":"hogmother","links":{"rel":"self","uri":"\/books\/2"}}]}
// HTTP/1.0 200 OK Cache-Control: no-cache, private Content-Type: application/json Date: Sun, 10 Sep 2017 09:51:32 GMT {"data":[{"id":1,"title":"hogfather","links":{"rel":"myself","uri":"\/books\/1"}},{"id":2,"title":"hogmother","links":{"rel":"myself","uri":"\/books\/2"}