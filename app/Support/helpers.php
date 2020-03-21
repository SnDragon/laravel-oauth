<?php

if(!function_exists('json_response')){
    function json_response($code = 0, $message = '', $data = []){
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}