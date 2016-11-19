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

        //逐一處理每一則訊息
        $messageJSONs = $json['entry'][0]['messaging'];
        foreach ($messageJSONs as $messageJSON) {
            $this->handleMessage($messageJSON);
        }

        return response()->json();
    }

    private function handleMessage($messageJSON)
    {
        //設定回覆訊息
        $responseMessage = '';
        if (isset($messageJSON['text'])) {
            $responseMessage = 'Echo: ' . $messageJSON['text'];
        } elseif (isset($messageJSON['attachments'])) {
            $responseMessage = 'url: ' . $messageJSON['attachments'][0]['payload']['url'];
        }

        //回覆對象
        $recipientID = isset($messageJSON['sender']['id']) ? $messageJSON['sender']['id'] : null;
        if (!$recipientID) {
            return false;
        }

        \Log::info('RecipientID: ' . $recipientID);

        //送出訊息
        $client = new Client(['base_uri' => 'https://graph.facebook.com/v2.6/me/messages']);
        $response = $client->post('/', [
            'query' => [
                'access_token' => env('FB_BOT_TOKEN'),
            ],
            'json'  => [
                'recipient' => [
                    'id' => $recipientID,
                ],
                'message'   => [
                    'text' => $responseMessage,
                ],
            ],
        ]);

        \Log::info('Response: ', $response);

        return true;
    }
}
