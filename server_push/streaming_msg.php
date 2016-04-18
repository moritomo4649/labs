<?php
/*
	//date_default_timezone_set("America/New_York");
	header("Content-Type: text/event-stream\n\n");

	$counter = rand(1, 10);
	while (1) {
		// "ping" イベントを毎秒送信
		echo "event: ping\n";
		$curDate = time();
		echo 'data: {"time": "' . $curDate . '"}';
		echo "\n\n";

		// シンプルなメッセージをランダムな間隔で送信
		$counter--;

		if (!$counter) {
			echo 'data: time:' . $curDate . "\n\n";
			$counter = rand(1, 10);
		}
		ob_flush();
		flush();
		sleep(1);
	}
*/


	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	header("Access-Control-Allow-Origin: *");

	while (1) {
//		echo 'data: Hello from server! ' . date('Y-m-d H:i:s'), "\n\n";
		$html = getInstagramPicData();
		echo "data:".$html."\n\n";
		ob_flush();
		flush();
		sleep(4);
	}


/*
	header("Content-Type: text/event-stream"); 
	header("Cache-Control: no-cache"); 
	header("Access-Control-Allow-Origin: *"); 

	$lastEventId = floatval(isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : 0); 
	if ($lastEventId == 0) { 
		$lastEventId = floatval(isset($_GET["lastEventId"]) ? $_GET["lastEventId"] : 0); 
	} 

	echo ":" . str_repeat(" ", 2048) . "\n"; // 2 kB padding for IE 
	echo "retry: 2000\n"; 

	// event-stream 
	$i = $lastEventId; 
	$c = $i + 100; 
	while (++$i < $c) { 
		echo "id: " . $i . "\n"; 
		echo "data: " . $i . ";\n\n"; 
		ob_flush(); 
		flush(); 
		sleep(1); 
	}
*/

function getInstagramPicData(){

	// 設定項目
	$access_token = '' ;	// アクセストークン
	$request_url = 'https://api.instagram.com/v1/media/popular?access_token=' . $access_token ;		// リクエストURL

	// アイテムデータをJSON形式で取得する (CURLを使用)
	$curl = curl_init() ;

	// オプションのセット
	curl_setopt( $curl , CURLOPT_URL , $request_url ) ;
	curl_setopt( $curl , CURLOPT_HEADER, 1 ) ; 
	curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ;								// 証明書の検証を行わない
	curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;								// curl_execの結果を文字列で返す
	curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;										// タイムアウトの秒数

	// 実行
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;

	// 終了
	curl_close( $curl ) ;

	// 取得したデータ
	$json = substr( $res1, $res2['header_size'] ) ;										// 取得したデータ(JSONなど)
	$header = substr( $res1, 0, $res2['header_size'] ) ;								// レスポンスヘッダー (検証に利用したい場合にどうぞ)

	// HTML用
	$html = '' ;

	// JSONデータをオブジェクト形式に変換する
	$obj = json_decode( $json ) ;

	// HTMLを作成
//	$html .= '<h2>実行結果</h2>' ;

	// エラー判定
	if( !$obj || !isset($obj->data) ){
		$html .= '<p>データを取得できませんでした…。設定を再確認して下さい。</p>' ;
	}else{
		foreach( $obj->data as $item ){
			// 各データ
			$id = $item->id ;		// メディアID
			$type = $item->type ;		// メディアタイプ
			$link = $item->link ;		// リンク
			$images = $item->images->low_resolution->url ;		// 画像
			$videos = $item->videos->low_resolution->url ;		// 動画
			$date = $item->created_time ;		// 日付 (UNIX TIMESTAMP)
			$likes = ( isset($item->likes->count) ) ? $item->likes->count : 0 ;		// ライク数
			$comments = ( isset($item->comments->count) ) ? $item->comments->count : 0 ;		// コメント数

/*
			// ユーザー情報
			$user_id = $item->user->id ;		// 投稿者ID
			$user_name = $item->user->username ;		// 投稿者のユーザーネーム
			$user_full_name = ( isset($item->user->full_name) ) ? $item->user->full_name : '' ;		// 投稿者のユーザーネーム
			$user_profile_picture = ( isset( $item->user->profile_picture ) ) ? $item->user->profile_picture : '' ;		// アイコン画像

			// 場所情報
			$location_id = ( isset($item->location->id) ) ? $item->location->id : '' ;		// 場所ID
			$location_name = ( isset($item->location->name) ) ? $item->location->name : '' ;		// 場所名
			$location_lat = ( isset($item->location->latitude) ) ? $item->location->latitude : '' ;		// 緯度
			$location_long = ( isset($item->location->longitude) ) ? $item->location->longitude : '' ;		// 経度

			// タグ情報
			$tags = ( isset($item->tags) && !empty($item->tags) ) ? '#' . implode( '、#' , (array)$item->tags ) : '' ;

			// 日付の整形
			$date = date( 'Y/m/d H:i' , $date ) ;

			// ブラウザに出力
			$html .= '<dl>' ;
			$html .= 	'<dt>投稿者のID</dt>' ;
			$html .= 		'<dd>' . $user_id . '</dd>' ;
			$html .= 	'<dt>投稿者のユーザーネーム</dt>' ;
			$html .= 		'<dd><a href="https://instagram.com/' . $user_name . '" target="_blank">' . $user_name . '</a></dd>' ;

			// フルネームがある場合
			if( $user_full_name )
			{
				$html .= 	'<dt>投稿者のフルネーム</dt>' ;
				$html .= 		'<dd>' . $user_full_name . '</dd>' ;
			}

			// アイコン画像がある場合
			if( $user_profile_picture )
			{
				$html .= 	'<dt>投稿者のアイコン</dt>' ;
				$html .= 		'<dd><img class="_img" src="' . $user_profile_picture . '" width="75" height="75"></dd>' ;
			}

			$html .= 	'<dt>メディアID</dt>' ;
			$html .= 		'<dd>' . $id . '</dd>' ;
			$html .= 	'<dt>メディアタイプ</dt>' ;
			$html .= 		'<dd>' . $type . '</dd>' ;
			$html .= 	'<dt>メディアページ</dt>' ;
			$html .= 		'<dd><a href="' . $link . '" target="_blank">' . $link . '</a></dd>' ;
			$html .= 	'<dt>メディア</dt>' ;

			// ユーザーのタグ付けがある場合
			if( isset($item->users_in_photo) && !empty($item->users_in_photo) )
			{
				$html .= 		'<dd>' ;
				$html .= 			'<div style="position:relative; top:0; left:0; overflow:hidden; width:250px; height:250px;">' ;
				$html .= 				'<img src="' . $images . '" style="width:250px; height:250px;">' ;

				// タグ付けされたユーザーを配置していく
				foreach( $item->users_in_photo as $user )
				{
					if( isset($user->user->username) && isset($user->user->profile_picture) && isset($user->position->y) && isset($user->position->x) )
					{
						$x = ( 250 * $user->position->x ) - 15 ;
						$y = ( 250 * $user->position->y ) - 15 ;
						$html .= '<img src="' . $user->user->profile_picture . '" title="' . $user->user->username . '" width="30" height="30" style="position:absolute; top:' . $x . 'px; left:' . $y . 'px; vertical-align:bottom;">' ;
					}
				}

				$html .= 			'</div>' ;
				$html .= 			'<p>※' . count( $item->users_in_photo ) . '人のユーザーがタグ付けされています。</p>' ;
				$html .= 		'</dd>' ;
			}
			else
			{
				$html .= 		'<dd><img class="_img" src="' . $images . '"></dd>' ;
			}

			$html .= 	'<dt>投稿日時</dt>' ;
			$html .= 		'<dd>' . $date . '</dd>' ;
			$html .= 	'<dt>ライク数</dt>' ;
			$html .= 		'<dd>' . $likes . '</dd>' ;
			$html .= 	'<dt>コメント数</dt>' ;
			$html .= 		'<dd>' . $comments . '</dd>' ;

			// 場所IDがある場合
			if( $location_id )
			{
				$html .= 	'<dt>場所ID</dt>' ;
				$html .= 		'<dd>' . $location_id . '</dd>' ;
			}


			// 場所名がある場合
			if( $location_name )
			{
				$html .= 	'<dt>場所名</dt>' ;
				$html .= 		'<dd>' . $location_name . '</dd>' ;
			}


			// 緯度、経度がある場合
			if( $location_lat && $location_long )
			{
				$html .= 	'<dt>地図</dt>' ;
				$html .= 		'<dd><a href="https://www.google.co.jp/maps/@' . $location_lat . ',' . $location_long . ',15z" target="_blank">Google Mapsで位置を確認する</a></dd>' ;
			}

			// ハッシュタグがある場合
			if( $tags )
			{
				$html .= 	'<dt>タグ情報</dt>' ;
				$html .= 		'<dd>' . $tags . '</dd>' ;
			}

			$html .= '</dl>' ;
*/
			$html .= 		'<img width="100" height="100" src="' . $images . '">';
		}
	}
/*
	// 取得したデータ
	$html .= '<h2>取得したデータ</h2>' ;
	$html .= '<p>下記のデータを取得できました。</p>' ;
	$html .= 	'<h3>JSON</h3>' ;
	$html .= 	'<p><textarea rows="8">' . $json . '</textarea></p>' ;
	$html .= 	'<h3>レスポンスヘッダー</h3>' ;
	$html .= 	'<p><textarea rows="8">' . $header . '</textarea></p>' ;

	// アプリケーション連携の解除
	$html .= '<h2>アプリケーション連携の解除</h2>' ;
	$html .= '<p>このアプリケーションとの連携は、下記設定ページで解除することができます。</p>' ;
	$html .= '<p><a href="https://instagram.com/accounts/manage_access/" target="_blank">https://instagram.com/accounts/manage_access/</a></p>' ;
*/
//	$html .= 		'<video width="100" height="100" src="' . $videos . '" autoplay></video>';

	return $html;

}


?>