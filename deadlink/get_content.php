<?php
$FP				 = null;
$CON_SLEEP		 = 4000000;
$CHAR_CODE		 = 'EUC-JP';
$SOCK_TIMEOUT	 = 15;
$MAX_RETRY		 = 3;
$HOME_DIR		 = '/home/nishizawa/deadlink/test/';
$URL_DIR		 = $HOME_DIR.'url/';
$LOG_DIR		 = $HOME_DIR.'log/';
$LOG_FILE		 = $LOG_DIR.$argv[1];
$USER_AGENT		 = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_4 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12H143 Safari/600.1.4';
$STATUS_CODE_MSG = array(
	0 => 'このページは表示できません',
	1 => 'titleタグがありません'
);
$HEADERS = array(
	"HTTP/1.1",
	"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
	"Accept-Encoding:gzip ,deflate",
	"Accept-Language: ja,en-US;q=0.7,en;q=0.3",
	"Connection:keep-alive",
	"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko",
	"Expect:"
);

$MOBILE_HEADERS = array(
	"HTTP/1.1",
	"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
	"Accept-Encoding:gzip ,deflate",
	"Accept-Language: ja,en-US;q=0.7,en;q=0.3",
	"Connection:keep-alive",
	"User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 8_4 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12H143 Safari/600.1.4",
	"Expect:"
);

try{
	$old_url = '';
	$fp = fopen($LOG_FILE,"w");
	while(!feof(STDIN)) {
		$url = trim(fgets(STDIN));
		if($url === FALSE) {
			if(feof(STDIN))
				break;
			continue;
		}
		$first_url = $url;
		$access_flg = false;
		if( $old_url != $first_url ){
			$access_flg	 = true;
			$old_url	 = $first_url;
		}
		if( $access_flg === true ){
			if( $url != '' ){
				//アクセス先コンテンツ取得
				$retry_count = 0;
				$list_info = getAccessContents(&$url);
				if( !is_array($list_info) ){
					fwrite($fp,"{$first_url}\t{$list_info}\n");
					continue;
				}
				//タイトル取得
				if( preg_match("/<title>(.*?)<\/title>/is",$list_info[2],$title) > 0 ){
					$title = preg_replace("/\r/"," ",$title);
					$title = preg_replace("/\n/"," ",$title);
				}else{
					$title[1] = mb_convert_encoding($STATUS_CODE_MSG[1],"EUC-JP","UTF-8");
				}
				if( isset($STATUS_CODE_MSG[$list_info[1]]) )
					$title[1] = mb_convert_encoding($STATUS_CODE_MSG[$list_info[1]],"EUC-JP","UTF-8");
				fwrite($fp,"{$first_url}\t{$list_info[1]}\t{$list_info[0]}\t".trim($title[1])."\n");
			}
		}else{
			if( !is_array($list_info) ){
				fwrite($fp,"{{$first_url}\t{$list_info}\n");
				continue;
			}
			fwrite($fp,"{$first_url}\t{$list_info[1]}\t{$list_info[0]}\t".trim($title[1])."\n");
		}
	}
	fclose($fp);
}catch(Exception $e){
	fwrite($fp,"{$first_url}\t\t\texception_err\n");
}

	function curlConnect(&$url){
		global $fp;
		global $FP;
		global $SOCK_TIMEOUT;
		global $CON_SLEEP;
		global $con_count;
		global $USER_AGENT;
		global $HEADERS;
		global $MOBILE_HEADERS;
		global $argv;
		global $listData;

		$header = $HEADERS;
		if( $argv[2] == 'mobile' ){
			$MOBILE_HEADERS[] = $_SERVER['HTTP_COOKIE'];
			$header = $MOBILE_HEADERS;
		}

		try{
			usleep($CON_SLEEP);
			$FP= curl_init($url);
			if( $FP === false ){
				if( $con_count > $MAX_RETRY )
					return false;
				$con_count++;
				curlConnect(&$url);
			}
			curl_setopt($FP, CURLOPT_HEADER, false);
			curl_setopt($FP, CURLOPT_SSLVERSION, 1);
			curl_setopt($FP, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($FP, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($FP, CURLOPT_TIMEOUT, $SOCK_TIMEOUT);
			curl_setopt($FP, CURLOPT_COOKIEJAR, "cookie"); 
			curl_setopt($FP, CURLOPT_COOKIEFILE, "cookie");
			curl_setopt($FP, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($FP, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
			curl_setopt($FP, CURLOPT_HTTPHEADER, $header);
			return true;
		}catch(Exception $e){
			fwrite($fp,"{$url}\t\t\texception_err\n");
		}
	}

	function setEncodeCharCode(&$record,&$char_flg,$url=null){
		global $CHAR_CODE;

		//エンコード文字コード設定
		$tmpCharCode = 'euc-jp';

		//HTMLタグで出力する文字コード
		$code_flg = preg_match("/charset[[:space:]]{0,}[=-][[:space:]]{0,}[\"']{0,}(x\-euc\-jp|euc\-jp|ms_kanji|x\-sjis|sjis|utf\-8|jis|shift_jis|shift\-jis|iso\-2022\-jp)[\"']{0,}/is",$record,$listCharCode);
		$tmpCharCode = $listCharCode[1];
		if( $code_flg != 1 ){
			$code_flg = preg_match("/<\?xml.*?version[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}\d\.\d[\"']{0,}.*?encoding[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}.*?\?>/is",$record,$listCharCode);
			$tmpCharCode = $listCharCode[1];
		}
		if( $char_flg === false && $code_flg !== false && (int)$code_flg > 0 ){
			$char_flg = true;
			$check_flg2 = preg_match("/utf\-8/i",$tmpCharCode);
			if( $check_flg2 !== false && $check_flg2 > 0 )
				$CHAR_CODE = "UTF-8";
			$check_flg3 = preg_match("/euc/i",$tmpCharCode);
			if( $check_flg3 !== false && $check_flg3 > 0 )
				$CHAR_CODE = "EUC-JP";
			$check_flg4 = preg_match("/x\-sjis|sjis|shift_jis|shift\-jis|ms_kanji/i",$tmpCharCode);
			if( $check_flg4 !== false && $check_flg4 > 0 )
				$CHAR_CODE = "SJIS";
			if( $tmpCharCode == "jis" || $tmpCharCode == "JIS" )
				$CHAR_CODE = "JIS";
			$check_flg5 = preg_match("/iso\-2022\-jp/i",$tmpCharCode);
			if( $check_flg5 !== false && $check_flg5 > 0 )
				$CHAR_CODE = "ISO-2022-JP";
		}
	}

	function setTryEncoding(&$content,&$CHAR_CODE){
		$char_flg = mb_check_encoding($content,'SJIS');
		if( $char_flg === true ){
			$CHAR_CODE = 'SJIS';
			return;
		}else{
			$char_flg = mb_check_encoding($content,'EUC-JP');
			if( $char_flg === true ){
				$CHAR_CODE = 'EUC-JP';
				return;
			}else{
				$char_flg = mb_check_encoding($content,'UTF-8');
				if( $char_flg === true ){
					$CHAR_CODE = 'UTF-8';
					return;
				}else{
					$char_flg = mb_check_encoding($content,'ISO-2022-JP');
					if( $char_flg === true ){
						$CHAR_CODE = 'ISO-2022-JP';
						return;
					}else{
						$char_flg = mb_check_encoding($content,'JIS');
						if( $char_flg === true )
							$CHAR_CODE = 'JIS';
						return;
					}
				}
			}
		}
	}

	function getAccessContents(&$url){
		global $fp;
		global $FP;
		global $CHAR_CODE;
		global $MAX_RETRY;
		global $STATUS_CODE_MSG;
		global $retry_count;
		global $listData;

		try{
			$char_flg	 = false;
			$html		 = null;
			$con_count	 = 0;
			$err_flg	 = curlConnect(&$url);
			if( $err_flg !== false ){
				$cnt_type_flg	 = false;
				$htmlSource		 = curl_exec($FP);
				if( $htmlSource === false ){
					if( $retry_count > $MAX_RETRY )
						return 'exec_err';
					$retry_count++;
					curlConnect(&$url);
					$content = getAccessContents(&$url);
				}
				$listCurlData = explode("\n",$htmlSource);
				foreach($listCurlData as $record){
					//エンコード文字コード設定
					setEncodeCharCode(&$record,&$char_flg);
//					if( $cnt_type_flg === true ){
						if( $char_flg === false )
							setTryEncoding(&$record,&$CHAR_CODE);
						//エンコード文字コード設定
						$html .= preg_replace("/\n/s","",mb_convert_encoding($record,"EUC-JP",$CHAR_CODE));
//					}
//					if( preg_match("/^Content-Type/i",$record) !== false && preg_match("/^Content-Type/i",$record) > 0 )
//						$cnt_type_flg = true;
				}
			}else{
				return 'connect_err';
			}
			$list_info = curl_getinfo($FP);
			return array($list_info['url'],$list_info['http_code'],$html);
		}catch(Exception $e){
			fwrite($fp,"{{$url}\t\t\texception_err\n");
		}
	}




?>