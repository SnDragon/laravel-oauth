<?php

namespace App\Support;

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
    public abstract function getLoginUrl($request);

    /**
     * 获取Access Token
     */
    public abstract function getAccessToken($request);

    /**
     * 获取用户信息
     */
    public abstract function getUserInfo($token);
}