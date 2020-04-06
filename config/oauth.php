<?php
return [
    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect_uri'  => env('GITHUB_CALLBACK_URL')
    ],
    'weibo'  => [
        'client_id'     => env('WEIBO_CLIENT_ID'),
        'client_secret' => env('WEIBO_CLIENT_SECRET'),
        'redirect_uri'  => env('WEIBO_CALLBACK_URL')
    ],
    'qq' => [
        'client_id'     => env('QQ_CLIENT_ID'),
        'client_secret' => env('QQ_CLIENT_SECRET'),
        'redirect_uri'  => env('QQ_CALLBACK_URL')
    ],
    'wechat' => [
        'client_id'     => env('WECHAT_CLIENT_ID'),
        'client_secret' => env('WECHAT_CLIENT_SECRET'),
        'redirect_uri'  => env('WECHAT_CALLBACK_URL')
    ],
];