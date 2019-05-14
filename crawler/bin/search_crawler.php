<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/home/moritomo/labs/crawler/crawler");
require_once("utility/crawler_utility_renewal.php");

//使い方：php search_craler.php https://gamewith.jp/ img b 2

//$in_url			 = urldecode($argv[1]);		//$_GET['url']
$in_url			 = $argv[1];		//$_GET['url']
$exclusion_mode		 = $argv[2];			//$exclusion_mode検知対象both/text/img
$domain_type		 = $argv[3];			//別ドメインを検地するa/しないb
$search_max_depth	 = $argv[4];			//階層
//$badwords		 = urldecode($argv[5]);		//$_GET['badwords']
$badwords		 = '';				//$_GET['badwords']
$checked1		 = "checked";
$checked2		 = "";
$toolMsg;

$id			 = getStartInfoIdDb();
//$id			 = 1;
$in_url			 = getMultiByteEncodeString($in_url);
$EXIST_URL		 = $in_url;
$search_flg		 = 'start';
print $in_url."\n";
	//検知時間開始
	$start_time = time();

	list($target_domain,$target_path) = getHost($in_url);
	$search_host = $target_domain;
	$url_exist_flg = false;

	$exist_url	 = $in_url;
	$firstAccessSource = null;
	//過去に同じドメインで検知していない場合、新規にURLを取得する
	//リダイレクトしているかチェック
	list($location_url,$firstAccessSource) = checkRedirectUrl($in_url,$domain_type,$search_host,$exclusion_mode,$id,$firstAccessSource);

	//リダイレクトしていれば、リダイレクト先を第１階層とする
	if( !is_null($location_url) )
		$in_url = $location_url;
	//curlでの接続エラー
	if( $location_url == 'CURL_CON_ERR' ){
		exit;
	}

	//検知先のURLリストを取得
	$LIST_SEC_URL = setDepthUrl($in_url,$domain_type,$search_host,$exclusion_mode,$search_max_depth,$id,$badwords,$firstAccessSource);
print_r($LIST_SEC_URL);
print "mo\n";
//exit;	
	$listTmpData = array();
	if( !empty($LIST_SEC_URL) || $search_max_depth == 1 ){

		$err_exist_flg	 = true;
		$listSaveUrl	 = $LIST_SEC_URL;

		//同じドメインのみ検知する場合
		if( $domain_type == 'b' ){
			$domain_exist_flg = checkSearchDomain($LIST_SEC_URL,$target_domain);
			//検知先のURLがすべて別サーバーのドメインだったら
			if( !is_null($domain_exist_flg) ){
print "_/_/_/_/_/_/_/_/_/_/_/_/_/\n";
				exit;
			}
		}
		//二階層目のURLを取り出す
		foreach($LIST_SEC_URL as $key => $sec_url){
			$html				 = null;

			//キャッシュを利用しない
			if( is_null($html) ){
				setHostPath($sec_url);
				$listUrl = array();
				if( $exclusion_mode == 'move' ){
					$exist_flg = checkMoveFile($sec_url,$domain_type,$target_domain);
					if( is_null($exist_flg) ){
print "MOVIE<>".$key."<>".$sec_url."\n";
						$listUrl[] = $sec_url;
						insertImgUrlDb($listUrl,$id);
					}
				}elseif( $exclusion_mode == 'both' || $exclusion_mode == 'img' ){
					$exist_flg = exclusionBothFile($sec_url,$domain_type,$target_domain);
					if( $exist_flg === true ){
						$exist_flg = checkImgFile($sec_url,$domain_type,$target_domain);
						if( $exist_flg === false ){
print "IMG<>".$key."<>".$sec_url."\n";
							$listUrl[] = $sec_url;
							insertImgUrlDb($listUrl,$id);
						}
						continue;
					}
print $key."<>".$sec_url."\n";
				}
				$char_flg			 = false;
				$locationUrl		 = null;
				//ホストとパスを設定
				$err_flg = curlConnect($sec_url);
			}

			if( $err_flg !== false || !is_null($html) ){
				if( is_null($html) ){
					$start						 = _MicrotimeFloat();
					$cnt_type_flg				 = false;
					$frame_exist_flg			 = false;
					$status_notfound_flg		 = false;
					$content_length				 = 0;
					$htmlSource					 = curl_exec($FP);
					$check_flg					 = curlClose($sec_url);
					$listCurlData				 = explode("\n",$htmlSource);
					foreach($listCurlData as $record){
						//検知先URLの文字コードを取得
						setEncodeCharCode($record,$char_flg,$sec_url);
						if( $cnt_type_flg === true ){
							//文字コードが取得できなかったとき
							if( $char_flg === false )
								setTryEncoding($record,$CHAR_CODE);
							$record = mb_convert_encoding($record,"EUC-JP",$CHAR_CODE);

							//1行のコンテンツの占める割合が基準を超えているのかチェケラ
							$ratio_flg = checkContentRatio($content_length,$record);
							if( is_null($ratio_flg) ){
								//リダイレクトURLがあれば取得
								$meta_check_flg = regMetaLocateUrl($html,$locationUrl,$domain_type,$search_host,$exclusion_mode,$id,$CHAR_CODE);
								if( !$meta_check_flg )
									continue;
							}
							$tmp = preg_replace("/\n/s","",$record);
							$html .= $tmp;
						}else{
							//コンテンツの長さを取得
							setContentLength($content_length,$record);
							//HTTPステータスをチェケラ
							$status_flg = checkContentStatus($record);
							//ステータスが４００～なら
							if( !is_null($status_flg) ){
								$status_notfound_flg = true;
								return true;
							}
						}

						if( preg_match("/^Content-Type/i",$record) !== false && preg_match("/^Content-Type/i",$record) > 0 )
							$cnt_type_flg = true;
					}

					if( $status_notfound_flg === true )
						continue;
				}
				//画面に表示されない部分(タグなど)を削除
				delExclusionWord($html);
				//画像があれば取得
				setLastPageImageUrl($sec_url,$id,$CHAR_CODE,$htmlSource,$exclusion_mode);

			}else{
				continue;
			}
			$CHAR_CODE = "SJIS";
		}
	}
	$etime = time() - $start_time;


