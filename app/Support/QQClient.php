<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class QQClient extends Client
{
    const AUTHORIZE_URL = 'https://graph.qq.com/oauth2.0/authorize';
    const GET_TOKEN_URL = 'https://graph.qq.com/oauth2.0/token';
    const GET_OPENID_URL = 'https://graph.qq.com/oauth2.0/me';
    const GET_USER_INFO_URL = 'https://graph.qq.com/user/get_user_info';

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

    /**
     * 获取用户信息
     */
    public function getUserInfo($token)
    {
        Log::debug(__METHOD__, ['token' => $token]);
        // 获取用户openid
        $url = self::GET_OPENID_URL . '?access_token=' . $token;
        $response = $this->getHttpClient()->request('GET', $url)->getBody();
        // 返回包:callback( {"client_id":"YOUR_APPID","openid":"YOUR_OPENID"} );
        $data = [];
        if(strpos($response, "callback") !== false){
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $data = json_decode($response, true);
        }
        Log::debug(__METHOD__.':get openid', ['ret' => $response, 'data' => $data]);
        if(!is_array($data) || !isset($data['openid'])){
            throw new \RuntimeException('获取openid失败');
        }
        // 根据openid和access_token获取用户数据
        $params = [
            'access_token' => $token,
            'oauth_consumer_key' => $data['client_id'],
            'openid' => $data['openid']
        ];
        $response = $this->getHttpClient()->request('GET', self::GET_USER_INFO_URL . '?' . http_build_query($params));
        $ret = json_decode($response->getBody(), 'true');
        Log::debug(__METHOD__.':get userinfo', ['ret' => $ret]);
        if(!$ret){
            throw new \RuntimeException('获取用户信息失败');
        }
        return [
            'id' => $data['openid'],
            'nickname' => $ret['nickname'] ?? '',
            'avatar_url' => $ret['figureurl'] ?? ''
        ];
    }

    protected function parseResponse($body)
    {
        Log::debug(__METHOD__, ['body' => $body]);
        $results = [];
        parse_str($body, $results);
        return $results;
    }

    protected function requestAccessToken($url){
        $response = $this->getHttpClient()->request('GET', $url);
        return $response->getBody();
    }
}