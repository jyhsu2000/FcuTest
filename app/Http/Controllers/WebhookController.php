<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

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

        $recipientID = $json['entry']['messaging']['sender']['id'];
        //送出訊息
        $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.6/me/messages']);
        $response = $client->post('/', [
            'query' => ['access_token' => env('FB_BOT_TOKEN')],
            'json'  => [
                'recipient' => $recipientID,
                'message'   => $message,
            ],
        ]);

        return response()->json();
    }
}
