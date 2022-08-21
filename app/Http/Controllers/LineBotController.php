<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LDAP\Result;

class LineBotController extends Controller
{
    public function reply(Request $request) 
    {
		Log::debug("ああああ");	
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

		switch($event){
			// フォローイベント（友達登録時/ブロック解除時）
			case($event instanceof FollowEvent):
				// 例えばDBにユーザーIDを格納したりする
				$user_id = $event->getUserId();
				User::AddDate($user_id);

				$message = '友達登録ありがとう！';
				$response = $bot->replyText($replyToken, $message);
				return $response->getHTTPStatus();

			// フォロー解除イベント（ブロック時）
			// case($event instanceof UnfollowEvent):
			// 	// 例えばDBからユーザー削除したりする
			// 	$user_id = $event->getUserId();
			// 	User::DeleteData($user_id)
			// 	return [];

			// おうむ返しする
			case($event instanceof TextMessage):
				$send_Text = $event->getText(); // 送信されたメッセージ
				$response = $bot->replyText($replyToken, $send_Text);
				return $response->getHTTPStatus();

			// スタンプメッセージ
			case($event instanceof StickerMessage):
				$message = 'スタンプありがとう';
				$response = $bot->replyText($replyToken, $message);
				return $response->getHTTPStatus();

			default:
				return;
			}
		}

    }

	public function testReply(Request $request)
	{
		Log::debug("いいい");	
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

		switch($event){
			// フォローイベント（友達登録時/ブロック解除時）
			case($event instanceof FollowEvent):
				// 例えばDBにユーザーIDを格納したりする
				$user_id = $event->getUserId();
				User::AddDate($user_id);

				$message = '友達登録ありがとう！';
				$response = $bot->replyText($replyToken, $message);
				return $response->getHTTPStatus();

			// フォロー解除イベント（ブロック時）
			// case($event instanceof UnfollowEvent):
			// 	// 例えばDBからユーザー削除したりする
			// 	$user_id = $event->getUserId();
			// 	User::DeleteData($user_id)
			// 	return [];

			// おうむ返しする
			case($event instanceof TextMessage):
				$send_Text = $event->getText(); // 送信されたメッセージ
				$response = $bot->replyText($replyToken, $send_Text);
				return $response->getHTTPStatus();

			// スタンプメッセージ
			case($event instanceof StickerMessage):
				$message = 'スタンプありがとう';
				$response = $bot->replyText($replyToken, $message);
				return $response->getHTTPStatus();

			default:
				return;
			}
		}
	}
}
