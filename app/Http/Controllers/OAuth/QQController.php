<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Support\OAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QQController extends Controller
{
    /**
     * 返回github登录页
     */
    public function login(Request $request)
    {
        $loginUrl = OAuth::driver('qq')->getLoginUrl($request);
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
        $driver = OAuth::driver('qq');
        try {
            $data                = $driver->getAccessToken($request);
            $token               = $data['access_token'];
            $info                = $driver->getUserInfo($token);
            $info['login_type']  = 'qq';
            $info['oauth_token'] = $token;
            $request->session()->put('user_info', $info);
            return redirect('/');
        } catch (\Exception $e) {
            Log::error($e);
            return json_response(-1, $e->getMessage() ?: 'error');
        }
    }
}