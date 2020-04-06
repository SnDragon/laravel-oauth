<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Support\OAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    /**
     * 返回微信登录页
     */
    public function login(Request $request)
    {
        $loginUrl = OAuth::driver('wechat')->getLoginUrl($request);
        return redirect($loginUrl);
    }

    /**
     * 处理回调
     */
    public function callback(Request $request)
    {
        Log::debug(__METHOD__, ['params' => $request->all()]);
        $this->validate($request, [
            'code'  => 'required',
            'state' => 'required',
        ]);
        $driver = OAuth::driver('wechat');
        try {
            $data                = $driver->getAccessToken($request);
            $info                = $driver->getUserInfo($data);
            $info['login_type']  = 'wechat';
            $info['oauth_token'] = $data['access_token'];
            $request->session()->put('user_info', $info);
            return redirect('/');
        } catch (\Exception $e) {
            Log::error($e);
            return json_response(-1, $e->getMessage() ?: 'error');
        }
    }
}