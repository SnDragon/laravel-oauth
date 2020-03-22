<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class GithubClient extends Client
{
    const AUTHORIZE_URL = 'https://github.com/login/oauth/authorize';
    const GET_TOKEN_URL = 'https://github.com/login/oauth/access_token';
    const GET_USER_INFO_URL = 'https://api.github.com/user';

    /**
     * 获取登录地址
     */
    public function getLoginUrl($request)
    {
        $config = $this->config;
        $state  = md5(uniqid(time()));
        $params = [
            'client_id'    => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'state'        => $state,
        ];
        // 保存state到session中,后续跟回调过来的state做对比,防止CSRF攻击
        $request->session()->put('github_oauth_state', $state);
        $loginUrl = self::AUTHORIZE_URL . '?' . http_build_query($params);
        return $loginUrl;
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($token)
    {
        // 获取user
        $response = $this->getHttpClient()->request('GET', self::GET_USER_INFO_URL, [
            'headers' => ['Authorization' =>  'token ' . $token]
        ]);
        $ret = json_decode($response->getBody(), 'true');
        if(!$ret){
            throw new \RuntimeException('获取用户信息失败');
        }
        return [
            'id' => $ret['id'] ?? 0,
            'nickname' => $ret['login'] ?? '',
            'avatar_url' => $ret['avatar_url'] ?? ''
        ];
    }

    /**
     * 获取Access Token
     */
    public function getAccessToken($request)
    {
        if ($request->state != $request->session()->get('github_oauth_state')) {
            return new \RuntimeException('state not match');
        }
        $code = $request->code;
        // 获取ACCESS_TOKEN
        $params   = [
            'code'          => $code,
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret']
        ];
        $url      = self::GET_TOKEN_URL . '?' . http_build_query($params);
        $response = $this->getHttpClient()->request('POST', $url, [
            'headers' => ['Accept' => 'application/json']
        ]);
        $ret      = json_decode($response->getBody(), true);
        if (!$ret || !isset($ret['access_token'])) {
            return new \RuntimeException('获取access token失败');
        }
        Log::debug(__METHOD__, ['ret' => $ret]);
        return $ret['access_token'];
    }
}