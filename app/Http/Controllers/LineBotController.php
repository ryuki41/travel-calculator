<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\SignatureValidator;

class LineBotController extends Controller
{
    public function reply(Request $request) 
    {
		// アクセストークンを使いCurlHTTPClientをインスタンス化
		$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('OKuB7AFUszD6wNNGj8o7jeVk2qY0mZdB6EQA8lRnQCt7SweLOulntBTxoGoxauHviNu75g0TT/fEbmbg+WKWR5yOQXVRBziqQkjgxJJrlfUZDq1MJl557WoDw9xW+pssoglKF6/iCRCu0wGZXd4Rd49PbdgDzCFqoOLOYbqAITQ=');
		// CurlHTTPClientとチャンネルシークレットを使いLINEBotをインスタンス化
		$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '80633040668e36eca56a2503b623b5e4']);
	
		// LINE Messaging APIがリクエストに付与した署名を取得
		$signature = $request->headers->get(HTTPHeader::LINE_SIGNATURE);
		if(!$signature){
			abort(400);
		}
		//ラインプラットフォーム以外
		if(!SignatureValidator::validateSignature($request->getContent(), '80633040668e36eca56a2503b623b5e4', $signature)) {
			abort(400);
		}

		$events = $bot->parseEventRequest($request->getContent(), $signature);
		foreach ($events as $event) {
			// 返信先Token
			$replyToken = $event->getReplyToken();
			// おうむ返しする
			$send_Text = $event->getText(); 
			// 送信されたメッセージ
			$response = $bot->replyText($replyToken, $send_Text);
			return;
		}

    }
}
