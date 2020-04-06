<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class WechatClient extends Client
{
    const AUTHORIZE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    const GET_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const GET_USER_INFO_URL = 'https://api.weixin.qq.com/sns/userinfo';

    public function getLoginUrl($request)
    {
        $config = $this->config;
        $state  = md5(uniqid(time()));
        // 保存state到session中,后续跟回调过来的state做对比,防止CSRF攻击
        $request->session()->put('oauth_state', $state);
        Log::debug(__METHOD__, ['state' => $state, 'session_state' => $request->session()->get('oauth_state')]);
        $params   = [
            'appid'         => $config['client_id'],
            'redirect_uri'  => $config['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'snsapi_userinfo',
            'state'         => $state,
        ];
        $loginUrl = $this->getAuthorizeUrl() . '?' . http_build_query($params) . '#wechat_redirect';
        return $loginUrl;
    }

    protected function getAuthorizeUrl()
    {
        return self::AUTHORIZE_URL;
    }

    protected function getAccessTokenUrl(array &$params)
    {
        $config = $this->config;
        // TODO 待优化
        $params = [
            'appid'      => $config['client_id'],
            'secret'     => $config['client_secret'],
            'code'       => $params['code'],
            'grant_type' => 'authorization_code',
        ];
        return self::GET_TOKEN_URL . '?' . http_build_query($params);
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo($data)
    {
        $params   = [
            'access_token' => $data['access_token'],
            'openid'       => $data['openid'],
            'lang'         => 'zh_CN',
        ];
        $response = $this->getHttpClient()->request('GET', self::GET_USER_INFO_URL . '?' . http_build_query($params));
        $ret      = json_decode($response->getBody(), 'true');
        Log::debug(__METHOD__ . ':get userinfo', ['ret' => $ret]);
        if (!$ret) {
            throw new \RuntimeException('获取用户信息失败');
        }
        return [
            'id'         => $data['openid'] ?? 0,
            'nickname'   => $ret['nickname'] ?? '',
            'avatar_url' => $ret['headimgurl'] ?? ''
        ];
    }
}