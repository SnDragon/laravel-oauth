<?php
return [
  'github' => [
      'client_id' => env('GITHUB_CLIENT_ID'),
      'client_secret' => env('GITHUB_CLIENT_SECRET'),
      'redirect_uri' => env('GITHUB_CALLBACK_URL')
  ]
];