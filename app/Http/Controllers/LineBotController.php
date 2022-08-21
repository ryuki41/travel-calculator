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
		$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['LINE_CHANNEL_ACCESS_TOKEN']);
		// CurlHTTPClientとチャンネルシークレットを使いLINEBotをインスタンス化
		$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['LINE_CHANNEL_SECRET']]);
	
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
			return $response->getHTTPStatus();
		}

    }
}
