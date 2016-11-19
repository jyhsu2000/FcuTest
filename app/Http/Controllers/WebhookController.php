<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $entryJSONs = $json['entry'];
        foreach ($entryJSONs as $entryJSON) {
            $messageJSONs = $entryJSON['messaging'];
            foreach ($messageJSONs as $messageJSON) {
                $this->handleMessage($messageJSON);
            }
        }

        return response()->json();
    }

    private function handleMessage($messageJSON)
    {
        \Log::info('MessageJSON: ', $messageJSON);

        //不處理機器人自己發送的訊息
        if (isset($messageJSON['message']['is_echo']) && $messageJSON['message']['is_echo']) {
            return false;
        }
        //設定回覆訊息
        $responseMessage = '';
        if (isset($messageJSON['message']['text'])) {
            //文字訊息
            $responseMessage = 'Echo: ' . $messageJSON['message']['text'];
        } elseif (isset($messageJSON['message']['attachments'])) {
            //附件檔案
            //TODO: 依類型處理（檔案、圖片、貼圖、按讚）
            //FIXME: 不該只處理第一個，可能會有多個檔案的情況
            $responseMessage = 'url: ' . $messageJSON['message']['attachments'][0]['payload']['url'];
        } else {
            //不處理其他類型
            return false;
        }

        //回覆對象
        $recipientID = isset($messageJSON['sender']['id']) ? $messageJSON['sender']['id'] : null;
        if (!$recipientID) {
            return false;
        }

        \Log::info('RecipientID: ' . $recipientID);
        \Log::info('ResponseMessage: ' . $responseMessage . ' (' . Carbon::now()->toDateTimeString() . ')');

        //送出訊息
        $apiUrl = 'https://graph.facebook.com/v2.6/me/messages';
        $client = new Client();
        $response = $client->post($apiUrl, [
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

        \Log::info('Response: ', json_decode($response->getBody(), true));

        return true;
    }
}
