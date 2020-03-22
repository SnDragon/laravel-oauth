<?php

namespace App\Support;

class WeiboClient extends Client
{

    const AUTHORIZE_URL = 'https://api.weibo.com/oauth2/authorize';
    const GET_TOKEN_URL = 'https://api.weibo.com/oauth2/access_token';
    const GET_USER_INFO_URL = 'https://api.weibo.com/2/users/show.json';

    /**
     * 获取用户信息
     */
    public function getUserInfo($data)
    {
        // 获取user
        $params = [
            'access_token' => $data['access_token'],
            'uid'          => $data['uid']
        ];
        $response = $this->getHttpClient()->request('GET', self::GET_USER_INFO_URL . '?' . http_build_query($params));
        $ret = json_decode($response->getBody(), 'true');
        if(!$ret){
            throw new \RuntimeException('获取用户信息失败');
        }
        return [
            'id' => $ret['id'] ?? 0,
            'nickname' => $ret['screen_name'] ?? '',
            'avatar_url' => $ret['profile_image_url'] ?? ''
        ];
    }

    protected function getAuthorizeUrl()
    {
        return self::AUTHORIZE_URL;
    }

    protected function getAccessTokenUrl(array &$params)
    {
        $params['grant_type'] = 'authorization_code';
        $params['redirect_uri'] = $this->config['redirect_uri'];
        return self::GET_TOKEN_URL . '?' . http_build_query($params);
    }
}