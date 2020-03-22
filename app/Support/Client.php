<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

abstract class Client
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    protected function getHttpClient()
    {
        return app('http.client');
    }

    /**
     * 获取登录地址
     */
    public function getLoginUrl($request){
        $config = $this->config;
        $state  = md5(uniqid(time()));
        $params = [
            'client_id'    => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'state'        => $state,
            'response_type' => 'code',
        ];
        // 保存state到session中,后续跟回调过来的state做对比,防止CSRF攻击
        $request->session()->put('oauth_state', $state);
        $loginUrl = $this->getAuthorizeUrl() . '?' . http_build_query($params);
        return $loginUrl;
    }

    protected abstract function getAuthorizeUrl();

    protected abstract function getAccessTokenUrl(array &$params);

    /**
     * 获取Access Token
     */
    public function getAccessToken($request){
        if ($request->state != $request->session()->get('oauth_state')) {
            return new \RuntimeException('state not match');
        }
        $code = $request->code;
        // 获取ACCESS_TOKEN
        $params   = [
            'code'          => $code,
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret']
        ];
        $url = $this->getAccessTokenUrl($params);
        $response = $this->getHttpClient()->request('POST', $url, [
            'headers' => ['Accept' => 'application/json']
        ]);
        $ret      = json_decode($response->getBody(), true);
        if (!$ret || !isset($ret['access_token'])) {
            return new \RuntimeException('获取access token失败');
        }
        Log::debug(__METHOD__, ['ret' => $ret]);
        return $ret;
    }

    /**
     * 获取用户信息
     */
    public abstract function getUserInfo($token);


}