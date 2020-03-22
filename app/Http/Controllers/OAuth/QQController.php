<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Support\OAuth;
use Illuminate\Http\Request;

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
}