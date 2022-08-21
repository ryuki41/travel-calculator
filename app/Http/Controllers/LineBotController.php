<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Constant\HTTPHeader;

class LineBotController extends Controller
{
    public function reply(Request $request) 
    {
		// アクセストークンを使いCurlHTTPClientをインスタンス化
		$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('a7RYrnOpjib7rNgYH1ZG1ZCzwKjjOcaI8HE3IjBalpzc31eJiBsTO/rba0q+Qv2kiNu75g0TT/fEbmbg+WKWR5yOQXVRBziqQkjgxJJrlfVTT3MawZGX7lKFj3MW1L0pjYBJcNP3ys61Wk1x4Q4IFUc1Wk1x4Q4IFUc1/Dw1x4Q4IFU9/');
		// CurlHTTPClientとチャンネルシークレットを使いLINEBotをインスタンス化
		$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => 'ee053d87eaf697557a6251b14868eb50']);
	
		// $httpClient = new CurlHTTPClient($_ENV['LINE_CHANNEL_ACCESS_TOKEN']);
		// $bot = new LINEBot($httpClient, ['channelSecret' => $_ENV['LINE_CHANNEL_SECRET']]);

		// LINE Messaging APIがリクエストに付与した署名を取得
		$signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
		if(!$signature){
			abort(400);
		}
		//ラインプラットフォーム以外
		// if(!SignatureValidator::validateSignature($request->getContent(), $_ENV['LINE_CHANNEL_SECRET'], $signature){
		// 	abort(400);
		// }

		$events = $bot->parseEventRequest($request->getContent(), $signature);
		foreach ($events as $event) {
			// 返信先Token
			$replyToken = $event->getReplyToken();

			// おうむ返しする
			$send_Text = $event->getText(); // 送信されたメッセージ
			$response = $bot->replyText($replyToken, $send_Text);
			Log::debug($response->getHTTPStatus());
			return;
		}

    }
}
