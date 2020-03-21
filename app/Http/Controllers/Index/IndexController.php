<?php

namespace App\Http\Controllers\Index;

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

    public function index()
    {
        return json_response(0, 'success');
    }
}