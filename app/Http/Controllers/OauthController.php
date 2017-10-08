<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class OauthController extends Controller
{

 public function oauth(Request $request)
  {
    $http = new Client;

    $response = $http->post('http://192.168.10.10:8001/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => '3',
            'client_secret' => 'UzJYaNedFvuUOnurczK1lwSfcWbFbiEDdoQvhAYX',
            'redirect_uri' => 'http://mylaravel.com/callback',
            'code' => $request->code,
        ],
    ]);

    return json_decode((string) $response->getBody(), true);
}
}
