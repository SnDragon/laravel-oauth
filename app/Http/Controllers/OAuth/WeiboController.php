<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Support\OAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeiboController extends Controller
{
    /**
     * 返回微博登录页
     */
    public function login(Request $request)
    {
        $loginUrl = OAuth::driver('weibo')->getLoginUrl($request);
        return redirect($loginUrl);
    }

    /**
     * 接受授权成功后的回调
     */
    public function callback(Request $request)
    {
        $this->validate($request, [
            'code'  => 'required',
            'state' => 'required',
        ]);
        $driver = OAuth::driver('weibo');
        try{
            $data = $driver->getAccessToken($request);
            $info  = $driver->getUserInfo($data);
            $info['login_type'] = 'weibo';
            $info['oauth_token'] = $data['access_token'];
            $request->session()->put('user_info', $info);
            return redirect('/');
        } catch (\Exception $e){
            Log::error($e);
            return json_response(-1, $e->getMessage() ?: 'error');
        }
    }
}