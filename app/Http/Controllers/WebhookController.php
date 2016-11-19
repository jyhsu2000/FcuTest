<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Resource;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $json = $request->all();

        \Log::info('Request: ', $json);

        //驗證
        if (isset($json['hub_verify_token']) && $json['hub_verify_token'] == 'fcu_test_token') {
            return response()->json((int)$json['hub_challenge']);
        }

        if (!isset($json['message'])) {
            return response()->json([]);
        }
        $message = '';
        if (isset($json['message']['text'])) {
            $message = 'Echo: ' . $json['message']['text'];
        } elseif (isset($json['message']['attachments'])) {
            $message = 'url: ' . $json['message']['attachments'][0]['payload']['url'];
        }

        return response()->json(['message' => ['text' => $message]]);
    }
}
