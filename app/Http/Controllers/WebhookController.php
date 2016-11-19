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

        if (!isset($json['entry'][0]['messaging'][0])) {
            return response()->json([]);
        }
        $message = '';
        if (isset($json['entry'][0]['messaging'][0]['text'])) {
            $message = 'Echo: ' . $json['entry'][0]['messaging'][0]['text'];
        } elseif (isset($json['entry'][0]['messaging'][0]['attachments'])) {
            $message = 'url: ' . $json['entry'][0]['messaging'][0]['attachments'][0]['payload']['url'];
        }

        $recipientID = isset($json['entry'][0]['messaging'][0]['sender']['id'])
            ? $json['entry'][0]['messaging'][0]['sender']['id'] : null;
        if (!$recipientID) {
            return response()->json(['No recipientID']);
        }

        //送出訊息
        $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.6/me/messages']);
        $response = $client->post('/', [
            'query' => ['access_token' => env('FB_BOT_TOKEN')],
            'json'  => [
                'recipient' => [
                    'id' => $recipientID,
                ],
                'message'   => [
                    'text' => $message,
                ],
            ],
        ]);

        \Log::info('Response: ', $response);

        return response()->json();
    }
}
