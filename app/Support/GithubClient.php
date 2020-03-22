<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class GithubClient extends Client
{
    const AUTHORIZE_URL = 'https://github.com/login/oauth/authorize';
    const GET_TOKEN_URL = 'https://github.com/login/oauth/access_token';
    const GET_USER_INFO_URL = 'https://api.github.com/user';


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

    protected function getAuthorizeUrl()
    {
        return self::AUTHORIZE_URL;
    }

    protected function getAccessTokenUrl(array &$params)
    {
        return self::GET_TOKEN_URL . '?' . http_build_query($params);
    }
}