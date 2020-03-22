<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;

class IndexController
{
    public function welcome()
    {
        return view('welcome');
    }

    public function login()
    {
        return view('login');
    }

    public function index(Request $request)
    {
        if(!$this->isLogin($request)){
            return redirect('login');
        }else{
            $info = $request->session()->get('user_info');
            return view('index', $info);
        }
    }

    public function logout(Request $request){
        $request->session()->flush();
        $request->session()->regenerate(true);
        return redirect('/login');
    }

    protected function isLogin(Request $request){
        return !empty($request->session()->get('user_info'));
    }
}