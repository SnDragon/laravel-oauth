<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GithubController extends Controller
{
    /**
     * 返回github登录页
     */
    public function login(Request $request)
    {
        $url    = 'https://github.com/login/oauth/authorize';
        $config = config('oauth.github');
        $state  = md5(uniqid(time()));
        $params = [
            'client_id'    => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'state'        => $state,
        ];
        // 保存state到session中,后续跟回调过来的state做对比,防止CSRF攻击
        $request->session()->put('github_oauth_state', $state);
        $loginUrl = $url . '?' . http_build_query($params);
        return redirect($loginUrl);
    }

    /**
     * 接受授权成功后的回调
     */
    public function callback(Request $request)
    {
        $this->validate($request, [
            'code'  => 'required',
            'state' => 'required',
        ]);
        if ($request->state != $request->session()->get('github_oauth_state')) {
            return json_response(-1, 'state not match');
        }
        $code = $request->code;
        // 获取ACCESS_TOKEN
        $client   = new \GuzzleHttp\Client();
        $config   = config('oauth.github');
        $params   = [
            'code'          => $code,
            'client_id'     => $config['client_id'],
            'client_secret' => $config['client_secret']
        ];
        $url      = 'https://github.com/login/oauth/access_token?' . http_build_query($params);
        $response = $client->request('POST', $url, [
            'headers' => ['Accept' => 'application/json']
        ]);
        $ret      = json_decode($response->getBody(), true);
        Log::debug(__METHOD__, ['url' => $url, 'ret' => $ret]);
        if (!$ret || !isset($ret['access_token'])) {
            return json_response(-1, '获取access token失败');
        }

        $token = $ret['access_token'];
        $request->session()->put('github_oauth_token', $token);
        $request->session()->put('login_type', 'github');
        // TODO 放在index里
        // 获取user
        $response = $client->request('GET','https://api.github.com/user', [
            'headers' => ['Authorization' =>  'token ' . $token]
        ]);
        $ret = json_decode($response->getBody(), 'true');
        return json_response(0, 'success', ['ret' => $ret]);
    }
}