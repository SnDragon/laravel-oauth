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
        Log::debug(__METHOD__,[ 'state' => $state, 'session_state' => $request->session()->get('oauth_state')]);
        $loginUrl = $this->getAuthorizeUrl() . '?' . http_build_query($params);
        return $loginUrl;
    }

    protected abstract function getAuthorizeUrl();

    protected abstract function getAccessTokenUrl(array &$params);

    /**
     * 获取Access Token
     */
    public function getAccessToken($request){
        Log::debug(__METHOD__, ['state' => $request->state, 'session_state' =>  $request->session()->get('oauth_state')]);
        if ($request->state != $request->session()->get('oauth_state')) {
            throw new \RuntimeException('state not match');
        }
        // 获取ACCESS_TOKEN
        $params = [
            'code'          => $request->code,
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret']
        ];
        $url = $this->getAccessTokenUrl($params);
        $response = $this->requestAccessToken($url);
        $ret      = $this->parseResponse($response);
        Log::debug(__METHOD__, ['ret' => $ret]);
        if (!$ret || !isset($ret['access_token'])) {
            throw new \RuntimeException('获取access token失败');
        }
        return $ret;
    }

    /**
     * 获取用户信息
     */
    public abstract function getUserInfo($token);

    protected function parseResponse($response)
    {
        return json_decode($response, true);
    }

    protected function requestAccessToken($url)
    {
        Log::debug(__METHOD__, ['url' => $url]);
        $response = $this->getHttpClient()->request('POST', $url, [
            'headers' => ['Accept' => 'application/json']
        ]);
        return $response->getBody();
    }
}