<?php

namespace App\Support;

class QQClient extends Client
{
    const AUTHORIZE_URL = 'https://graph.qq.com/oauth2.0/authorize';
    const GET_TOKEN_URL = 'https://github.com/login/oauth/access_token';
    const GET_USER_INFO_URL = 'https://api.github.com/user';

    protected function getAuthorizeUrl()
    {
        return self::AUTHORIZE_URL;
    }

    protected function getAccessTokenUrl(array &$params)
    {
        // TODO: Implement getAccessTokenUrl() method.
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($token)
    {
        // TODO: Implement getUserInfo() method.
    }
}