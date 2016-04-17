<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "書き換えてください");
require_once('db/crawler_db.php');
session_start();

//グローバル変数関連
//$SEARCH_URL			 = null;
$FP						 = null;
$PARAM					 = null;
$HOST					 = null;
$PATH					 = null;
$EXIST_URL				 = null;
$BASE_URL				 = null;
$BASE_PARAM				 = null;
$BASE_HOST				 = null;
$BASE_PATH				 = null;
$TARGET_URL				 = null;
$LIST_CON_ERR_URL		 = array();
$LIST_CLOSE_ERR_URL		 = array();
$LIST_SEC_URL			 = array();
$LIST_FRAME_URL			 = array();
$LIST_IMG_URL			 = array();
$LIST_JS_URL			 = array();
$LIST_LINK_URL			 = array();
$LIST_CHECKED_URL		 = array();
$LIST_GETED_URL			 = array();

//メッセージのリスト関連
$LIST_ERR_MSG			 = array(
							"CURL_CON_ERR"		 => "curlの接続に失敗しました。",
							"CURL_CON_TIMEOUT"	 => "curlの接続がタイムアウトになりました"
		);

//パラメータ関連
$CHECK_ERR_FLG			 = false;
$CHAR_CODE				 = 'EUC-JP';
$SOCK_TIMEOUT			 = 8;
$SEARCH_LEN				 = 2;
$N_BEST_NUM				 = 20;
$CONTENT_LENGTH_RATIO	 = 50;
$LIMIT_MAIL_TIME		 = 120;
$CURL_CON_SLEEP			 = 100000;
$MAX_DEPTH				 = 500;

//DB関連
$DB_JUDGE_HOST			 = 'DBホスト';
$DB_JUDGE_USER			 = 'DBユーザー名';
$DB_JUDGE_PASS			 = 'DBパスワード';
$DB_JUDGE_NAME			 = 'DB名';

//メール関連
$MAIL_ERR_MSG			 = array();
$MAIL_CHAR_CODE			 = 'ISO-2022-JP';
$FROM_ADMIN_NAME		 = "";
$FROM_ADMIN_MAIL		 = "a";
$LIST_MAIL				 = array('');
$SUBJECT				 = "To：";
$MAIL_BODY				 = null;

//mecab関連
$MECAB_DIC_PATH			 = "/usr/local/mecab/lib/mecab/dic/ipadic";
$MECAB_DIC_CMD			 = "/usr/local/mecab/libexec/mecab/mecab-dict-index -d ".$MECAB_DIC_PATH." -u ".$MECAB_DIC_PATH."/";
$MECAB_CMD				 = "/usr/local/mecab/bin/mecab -a -d ";
$TOOL_A_FILE_NAME		 = "jwd_suspend_word_a.txt";
$TOOL_B_FILE_NAME		 = "jwd_suspend_word_b.txt";
$TOOL_C_FILE_NAME		 = "jwd_suspend_word_c.txt";
$TOOL_WORD_FILE_NAME	 = "jwd_suspend_word_";
$TOOL_A_DIC_NAME		 = "suspend_word_a_dev.dic";
$TOOL_B_DIC_NAME		 = "suspend_word_b_dev.dic";
$TOOL_C_DIC_NAME		 = "suspend_word_c_dev.dic";
$TOOL_WORD_DIC_NAME		 = "suspend_word_";

$LIST_REPLACEMENT	 = array(
	"/([[:space:]]{0,})/im"			 => "",
	"/(<!--.*?-->)/im"				 => "",//これを一番最初にreplaceしないと無駄にタグを削除してしまう可能性大
	"/(<!doctype.*?>)/im"			 => "",
	"/(<xml.*?>)/im"				 => "",
	"/(<\/?html.*?>)/im"			 => "",
	"/(<\/?head.*?>)/im"			 => "",
	"/(<meta.*?>)/im"				 => "",
	"/(<link.*?>)/im"				 => "",
	"/(<\/?script.*?>)/im"			 => "",
	"/(<\/?body.*?>)/im"			 => "",
	"/(<a.*?>)/im"					 => "",
	"/class=[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}/im"					 => "",
	"/(<\/?div.*?>)/im"				 => "",
	"/(<\/?span.*?>)/im"			 => "",
	"/(<\/?font.*?>)/im"			 => "",
	"/(<\/?p.*?>)/im"				 => "",
	"/(<\/?b.*?>)/im"				 => "",
	"/(<\/?li.*?>)/im"				 => "",
	"/(<\/?ul.*?>)/im"				 => "",
	"/(<\/?ol.*?>)/im"				 => "",
	"/(<\/?h1.*?>)/im"				 => "",
	"/(<\/?h2.*?>)/im"				 => "",
	"/(<\/?h3.*?>)/im"				 => "",
	"/(<\/?table.*?>)/im"			 => "",
	"/(<\/?tr.*?>)/im"				 => "",
	"/(<\/?td.*?>)/im"				 => "",
	"/(<\/?form.*?>)/im"			 => "",
	"/(<input.*?>)/im"				 => "",
	"/(<param.*?>)/im"				 => "",
	"/(<i?frame.*?>)/im"			 => "",
/*
	"/(<\/?a.*?>)/im"				 => "",
	"/(<title.*?>)/im"				 => "",
	"/(<strong.*?>)/im"				 => "",
	"/(<select.*?>)/im"				 => "",
	"/(&nbsp;)/im"					 => "",
*/
);

$LIST_SECOUND_URL_REPLACEMENT	 = array(
	"/([[:space:]]{0,})/im"			 => "",
	"/(<!--.*?-->)/im"				 => "",//これを一番最初にreplaceしないと無駄にタグを削除してしまう可能性大
	"/(<!doctype.*?>)/im"			 => "",
	"/(<xml.*?>)/im"				 => "",
	"/(<\/?html.*?>)/im"			 => "",
	"/(<\/?head.*?>)/im"			 => "",
	"/(<\/?body.*?>)/im"			 => "",
	"/class=[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}/im"					 => "",
	"/(<\/?div.*?>)/im"				 => "",
	"/(<\/?span.*?>)/im"			 => "",
	"/(<\/?font.*?>)/im"			 => "",
	"/(<\/?p.*?>)/im"				 => "",
	"/(<\/?b.*?>)/im"				 => "",
	"/(<\/?ul.*?>)/im"				 => "",
	"/(<\/?ol.*?>)/im"				 => "",
	"/(<\/?h1.*?>)/im"				 => "",
	"/(<\/?h2.*?>)/im"				 => "",
	"/(<\/?h3.*?>)/im"				 => "",
	"/(<\/?table.*?>)/im"			 => "",
	"/(<\/?tr.*?>)/im"				 => "",
	"/(<\/?td.*?>)/im"				 => "",
	"/(<\/?form.*?>)/im"			 => "",
	"/(<input.*?>)/im"				 => "",
	"/(<param.*?>)/im"				 => "",
);

//SQL関連

$INSERT_BADWORDS_SQL =<<<__END_SQL__
insert ignore into badwords(id,url) values(
__END_SQL__;

$INSERT_IMGURL_SQL =<<<__END_SQL__
insert ignore into badwords(id,url) values 
__END_SQL__;

$INSERT_URL_SQL =<<<__END_SQL__
insert ignore into badwords(id,kw_id,url,kw,pid,checked,code,memo) values 
__END_SQL__;

$INSERT_START_TOOL_SQL =<<<__END_SQL__
insert ignore into active_id (regdate) values(current_timestamp);
__END_SQL__;

$SELECT_SEARCH_SQL =<<<__END_SQL__
select * from urllist where 
__END_SQL__;

$SELECT_CACHE_SQL =<<<__END_SQL__
select * from cache_urllist where 
__END_SQL__;

$SELECT_CACHE_COUNT_SQL =<<<__END_SQL__
select exist_url,max_level,regdate from cache_urlinfo where 
__END_SQL__;

$INSERT_TMP_URL =<<<__END_SQL__
insert ignore into urllist (id,url,level) values 
__END_SQL__;

$INSERT_CACHE_URLLIST_URL =<<<__END_SQL__
insert ignore into cache_urllist (exist_url,url,level,domain_type,regdate) values 
__END_SQL__;

$DELETE_OLD_LIST_URL_SQL =<<<__END_SQL__
delete from cache_urllist where 
__END_SQL__;

$UPDATE_END_LIST_URL_SQL =<<<__END_SQL__
update cache_urllist set status = 'e' 
__END_SQL__;

$UPDATE_SEARCHED_URL_SQL =<<<__END_SQL__
update urllist set searched = 'e' where 
__END_SQL__;

$INSERT_CACHE_URL_SQL =<<<__END_SQL__
insert ignore into urllist (id,url,level) values 
__END_SQL__;

$INSERT_CACHE_INFO_SQL =<<<__END_SQL__
insert ignore into cache_urlinfo (exist_url,max_level,regdate) values (
__END_SQL__;

$SELECT_MAX_LEVEL_SQL =<<<__END_SQL__
select level from cache_urllist where 
__END_SQL__;

$UPDATE_CACHE_INFO_SQL =<<<__END_SQL__
update cache_urlinfo set max_level = 
__END_SQL__;

$UPDATE_CACHE_URL_SQL =<<<__END_SQL__
update cache_urllist set status = null where 
__END_SQL__;

$UPDATE_CACHE_URLLIST_LEVEL_SQL =<<<__END_SQL__
update cache_urllist set level = 0 
__END_SQL__;

$UPDATE_URLLIST_LEVEL_SQL =<<<__END_SQL__
update urllist set level = 0 
__END_SQL__;

$DLETE_FRAME_URL_SQL =<<<__END_SQL__
delete from urllist where 
__END_SQL__;

	function setInsertCacheInfoDb($exist_url,$level){
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		global $SELECT_CACHE_SQL;
		global $INSERT_CACHE_INFO_SQL;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$sql = $INSERT_CACHE_INFO_SQL."'".$exist_url."',".$level.",CURRENT_TIMESTAMP);";
		$result_id = QueryDb($sql,$con);
	}

	function resetCacheUrl($exist_url){
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		global $UPDATE_CACHE_URL_SQL;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$sql = $UPDATE_CACHE_URL_SQL."exist_url = '".$exist_url."';";
		$result_id = QueryDb($sql,$con);
	}

	function updateCacheMaxLevel($exist_url){
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		global $SELECT_MAX_LEVEL_SQL;
		global $SELECT_CACHE_COUNT_SQL;
		global $UPDATE_CACHE_INFO_SQL;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		//urllistテーブル
		$sql = $SELECT_MAX_LEVEL_SQL."exist_url = '".$exist_url."' and status = 'e' order by level desc limit 1;";
		$listData = getDbArray($sql,'str',$con);
		//指定階層までのURLリストのコンプリートキャッシュがあるとき
		if( $listData[0]['level'] != '' ){
			$sql = $SELECT_CACHE_COUNT_SQL."exist_url = '".$exist_url."' order by max_level desc limit 1;";
			$listData1 = getDbArray($sql,'str',$con);
			if( $listData1[0]['max_level'] != '' ){
				$max_level = (int)$listData[0]['level'] - (int)$listData1[0]['max_level'];
				if( $max_level > 0 ){
					$sql = $UPDATE_CACHE_INFO_SQL.$listData[0]['level']." where exist_url = '".$exist_url."';";
					$result_id = QueryDb($sql,$con);
				}
			//キャッシュ情報テーブルにないとき
			}else{
				setInsertCacheInfoDb($exist_url,$listData[0]['level']);
			}
		}
	}

	function setHostPath(&$url){
		global $PARAM;
		global $HOST;
		global $PATH;

		$listParam1	 = array();
		$listParam2	 = array();
		$listCount	 = 0;
		$err_flg	 = false;

		$err_flg = preg_match("/(.+?:\/\/)(.+)/",$url,$listParam1);
		$PARAM = $listParam1[1];
		$err_flg = preg_match("/(.+?\/)(.*)/",$listParam1[2],$listParam2);
		$listCount = count($listParam2);
		if( $listCount > 0 ){
			$HOST = preg_replace("/\//","",$listParam2[1]);
			$listParam = split("/",$listParam2[2]);
			$PATH = $listParam2[2];
		}else{
			$HOST = preg_replace("/\//","",$listParam1[2]);
			$PATH = "";
		}
	}

	function getHost(&$url){
		$PARAM;
		$HOST;
		$PATH;

		$listParam1	 = array();
		$listParam2	 = array();
		$listCount	 = 0;
		$err_flg	 = false;

		$err_flg = preg_match("/(.+?:\/\/)(.+)/",$url,$listParam1);
		$PARAM = $listParam1[1];
		$err_flg = preg_match("/(.+?\/)(.*)/",$listParam1[2],$listParam2);
		$listCount = count($listParam2);
		if( $listCount > 0 ){
			$HOST = preg_replace("/\//","",$listParam2[1]);
			$listParam = split("/",$listParam2[2]);
			$PATH = $listParam2[2];
		}else{
			$HOST = preg_replace("/\//","",$listParam1[2]);
			$PATH = "";
		}
//		$HOST = preg_replace("/^(www\.)/","",$HOST);
		return array($HOST,$PATH);
	}

	function checkSearchDomain($listUrl,$domain){
		$domain = preg_replace("/\\\\/","",$domain);
		$domain = preg_replace("/\./","\\.",$domain);
		foreach($listUrl as $url){
			if( preg_match("/\/\/$domain/",$url) == 1 )
				return null;
		}
		return false;
	}

	function checkOtherDomain($url,$domain){
		//同じドメイン
		if( preg_match("/\/\/$domain/",$url) == 1 )
			return null;
		return false;
	}

	function setEncodeCharCode(&$record,&$char_flg,$url=null){
		global $CHAR_CODE;

		//エンコード文字コード設定
		$tmpCharCode = 'euc-jp';

		//HTMLタグで出力する文字コード
		$code_flg = preg_match("/charset[[:space:]]{0,}[=-][[:space:]]{0,}[\"']{0,}(x-euc-jp|euc-jp|ms_kanji|x-sjis|sjis|utf-8|jis|shift_jis|shift-jis|iso-2022-jp)[\"']{0,}/is",$record,$listCharCode);
		$tmpCharCode = $listCharCode[1];
		if( $code_flg != 1 ){
			$code_flg = preg_match("/<\?xml.*?version[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}\d\.\d[\"']{0,}.*?encoding[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}.*?\?>/is",$record,$listCharCode);
			$tmpCharCode = $listCharCode[1];
		}
		if( $char_flg === false && $code_flg !== false && (int)$code_flg > 0 ){
			$char_flg = true;
			$check_flg2 = preg_match("/utf-8/i",$tmpCharCode);
			if( $check_flg2 !== false && $check_flg2 > 0 )
				$CHAR_CODE = "UTF-8";
			$check_flg3 = preg_match("/euc/i",$tmpCharCode);
			if( $check_flg3 !== false && $check_flg3 > 0 )
				$CHAR_CODE = "EUC-JP";
			$check_flg4 = preg_match("/x-sjis|sjis|shift_jis|shift-jis|ms_kanji/i",$tmpCharCode);
			if( $check_flg4 !== false && $check_flg4 > 0 )
				$CHAR_CODE = "SJIS";
			if( $tmpCharCode == "jis" || $tmpCharCode == "JIS" )
				$CHAR_CODE = "JIS";
			$check_flg5 = preg_match("/iso-2022-jp/i",$tmpCharCode);
			if( $check_flg5 !== false && $check_flg5 > 0 )
				$CHAR_CODE = "ISO-2022-JP";
		}
	}

	function setDepthUrl($in_url,&$domain_type,&$search_host,$exclusion_mode,$depth,&$id,$badwords,&$first_asource){
		global $LIST_FRAME_URL;
		global $LIST_SEC_URL;
		global $MAX_DEPTH;

		$listDepthUrl	 = array();
		$listTmpUrl		 = array();
		$listSaveUrl	 = array();
		$LIST_SEC_URL[]	 = $in_url;
		$listDebugUrl[]	 = $in_url;

		//検地階層で空白または0を指定したとき
		if( $depth == 0 || $depth == '' ){
			$depth = $MAX_DEPTH;
		}
		$exist_url = $in_url;

print "検知先URL：".$exist_url."\n";
		if( $depth >= 2 ){
			//階層
			for($d=1;$d < $depth;$d++){
print $d.":階層目-------------------------------------------------------------------------\n";
				$listTmpUrl		 = $LIST_SEC_URL;
				$LIST_SEC_URL	 = array();
				//N階層目のURLリスト
				foreach($listTmpUrl as $tmp_url){
print $tmp_url."：このURLをチェック\n";

					//フレームありフラグ
					$frame_exist_flg = false;

					//N階層目のURL先のURLリスト
					$check_flg = setSecondUrl(&$tmp_url,&$frame_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords,$d,$first_asource);

					//フレーム先のURLリストを取得
					if( $frame_exist_flg === true ){
print $tmp_url.":フレームあり\n";

						//フレームありフラグを元に戻す
						$frame_exist_flg = false;

						//フレームURLリストをユニークにする
						$LIST_FRAME_URL		 = array_unique($LIST_FRAME_URL);

						//フレームのURLがあるとき
						if( $LIST_FRAME_URL[0] != '' ){
							setListFrameUrl($LIST_FRAME_URL,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords,$d,$first_asource,'frame',$exist_url);
							$limit_flg = setUniqueListUrl(&$LIST_FRAME_URL,$exist_url,$id,$domain_type);

							setUniqueUrl(&$LIST_FRAME_URL,$id,$d,$domain_type,'db',$exist_url,$exclusion_mode,'frame');		//重複URLを削除
							$LIST_FRAME_URL		 = array();
						}
					}
				}
				$limit_flg = setUniqueListUrl(&$LIST_SEC_URL,$exist_url,$id,$domain_type);
				//初期化
				setInitListDepthUrl(&$listTmpUrl,&$listTmpSecUrl,&$LIST_SEC_URL,&$listSaveUrl,&$listDepthUrl,$id,$d,$domain_type,$exist_url,$exclusion_mode);
			}
		}
		$LIST_SEC_URL		 = array();
		setUniqueUrl(&$listDepthUrl,'','','','','',$exclusion_mode);			//重複URLを削除
		foreach($listDepthUrl as $url){
			$LIST_SEC_URL[] = $url;
		}

		$listDepthUrl	 = array();
		$LIST_SEC_URL	 = array_unique($LIST_SEC_URL);
		//URL取得完了フラグを立てる
		setUpdateEndListUrlDb($exist_url);
		//検知先ＵＲＬを０にする
		setUpdateFirstLevelUrlDb($id,$exist_url);
		//階層がキャッシュ情報テーブルより深い場合は更新
		updateCacheMaxLevel($exist_url);

		//DBから収集したURLを取得(１階層からソートさせるため)
//		setListSearchUrl($id);
		$LIST_SEC_URL = array();
		$url_exist_flg = setListExistSearchUrl($exist_url,$id,$domain_type,$depth);
	}

	function setListFrameUrl($listFrameTmpUrl,&$domain_type,&$search_host,$exclusion_mode = null,&$id,$badwords,$level = null,$first_asource = null,$frame_flg = null,$exist_url){
		global $LIST_FRAME_URL;
		global $LIST_SEC_URL;

		$listTmpUrl			 = array();
		$listUrl			 = $LIST_SEC_URL;
		$LIST_FRAME_URL		 = array();

		foreach($listFrameTmpUrl as $findex => $url ){

print $url.":<>フレームのURLを１つ取り出す\n";

			$frame_exist_flg = false;
			$check_flg = setSecondUrl(&$url,&$frame_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords,$level,$first_asource,'frame');

			//DBに登録されていないフレームURLリストのみにする
			$limit_flg = setUniqueListUrl(&$LIST_FRAME_URL,$exist_url,$id,$domain_type);
			if( !is_null($limit_flg) )
				return false;

			setUniqueUrl(&$LIST_FRAME_URL,$id,$level,$domain_type,'db',$exist_url,$exclusion_mode,'frame');
			setUniqueUrl(&$LIST_SEC_URL,$id,$level,$domain_type,'db',$exist_url,$exclusion_mode,'frame');

			//いままで取得したURLを除外して新しく取得したURLのみにする
			foreach($LIST_SEC_URL as $url){
				//この階層で取得したURLの中にいままで取得したURLがあれば除外する
				if( in_array($url, $listUrl) === true )
					continue;
print $url."\n";
				$listUrl[] = $url;
				$listTmpUrl[] = $url;
			}
/*
			if( $listTmpUrl[0] != '' ){
				foreach($listTmpUrl as $next_url){
					//検知対象ではないが次のURLの中にフレームタグがあるかチェック
					$frame_exist_flg = checkExistFrame(&$next_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
				}
			}
			if( $frame_exist_flg === true ){
print $url.":<><>frame\n";


				//DBに登録されていないフレームURLリストのみにする
//				$limit_flg = setUniqueListUrl(&$LIST_FRAME_URL,$exist_url,$id,$domain_type);
//				if( !is_null($limit_flg) )
//					return false;

				$frame_exist_flg = false;
				//フレームURLリストが存在するとき
				if( $LIST_FRAME_URL[0] != '' ){
					setListFrameUrl($LIST_FRAME_URL,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords,$level,$first_asource,$frame_flg,$exist_url);
				}
				$LIST_FRAME_URL		 = array();
			}
*/
		}
	}

	function setDelFrameUrlDB($id,$url,$exist_url){
		global $DELETE_OLD_LIST_URL_SQL;
		global $DLETE_FRAME_URL_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$sql = $DELETE_OLD_LIST_URL_SQL."exist_url = '".$exist_url."' and url = '".$url."';";
		$result_id = QueryDb($sql,$con);
		$sql = $DLETE_FRAME_URL_SQL."id = ".$id." and url = '".$url."';";
		$result_id = QueryDb($sql,$con);
	}

	function setUpdateEndListUrlDb($exist_url){
		global $UPDATE_END_LIST_URL_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$sql = $UPDATE_END_LIST_URL_SQL."where exist_url = '".$exist_url."';";
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$result_id = QueryDb($sql,$con);
	}

	function setUpdateFirstLevelUrlDb($id,$exist_url){
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		global $UPDATE_CACHE_URLLIST_LEVEL_SQL;
		global $UPDATE_URLLIST_LEVEL_SQL;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);

		$sql = $UPDATE_CACHE_URLLIST_LEVEL_SQL."where url = '".$exist_url."';";
		$result_id = QueryDb($sql,$con);

		$sql = $UPDATE_URLLIST_LEVEL_SQL."where id = ".$id." and url = '".$exist_url."';";
		$result_id = QueryDb($sql,$con);
	}

	function setUniqueListUrl(&$listUrl,$exist_url,$id,$domain_type){
		global $SELECT_CACHE_SQL;
		global $SELECT_SEARCH_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;

		$listUrl = array_unique($listUrl);
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		//別ドメインを検知する
		if( $domain_type == 'a' )
			$sql = $SELECT_SEARCH_SQL."id = ".$id;
		//別ドメインを検知しない
		if( $domain_type == 'b' )
			$sql = $SELECT_CACHE_SQL."exist_url = '".$exist_url."';";
		$listData = getDbArray($sql,'str',$con);
		if( $listData[0] != '' ){
			$listDbUrl = array();
			$limitCount = 0;
			foreach($listData as $record){
				$limitCount++;
				$listDbUrl[] = $record['url'];
			}
			$listDiffUrl = array();
			$listDiffUrl = array_diff($listUrl,$listDbUrl);
			$listUrl = array();
			$listUrl = $listDiffUrl;
		}
		return null;
	}

	function setInitListDepthUrl(&$listTmpUrl,&$listTmpSecUrl,&$LIST_SEC_URL,&$listSaveUrl,&$listDepthUrl,$id,$level,$domain_type,$exist_url,$exclusion_mode){
		global $listDebugUrl;

		setUniqueUrl(&$LIST_SEC_URL,$id,$level,$domain_type,'db',$exist_url,$exclusion_mode);		//重複URLを削除
		$listTmpSecUrl	 = array();													//検知先URLのURLリストを一時的な配列に格納
		$listTmpSecUrl	 = $LIST_SEC_URL;											//N階層目のURLリストを一時的な配列に格納
		foreach($listTmpUrl as $url)
			$listSaveUrl[]	 = $url;												//いままでチェックしたURLリストを保存
		$listTmpUrl		 = array();													//１つ前のURLリストを初期化
		setUniqueUrl(&$listSaveUrl,$id,$level,$domain_type,'db',$exist_url,$exclusion_mode);		//重複URLを削除
print "_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/\n";
print "Total Count:".count($listSaveUrl);
print "\n";
print "_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/\n";
		//今回取得したURLリストの中にいままでチェックしたURLが含まれているのかチェック
		$LIST_SEC_URL		 = array();												//N階層目のURLリストを初期化
		foreach($listTmpSecUrl as $url){
			//この階層で取得したURLの中にいままで取得したURLがあれば除外する
			if( in_array($url, $listSaveUrl) === true )
				continue;
			$LIST_SEC_URL[] = $url;
		}
		foreach($LIST_SEC_URL as $url){
			$listDepthUrl[] = $url;
			$listDebugUrl[$d][] = $url;
		}
	}

	//setInitListDepthUrlのバックアップ
	function setInitListDepthUrl2(&$listTmpUrl,&$listTmpSecUrl,&$LIST_SEC_URL,&$listSaveUrl,&$listDepthUrl,$id,$level,$domain_type,$exist_url){
		global $listDebugUrl;

		setUniqueUrl(&$LIST_SEC_URL,$id,$level,$domain_type,'db',$exist_url,$exclusion_mode);			//重複URLを削除
		$listTmpSecUrl	 = array();							//検知先URLのURLリストを一時的な配列に格納
		$listTmpSecUrl	 = $LIST_SEC_URL;					//N階層目のURLリストを一時的な配列に格納
		foreach($listTmpUrl as $url)
			$listSaveUrl[]	 = $url;						//１つ前のURLリストを保存
		$listTmpUrl		 = array();							//１つ前のURLリストを初期化
		setUniqueUrl(&$listSaveUrl,$id,$level,$domain_type,'db',$exist_url,$exclusion_mode);		//重複URLを削除
		//今回取得したURLリストの中に１つ前のURLが含まれているのかチェック
		$LIST_SEC_URL		 = array();							//N階層目のURLリストを初期化
		foreach($listTmpSecUrl as $url){
			//この階層で取得したURLの中に１つ前のURLがあれば除外する
			if( in_array($url, $listSaveUrl) === true )
				continue;
			$LIST_SEC_URL[] = $url;
		}
		foreach($LIST_SEC_URL as $url){
			$listDepthUrl[] = $url;
			$listDebugUrl[$d][] = $url;
		}
	}

	function checkRedirectUrl($url,&$domain_type,&$search_host,&$exclusion_mode,&$id,&$first_asoruce){
		global $CHAR_CODE;
		global $FP;
		$char_flg	 = false;

		setHostPath(&$url);
print $url."\n";
		$err_flg = curlConnect(&$url);
		if( $err_flg !== false ){
			$first_asoruce	 = curl_exec($FP);

			$enc = mb_detect_encoding($first_asoruce,"UTF-8, JIS, EUC-JP,eucjp-win, SJIS,sjis-win");
			$first_asoruce = mb_convert_encoding($first_asoruce,"UTF-8",$enc);

			if( !$first_asoruce ){
				return 'CURL_CON_ERR';
			}
			$listInfo		 = curl_getinfo($FP);
			$check_flg		 = curlClose(&$url);
			$listCurlData	 = explode("\n",$first_asoruce);
			$locationUrl	 = null;
			$char_flg		 = false;
			foreach($listCurlData as $record){
				//エンコード文字コード設定
				setEncodeCharCode(&$record,&$char_flg);
				$record = mb_convert_encoding($record,"UTF-8",$CHAR_CODE);
				$locationUrl = getCheckLocationUrl(&$record,&$url,&$domain_type,&$search_host,&$exclusion_mode,&$id,&$CHAR_CODE);
				//LocationのURLがあるとき
				if( !is_null($locationUrl) ){
					setHostPath(&$locationUrl);
					$err_flg = curlConnect(&$locationUrl);
					if( $err_flg !== false ){
						$first_asoruce	 = curl_exec($FP);
						$first_asoruce = mb_convert_encoding($first_asoruce,"UTF-8",$CHAR_CODE);
						$re_check_flg	 = curlClose(&$locationUrl);
					}
					break;
				}
			}
			//更にリダイレクトしていれば辿る
			if( preg_match("/^3/",$listInfo['http_code']) == 1 ){
				$re_location_url = checkRedirectUrl($locationUrl,&$domain_type,&$search_host,&$exclusion_mode,&$id,&$first_asoruce);
				if( !is_null($re_location_url) ){
					setHostPath(&$re_location_url);
					$err_flg = curlConnect(&$re_location_url);
					if( $err_flg !== false ){
						$first_asoruce	 = curl_exec($FP);
						$enc = mb_detect_encoding($first_asoruce,"UTF-8, JIS, eucjp-win, sjis-win");
						$first_asoruce = mb_convert_encoding($first_asoruce,"UTF-8",$enc);
						$re_check_flg	 = curlClose(&$re_location_url);
					}
					return $re_location_url;
				}
				return $locationUrl;
			}
			if( !is_null($locationUrl) ){
				return $locationUrl;
			}
		}else{
			print "redirect connect error";
			return 'CURL_CON_ERR';
		}
		return null;
	}

	function getCheckLocationUrl(&$content,&$old_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,&$CHAR_CODE){
		global $LIST_SEC_URL;
		global $HOST;
		global $PARAM;
		if( preg_match("/^Location[[:space:]]{0,}:[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}/is",$content,$matchs) == 1 ){
			$location_url = trim($matchs[1]);
			if( preg_match("/https?:\/\//i",$location_url) != 1 ){
				//最後に/が付いているとき
				if( preg_match("/\/$/",$old_url) == 1 ){
					$location_url = $old_url.trim($matchs[1]);
				//最後に/が付いていないとき
				}else{
					//URLを解析し設定する
					setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,0);
					//metaタグで取得したURLが$LIST_SEC_URLに追加されているので削除 add 2009/10/20
					$lastindex = max(array_keys($LIST_SEC_URL));
					$location_url = $LIST_SEC_URL[$lastindex];
					unset($LIST_SEC_URL[$lastindex]);
				}
			}
			return $location_url;
		}
		if( mb_eregi("<meta.*?http-equiv[[:space:]]{0,}=.*?",$content) == 1 ){
			if( mb_eregi("Refresh",$content) == 1 ){
				$match_flg = mb_eregi("URL[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}",$content,$matchs);
				if( $match_flg != false ){
					$matchs[1] = trim($matchs[1]);
					//URLを解析し設定する
					setAnalysisUrl(&$matchs[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,0);
					//metaタグで取得したURLが$LIST_SEC_URLに追加されているので削除 add 2009/10/20
					$lastindex = max(array_keys($LIST_SEC_URL));
					$location_url = $LIST_SEC_URL[$lastindex];
					unset($LIST_SEC_URL[$lastindex]);
					return $location_url;
				}
			}
		}
		return null;
	}

	function checkExistFrame(&$url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level){
		global $FP;
		global $HOST;
		global $PATH;
		global $CHAR_CODE;
		$HOST = null;
		$PATH = null;
		setHostPath(&$url);
		$err_flg = curlConnect(&$url);
		if( $err_flg !== false ){
			$char_flg				 = false;
			$frame_exist_flg		 = false;
			$frame_content_exist_flg = false;
			$htmlSource				 = curl_exec($FP);
			$check_flg				 = curlClose(&$url);
			$listCurlData			 = explode("\n",$htmlSource);
			foreach($listCurlData as $record){
				//エンコード文字コード設定
				setEncodeCharCode(&$record,&$char_flg);
				$record = mb_convert_encoding($record,"UTF-8",$CHAR_CODE);

				if( mb_eregi("<!--",$record) == 1 )
					$coment_flg = true;
				if( mb_eregi("-->",$record) == 1 )
					$coment_flg = false;
				if( $coment_flg === true )
					continue;

				$frame_flg = setFrameTagUrl(&$record,&$url,&$frame_content_exist_flg,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level,2);
				if( $frame_flg === true ){
					return false;
				}
			}
			return null;
		}
		return null;
	}

	function setSecondUrl(&$url,&$frame_exist_flg,&$domain_type,&$search_host,$exclusion_mode = null,&$id,$badwords,$level = null,$first_asource = null,$frame_flg = null){
		global $FP;
		global $CHAR_CODE;
		global $CHECK_ERR_FLG;
		global $LIST_CON_ERR_URL;
		global $LIST_CLOSE_ERR_URL;
		global $MAIL_BODY;
		global $MAIL_ERR_MSG;
		global $LIST_MAIL;
		global $SUBJECT;
		global $MAIL_BODY;
		global $LIST_SEC_URL;
		global $FROM_ADMIN_MAIL;
		global $LIST_JS_URL;
		global $LIST_LINK_URL;
		global $PATH;

		//画像のみ
		if( $exclusion_mode == 'img' ){
			$pettern1	 = "/<img.*?src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern2	 = "/<a.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>(.*?)<\/(.+?)/is";
			$pettern3	 = "/<script.*?src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern4	 = "/<area.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern5	 = "/<link.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern6	 = "/@import.*?u?r?l?\(?[\"']{0,}(.*?[^\"';\)]{0,}.*?[^\"';\)]{0,}.*?)[\"']{0,}\)?;?/is";
			$pettern7	 = "/background\-image[[:space:]]{0,}:[[:space:]]{0,}url\([\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}\);?/is";
			$pettern8	 = "/background[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}/is";
			$pettern9	 = "/background[[:space:]]{0,}:[[:space:]]{0,}url\([\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}\)/is";
		//画像＆テキスト
		}else{
			$pettern1	 = "/<img.*?src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern2	 = "/<a.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>(.*?)<\/(.+?)/is";
			$pettern3	 = "/<script.*?src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern4	 = "/<area.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern5	 = "/<link.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
			$pettern6	 = "/@import.*?u?r?l?\(?[\"']{0,}(.*?[^\"';\)]{0,}.*?[^\"';\)]{0,}.*?)[\"']{0,}\)?;?/is";
			$pettern7	 = "/background\-image[[:space:]]{0,}:[[:space:]]{0,}url\([\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}\);?/is";
			$pettern8	 = "/background[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}/is";
			$pettern9	 = "/background[[:space:]]{0,}:[[:space:]]{0,}url\([\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}\)/is";
		}

		$matches	 = array();
		$listMatch	 = array();
		$html		 = null;
		$tmp		 = null;
		$char_flg	 = false;

		$HOST = null;
		$PATH = null;
		setHostPath(&$url);

		//フレームタグ以外で２階層目以降
		if( $level != 1 || !is_null($frame_flg) ){
			$err_flg = curlConnect(&$url);
		}
		if( $err_flg !== false || $level == 1 ){
			$start						 = _MicrotimeFloat();
			$meta_locationUrl			 = null;
			$meta_close_check_flg		 = true;
			$coment_close_check_flg		 = true;
			$coment_flg					 = false;
			$select_close_check_flg		 = true;
			$frame_content_exist_flg	 = false;
			$javascript_close_check_flg	 = true;
			$content_length				 = 0;
			if( $level == 1 && is_null($frame_flg) ){
				$htmlSource		 = $first_asource;
			}else{
				$htmlSource		 = curl_exec($FP);
				$check_flg		 = curlClose(&$url);
			}
			$listCurlData	 = explode("\n",$htmlSource);
			foreach($listCurlData as $record){
				//エンコード文字コード設定
				setEncodeCharCode(&$record,&$char_flg);
				$record = mb_convert_encoding($record,"UTF-8",$CHAR_CODE);

				if( mb_eregi("<!--",$record) == 1 )
					$coment_flg = true;
				if( mb_eregi("-->",$record) == 1 )
					$coment_flg = false;
				if( $coment_flg === true )
					continue;

				$frame_flg = setFrameTagUrl(&$record,&$url,&$frame_content_exist_flg,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level,2);

				//フレームタグだったら
				if( $frame_flg === true ){
print "フレームURLがあった ==================================\n";
					$frame_exist_flg = true;
					continue;
				}

				//beseのURLがあれば取得する
				setBaseUrl(&$record);

				//コンテンツの長さを取得
				setContentLength(&$content_length,$record);
				$ratio_flg = checkContentRatio($content_length,$record);
				if( is_null($ratio_flg) ){
					//metaタグを除外する
					$meta_check_flg = regMetaLocateUrl(&$record,&$locationUrl,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
					if( !$meta_check_flg )
						continue;
				}
				$html .= $record;
			}

			setJavaScriptTagUrl2(&$record,&$url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
			//不要なタグを削除
			delSecoundUrlExclusionWord(&$html);

			//第二階層のリンクを収集
			//画像＆テキスト
			if( $exclusion_mode == 'move' ||  $exclusion_mode == 'both' || $exclusion_mode == 'img' ){
				//imgタグにマッチ
				$hit_count1 = preg_match_all($pettern1,$html,$matches);
				if( $hit_count1 > 0 ){
					if( is_array($matches[1]) ){
						$listMatch = array_unique($matches[1]);
						if( count($listMatch) > 0 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							foreach($listMatch as $match_record){
								$ng_flg = preg_match("/javascript/i",$match_record);
								if( $ng_flg != 1 ){
									$match_record = mb_ereg_replace("&amp;","&",$match_record);
									setAnalysisUrl(&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
								}
							}
						}
					}else{
						$ng_flg = preg_match("/javascript/i",$matches[1]);
						if( $ng_flg != 1 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
							setAnalysisUrl(&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
						}
					}
				}
				//background-imageにマッチ
				$hit_count7 = preg_match_all($pettern7,$html,$matches);
				if( $hit_count7 > 0 ){
					if( is_array($matches[1]) ){
						$listMatch = array_unique($matches[1]);
						if( count($listMatch) > 0 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							foreach($listMatch as $match_record){
								$ng_flg = preg_match("/javascript/i",$match_record);
								if( $ng_flg != 1 ){
									$match_record = mb_ereg_replace("&amp;","&",$match_record);
									setAnalysisUrl(&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level,"debug");
								}
							}
						}
					}else{
						$ng_flg = preg_match("/javascript/i",$matches[1]);
						if( $ng_flg != 1 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
							setAnalysisUrl(&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
						}
					}
				}
				//backgroundにマッチ
				$hit_count8 = preg_match_all($pettern8,$html,$matches);
				if( $hit_count8 > 0 ){
					if( is_array($matches[1]) ){
						$listMatch = array_unique($matches[1]);
						if( count($listMatch) > 0 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							foreach($listMatch as $match_record){
								$ng_flg = preg_match("/javascript/i",$match_record);
								if( $ng_flg != 1 ){
									$match_record = mb_ereg_replace("&amp;","&",$match_record);
									setAnalysisUrl(&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level,"debug");
								}
							}
						}
					}else{
						$ng_flg = preg_match("/javascript/i",$matches[1]);
						if( $ng_flg != 1 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
							setAnalysisUrl(&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
						}
					}
				}
				//background:url()にマッチ
				$hit_count9 = preg_match_all($pettern9,$html,$matches);
				if( $hit_count9 > 0 ){
					if( is_array($matches[1]) ){
						$listMatch = array_unique($matches[1]);
						if( count($listMatch) > 0 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							foreach($listMatch as $match_record){
								$ng_flg = preg_match("/javascript/i",$match_record);
								if( $ng_flg != 1 ){
									$match_record = mb_ereg_replace("&amp;","&",$match_record);
									setAnalysisUrl(&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level,"debug");
								}
							}
						}
					}else{
						$ng_flg = preg_match("/javascript/i",$matches[1]);
						if( $ng_flg != 1 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
							setAnalysisUrl(&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
						}
					}
				}
				//aタグにマッチ
				$hit_count2 = preg_match_all($pettern2,$html,$matches);
				if( $hit_count2 > 0 ){
					if( is_array($matches[1]) ){
						if( count($matches[1]) > 0 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							foreach($matches[1] as $key => $match_record){
								$last_a_tag = trim($matches[2][$key]);
								$last_a_tag = mb_ereg_replace("　","",$last_a_tag);
								$last_a_tag = mb_ereg_replace("\t","",$last_a_tag);
								if( $last_a_tag != '' ){
									$ng_flg = preg_match("/javascript/i",$match_record);
									if( $ng_flg != 1 ){
										$match_record = mb_ereg_replace("&amp;","&",$match_record);
										setAnalysisUrl(&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
									}
								}
							}
						}
					}else{
						$ng_flg = preg_match("/javascript/i",$html);
						if( $ng_flg != 1 ){
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
							setAnalysisUrl(&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
						}
					}
				}
/*
					//scriptタグのsrcにマッチ
					$hit_count3 = preg_match_all($pettern3,$html,$matches);
					if( $hit_count3 > 0 ){
						$listScriptSrcUrl = array();
						if( is_array($matches[1]) ){
							$listMatch = array_unique($matches[1]);
							if( count($listMatch) > 0 ){
								mb_internal_encoding("UTF-8");
								mb_regex_encoding("UTF-8");
								foreach($listMatch as $match_record){
									$index = array_search($match_record, $LIST_JS_URL);
									if( $index === false ){
										$match_record = mb_ereg_replace("&amp;","&",$match_record);
										$LIST_JS_URL[] = $match_record;
print $match_record."\n";
										setScriptAndLinkAnalysisUrl(&$listScriptSrcUrl,&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,'script');
									}
								}
							}
						}else{
print $matches[1]."\n";
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
							setScriptAndLinkAnalysisUrl(&$listScriptSrcUrl,&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,'script');
						}
						if( count($listScriptSrcUrl) > 0 ){
							foreach($listScriptSrcUrl as $tmp_url){
								$LIST_SEC_URL[] = $tmp_url;
								$frame_script_exist_flg = false;
								$check_flg		 = true;
								$check_flg = setSecondUrl(&$tmp_url,&$frame_script_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords);
								//フレーム先のURLリストを取得
								if( $frame_script_exist_flg === true ){
print "script src frame\n";
									$frame_script_exist_flg = false;
									$LIST_SEC_URL	 = array_unique($LIST_SEC_URL);
									$LIST_FRAME_URL = $LIST_SEC_URL;		//フレームのURL
									//フレームのURLがあるとき
									if( count($LIST_FRAME_URL) > 0 ){
										foreach($LIST_FRAME_URL as $url ){
print $url.":script src frame\n";
											$check_flg = setSecondUrl(&$tmp_url,&$frame_script_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords);
										}
									}
								}
							}
						}
					}
*/
					//areaタグにマッチ
					$hit_count4 = preg_match_all($pettern4,$html,$matches);
					if( $hit_count4 > 0 ){
						if( is_array($matches[1]) ){
							$listMatch = array_unique($matches[1]);
							if( count($listMatch) > 0 ){
								mb_internal_encoding("UTF-8");
								mb_regex_encoding("UTF-8");
								foreach($listMatch as $match_record){
									$match_record = mb_ereg_replace("&amp;","&",$match_record);
									setAnalysisUrl(&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
								}
							}
						}else{
							mb_internal_encoding("UTF-8");
							mb_regex_encoding("UTF-8");
							$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
							setAnalysisUrl(&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
						}
					}
/*
					//linkタグのhrefにマッチ
					$hit_count5 = preg_match_all($pettern5,$html,$matches);
					if( $hit_count5 > 0 ){
						$listTmpLinkUrl = array();
						if( is_array($matches[1]) ){
							$listMatch = array_unique($matches[1]);
							if( count($listMatch) > 0 ){
								mb_internal_encoding("UTF-8");
								mb_regex_encoding("UTF-8");
								foreach($listMatch as $match_record){
									if( preg_match("/css/is",$match_record) == 1 ){
										$index = array_search($match_record, $LIST_LINK_URL);
										if( $index === false ){
											$match_record = mb_ereg_replace("&amp;","&",$match_record);
											$LIST_LINK_URL[] = $match_record;
											$PATH_hit_count = preg_match("/(.*?)\/.*?.css/",$match_record,$list_path);
											if( $PATH_hit_count == 1 )
												$PATH = $list_path[1];
											setScriptAndLinkAnalysisUrl(&$listTmpLinkUrl,&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,'link',$PATH);
										}
									}
								}
							}
						}else{
							if( preg_match("/css/is",$matches[1]) == 1 ){
								mb_internal_encoding("UTF-8");
								mb_regex_encoding("UTF-8");
								$matches[1] = mb_ereg_replace("&amp;","&",$matches[1]);
								$PATH_hit_count = preg_match("/(.*?)\/.*?.css/",$matches[1],$list_path);
								if( $PATH_hit_count == 1 )
									$PATH = $list_path[1];
								setScriptAndLinkAnalysisUrl(&$listTmpLinkUrl,&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,'link',$PATH);
							}
						}
						if( count($listTmpLinkUrl) > 0 ){
							foreach($listTmpLinkUrl as $tmp_url){
								$LIST_SEC_URL[] = $tmp_url;
								$frame_link_exist_flg = false;
								$check_flg		 = true;
								$check_flg = setSecondUrl(&$tmp_url,&$frame_link_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords);
								//フレーム先のURLリストを取得
								if( $frame_link_exist_flg === true ){
print "script src frame\n";
									$frame_link_exist_flg = false;
									$LIST_SEC_URL	 = array_unique($LIST_SEC_URL);
									$LIST_FRAME_URL = $LIST_SEC_URL;		//フレームのURL
									//フレームのURLがあるとき
									if( count($LIST_FRAME_URL) > 0 ){
										foreach($LIST_FRAME_URL as $url ){
print $url.":script src frame\n";
											$check_flg = setSecondUrl(&$tmp_url,&$frame_link_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords);
										}
									}
								}
							}
						}
					}
					//@importにマッチ
					$hit_count6 = preg_match_all($pettern6,$html,$matches);
					if( $hit_count6 > 0 ){
						$listTmpLinkUrl = array();
						if( is_array($matches[1]) ){
							$listMatch = array_unique($matches[1]);
							if( count($listMatch) > 0 ){
								mb_internal_encoding("UTF-8");
								mb_regex_encoding("UTF-8");
								foreach($listMatch as $match_record){
									if( preg_match("/css/is",$match_record) == 1 ){
										$index = array_search($match_record, $LIST_LINK_URL);
										if( $index === false ){
											$match_record = mb_ereg_replace("&amp;","&",$match_record);
											$LIST_LINK_URL[] = $match_record;
											setScriptAndLinkAnalysisUrl(&$listTmpLinkUrl,&$match_record,&$domain_type,&$search_host,&$exclusion_mode,&$id,'import',$url);
										}
									}
								}
							}
						}else{
							if( preg_match("/css/is",$matches[1]) == 1 ){
								mb_internal_encoding("UTF-8");
								mb_regex_encoding("UTF-8");
								$matches[1] = mb_ereg_replace("&amp;","&",&$matches[1]);
								setScriptAndLinkAnalysisUrl(&$listTmpLinkUrl,&$matches[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,'import',$url);
							}
						}
						if( count($listTmpLinkUrl) > 0 ){
							foreach($listTmpLinkUrl as $tmp_url){
								$LIST_SEC_URL[] = $tmp_url;
								$frame_link_exist_flg = false;
								$check_flg		 = true;
								$check_flg = setSecondUrl(&$tmp_url,&$frame_link_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords);
								//フレーム先のURLリストを取得
								if( $frame_link_exist_flg === true ){
print "script src frame\n";
									$frame_link_exist_flg = false;
									$LIST_SEC_URL	 = array_unique($LIST_SEC_URL);
									$LIST_FRAME_URL = $LIST_SEC_URL;		//フレームのURL
									//フレームのURLがあるとき
									if( count($LIST_FRAME_URL) > 0 ){
										foreach($LIST_FRAME_URL as $url ){
print $url.":script src frame\n";
											$check_flg = setSecondUrl(&$tmp_url,&$frame_link_exist_flg,&$domain_type,&$search_host,$exclusion_mode,&$id,$badwords);
										}
									}
								}
							}
						}
					}
*/
			}

			//接続エラーがあった場合、メールで報告
			if( $CHECK_ERR_FLG === true ){
				$con_err_url	 = null;
				$close_err_url	 = null;
				if( count($LIST_CON_ERR_URL) > 0 ){
					foreach($LIST_CON_ERR_URL as $err_url)
						$con_err_url .= $err_url."\n";
				}
				if( count($LIST_CLOSE_ERR_URL) > 0 ){
					foreach($LIST_CLOSE_ERR_URL as $err_url)
						$close_err_url .= $err_url."\n";
				}
			}
			if( $check_flg == true )
				return true;
			else
				return false;
		}else{
			return false;
		}
		return true;
	}

	function killToolProcess(){
		$pid = getmypid();
		$err_flg = system("kill -15 ".$pid);
	}

	function setUniqueUrl(&$listUrl,$id = null,$level = null,$domain_type = null,$mode = null,$exist_url = null,$exclusion_mode = null,$frame_flg = null){
		global $INSERT_TMP_URL;
		global $INSERT_CACHE_URLLIST_URL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		//検知対象が画像以外
		if( $exclusion_mode != 'img' ){
			$listTmpUrl = array();
			if( $listUrl != '' ){
				foreach($listUrl as $url){
					$listTmpUrl[] = $url;
					$url = preg_replace("/\/$/","",$url);
					$listTmpUrl[] = $url;
				}
				$listUrl = array();
				$listUrl = array_unique($listTmpUrl);
				if( $mode == 'db'){
					if( count($listUrl) > 0 ){
						$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
						$listSqlParam1 = array();
						$listSqlParam2 = array();
						foreach($listUrl as $url){
							//id,url,level
							$listSqlParam1[] = "(".$id.",'".mysql_real_escape_string($url)."',".$level.")";
							//exist_url,url,level,domain_type,regdate
							$listSqlParam2[] = "('".mysql_real_escape_string($exist_url)."','".mysql_real_escape_string($url)."',".$level.",'".$domain_type."',CURRENT_TIMESTAMP)";
						}
						$sql = $INSERT_TMP_URL.implode(",",$listSqlParam1).";";
						QueryDb($sql,$con);
						$sql = $INSERT_CACHE_URLLIST_URL.implode(",",$listSqlParam2).";";
						QueryDb($sql,$con);
					}
				}
			}
		}
	}

	function setAnalysisUrl(&$record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level = null,$check_flg = 1){
		global $HOST;
		global $LIST_SEC_URL;
		global $EXIST_URL;
		global $BASE_URL;
		global $BASE_PARAM;
		global $BASE_HOST;
		global $BASE_PATH;
		global $PARAM;
		global $TARGET_URL;
		global $LIST_FRAME_URL;
		global $PATH;
		$search_host = preg_replace("/\./","\\.",$HOST);
		$exist_flg	 = preg_match("/^https?:\/\/$search_host/",$record);
		$http_flg	 = preg_match("/^https?:\/\/.*/",$record);
		if( $http_flg == 1 || $exist_flg == 1 ){
			$url = trim($record);
			if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
//print "case0:\n";
//print $url."\n";
				$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
				if( $exist_flg === false ){
					insertBadWordsDb(&$url,&$id);
				}else{
					$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
					if( is_null($exist_flg) ){
						insertBadWordsDb(&$url,&$id);
					}else{
						//別ドメインを検地しない
						if( $domain_type == 'b' ){
							$chk_domain_flg = checkOtherDomain($url,$search_host);
							if( is_null($chk_domain_flg) ){
								$LIST_SEC_URL[] = $url;
							}
						//別ドメインを検地する
						}else{
							$LIST_SEC_URL[] = $url;
						}
					}
				}
			}
		}else{
//print "step1:".$record."<br>\n";
			$slash_flg = preg_match("/^\//",$record);
			//絶対パス
			if( $slash_flg == 1 ){
				if( is_null($BASE_URL) ){
//print "case1:\n";
//print $PARAM.$HOST.$record."<br>\n";
					$url = trim($PARAM.$HOST.$record);
					if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
						$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
						if( $exist_flg === false ){
							insertBadWordsDb(&$url,&$id);
						}else{
							$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
							if( is_null($exist_flg) ){
								insertBadWordsDb(&$url,&$id);
							}else{
								//別ドメインを検地しない
								if( $domain_type == 'b' ){
									$chk_domain_flg = checkOtherDomain($url,$search_host);
									if( is_null($chk_domain_flg) ){
										$LIST_SEC_URL[] = $url;
									}
								//別ドメインを検地する
								}else{
									$LIST_SEC_URL[] = $url;
								}
							}
						}
					}
				}else{
					$record = preg_replace('/^\//','',$record);
//print "case2:\n";
//print $BASE_URL.$record."<br>\n";
					$url = trim($BASE_URL.$record);
					if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
						$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
						if( $exist_flg === false ){
							insertBadWordsDb(&$url,&$id);
						}else{
							$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
							if( is_null($exist_flg) ){
								insertBadWordsDb(&$url,&$id);
							}else{
								//別ドメインを検地しない
								if( $domain_type == 'b' ){
									$chk_domain_flg = checkOtherDomain($url,$search_host);
									if( is_null($chk_domain_flg) ){
										$LIST_SEC_URL[] = $url;
									}
								//別ドメインを検地する
								}else{
									$LIST_SEC_URL[] = $url;
								}
							}
						}
					}
				}
			}else{
//print "step2:".$record."<br>\n";
				$sharp_flg = preg_match('/^#/',$record);
				if( $sharp_flg == 1 ){
					if( is_null($BASE_URL) ){
//print $TARGET_URL.$record."<br>\n";
						$url = trim($TARGET_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
							$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$LIST_SEC_URL[] = $url;
										}
									//別ドメインを検地する
									}else{
										$LIST_SEC_URL[] = $url;
									}
								}
							}
						}
//print "case3:\n";
//print $HOST.":".$PARAM.":".$record."<br>\n";
					}else{
//print "case4:\n";
//print $BASE_URL.$record."<br>\n";
						$url = trim($BASE_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
							$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$LIST_SEC_URL[] = $url;
										}
									//別ドメインを検地する
									}else{
										$LIST_SEC_URL[] = $url;
									}
								}
							}
						}
					}
					//第二階層
					if( $check_flg == 2 ){
						$LIST_FRAME_URL = $LIST_SEC_URL;
						$LIST_FRAME_URL = array_unique($LIST_FRAME_URL);
						setUniqueUrl(&$LIST_FRAME_URL,$id,$level,$domain_type,'db',$EXIST_URL,$exclusion_mode);		//重複URLを削除
					}
					$LIST_SEC_URL		 = array_unique($LIST_SEC_URL);
					return ;
				}
				//同じ階層(./index.html)
				if( preg_match('/^\.\//',$record) == 1 ){
//print "url3:".$record."\n";
					if( is_null($BASE_URL) ){
						$record		 = preg_replace('/^(\.\/)/','',$record);
						$PATH		 = getUrlFolderPath($PATH);
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						if( count($listDir) > 0 ){
							foreach($listDir as $dir)
								$listTmpDir[] = $dir;
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
//print "case5:\n";
//print $PARAM.$HOST.'/'.$tmp_path.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						}else{
//print "case6:\n";
//print $PARAM.$HOST.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						}
					}else{
//print "case7:\n";
//print $BASE_URL.$record."<br>\n";
						$url = trim($BASE_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
							$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$LIST_SEC_URL[] = $url;
										}
									//別ドメインを検地する
									}else{
										$LIST_SEC_URL[] = $url;
									}
								}
							}
						}
					}
				//同じ階層(index.html)
				}elseif(preg_match('/^\.\//',$record) != 1 && 
						preg_match('/^(\.\.\/){1,}/',$record) != 1 ){
					if( is_null($BASE_URL) ){
						$listDir	 = explode("/",$PATH);
						if( count($listDir) > 0 ){
							//空白部分があれば削除
							foreach($listDir as $index => $dir){
								if( $dir == '' )
									unset($listDir[$index]);
							}
						}
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						if( count($listDir) > 0 ){
							foreach($listDir as $dir)
								$listTmpDir[] = $dir;
						}
						$tmp_path = implode("/",$listTmpDir);
//print "case8:\n";
							if( $tmp_path == "" ){
//print $PARAM.$HOST.'/'.$record."<br>\n";
								$url = trim($PARAM.$HOST.'/'.$record);
							}else{
//print $PARAM.$HOST.'/'.$tmp_path.'/'.$record."<br>\n";
								$url = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
							}
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
//print "case9:\n";
//print $PARAM.$HOST.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						if( $PATH != '' ){
							$PATH = preg_replace("/^(\/)(.+)/","$2",$PATH);
							$PATH = preg_replace("/(.+)(\/)$/","$1",$PATH);
//print $PARAM.$HOST.'/'.$PATH.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$PATH.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						}
					}else{
//print "case11:\n";
//print $BASE_URL.$record."<br>\n";
						$url = trim($BASE_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
							$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$LIST_SEC_URL[] = $url;
										}
									//別ドメインを検地する
									}else{
										$LIST_SEC_URL[] = $url;
									}
								}
							}
						}
					}
				//別の階層
				}elseif( preg_match('/^(\.\.\/){1,}/',$record) == 1 ){
					$folderCount = 0;
					$folderCount = substr_count ($record,'../');
					$record = preg_replace('/(\.\.\/)/','',$record,-1);
					if( is_null($BASE_URL) ){
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						$breakCount	 = count($listDir) - $folderCount;
						if( $breakCount > 0 ){
							$fcount = 0;
							if( count($listDir) > 0 ){
								foreach($listDir as $dir){
									$fcount++;
									$listTmpDir[] = $dir;
									if( $fcount == $breakCount )
										break;
								}
							}
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
//print "case12:\n";
//print $PARAM.$HOST.'/'.$tmp_path.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						}else{
//print "case13:\n";
//print $PARAM.$HOST.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						}
					}else{
						setBaseUrlPath(&$BASE_URL);
						$listDir	 = explode("/",$BASE_PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						$breakCount	 = count($listDir) - $folderCount;
						if( $breakCount > 0 ){
							$fcount = 0;
							if( count($listDir) > 0 ){
								foreach($listDir as $dir){
									$fcount++;
									$listTmpDir[] = $dir;
									if( $fcount == $breakCount )
										break;
								}
							}
						}
						$BASE_PATH = implode("/",$listTmpDir);
						if( $BASE_PATH != "" ){
//print "case14:".$BASE_PARAM.$BASE_HOST.'/'.$BASE_PATH.'/'.$record."<br>\n";
							$url = trim($BASE_PARAM.$BASE_HOST.'/'.$BASE_PATH.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						}else{
//print "case15:".$BASE_PARAM.$BASE_HOST.'/'.$record."<br>\n";
							$url = trim($BASE_PARAM.$BASE_HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
								$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$LIST_SEC_URL[] = $url;
											}
										//別ドメインを検地する
										}else{
											$LIST_SEC_URL[] = $url;
										}
									}
								}
							}
						}
					}
				}else{
					$url = trim($record);
					if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'text' || $exclusion_mode == 'both' ){
						$exist_flg = checkImgFile(&$url,&$domain_type,&$search_host);
						if( $exist_flg === false ){
							insertBadWordsDb(&$url,&$id);
						}else{
							$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
							if( is_null($exist_flg) ){
								insertBadWordsDb(&$url,&$id);
							}else{
								//別ドメインを検地しない
								if( $domain_type == 'b' ){
									$chk_domain_flg = checkOtherDomain($url,$search_host);
									if( is_null($chk_domain_flg) ){
										$LIST_SEC_URL[] = $url;
									}
								//別ドメインを検地する
								}else{
									$LIST_SEC_URL[] = $url;
								}
							}
						}
					}
//print $record."<br>";
				}
			}
		}
		//第二階層
		if( $check_flg == 2 ){
			$LIST_FRAME_URL = $LIST_SEC_URL;
			$LIST_FRAME_URL = array_unique($LIST_FRAME_URL);
			setUniqueUrl(&$LIST_FRAME_URL,$id,$level,$domain_type,'db',$EXIST_URL,$exclusion_mode);		//重複URLを削除
		}
		$LIST_SEC_URL		 = array_unique($LIST_SEC_URL);
	}

	function setScriptAndLinkAnalysisUrl(&$listScriptLinkUrl,&$record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$mode = null,$css_url = null,$check_flg = 1){
		global $HOST;
		global $BASE_URL;
		global $BASE_PARAM;
		global $BASE_HOST;
		global $BASE_PATH;
		global $PARAM;
		global $TARGET_URL;
		global $LIST_FRAME_URL;
		global $PATH;
		if( $mode == 'link' ){
			$PATH = $css_url;
		}

		$search_host = preg_replace("/\./","\\.",$HOST);
		$exist_flg	 = preg_match("/^https?:\/\/$search_host/",$record);
		$http_flg	 = preg_match("/^https?:\/\/.*/",$record);
		if( $http_flg == 1 || $exist_flg == 1 ){
			$url = trim($record);
			if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
				$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
				if( $exist_flg === false ){
					insertBadWordsDb(&$url,&$id);
				}else{
					$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
					if( is_null($exist_flg) ){
						insertBadWordsDb(&$url,&$id);
					}else{
						//別ドメインを検地しない
						if( $domain_type == 'b' ){
							$chk_domain_flg = checkOtherDomain($url,$search_host);
							if( is_null($chk_domain_flg) ){
								$listScriptLinkUrl[] = $url;
							}
						//別ドメインを検地する
						}else{
							$listScriptLinkUrl[] = $url;
						}
					}
				}
			}
		}else{
//print "step1:".$record."<br>";
			$slash_flg = preg_match("/^\//",$record);
			//絶対パス
			if( $slash_flg == 1 ){
				if( is_null($BASE_URL) ){
//print "case1:";
//print $PARAM.$HOST.$record."<br>\n";
					$url = trim($PARAM.$HOST.$record);
					if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
						$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
						if( $exist_flg === false ){
							insertBadWordsDb(&$url,&$id);
						}else{
							$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
							if( is_null($exist_flg) ){
								insertBadWordsDb(&$url,&$id);
							}else{
								//別ドメインを検地しない
								if( $domain_type == 'b' ){
									$chk_domain_flg = checkOtherDomain($url,$search_host);
									if( is_null($chk_domain_flg) ){
										$listScriptLinkUrl[] = $url;
									}
								//別ドメインを検地する
								}else{
									$listScriptLinkUrl[] = $url;
								}
							}
						}
					}
				}else{
					$record = preg_replace('/^\//','',$record);
//print "case2:";
//print $BASE_URL.$record."<br>\n";
					$url = trim($BASE_URL.$record);
					if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
						$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
						if( $exist_flg === false ){
							insertBadWordsDb(&$url,&$id);
						}else{
							$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
							if( is_null($exist_flg) ){
								insertBadWordsDb(&$url,&$id);
							}else{
								//別ドメインを検地しない
								if( $domain_type == 'b' ){
									$chk_domain_flg = checkOtherDomain($url,$search_host);
									if( is_null($chk_domain_flg) ){
										$listScriptLinkUrl[] = $url;
									}
								//別ドメインを検地する
								}else{
									$listScriptLinkUrl[] = $url;
								}
							}
						}
					}
				}
			}else{
//print "step2:".$record."<br>\n";
				$sharp_flg = preg_match('/^#/',$record);
				if( $sharp_flg == 1 ){
					if( is_null($BASE_URL) ){
//print $TARGET_URL.$record."<br>\n";
						$url = trim($TARGET_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
							$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$listScriptLinkUrl[] = $url;
										}
									//別ドメインを検地する
									}else{
										$listScriptLinkUrl[] = $url;
									}
								}
							}
						}
//print "case3:";
//print $HOST.":".$PARAM.":".$record."<br>\n";
					}else{
//print "case4:";
//print $BASE_URL.$record."<br>\n";
						$url = trim($BASE_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
							$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$listScriptLinkUrl[] = $url;
										}
									//別ドメインを検地する
									}else{
										$listScriptLinkUrl[] = $url;
									}
								}
							}
						}
					}
					//第二階層
					if( $check_flg == 2 ){
						$LIST_FRAME_URL = $listScriptLinkUrl;
						$LIST_FRAME_URL = array_unique($LIST_FRAME_URL);
						setUniqueUrl(&$LIST_FRAME_URL,$id,'',$domain_type,'db','',$exclusion_mode);		//重複URLを削除
					}
					return ;
				}
				//同じ階層(./index.html)
				if( preg_match('/^\.\//',$record) == 1 ){
//print "url3:".$record."\n";
					if( is_null($BASE_URL) ){
						$record		 = preg_replace('/^(\.\/)/','',$record);
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						if( count($listDir) > 0 ){
							foreach($listDir as $dir)
								$listTmpDir[] = $dir;
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
//print "case5:";
//print $PARAM.$HOST.'/'.$tmp_path.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}else{
//print "case6:";
//print $PARAM.$HOST.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}
					}else{
//print "case7:";
//print $BASE_URL.$record."<br>\n";
						$url = trim($BASE_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
							$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$listScriptLinkUrl[] = $url;
										}
									//別ドメインを検地する
									}else{
										$listScriptLinkUrl[] = $url;
									}
								}
							}
						}
					}
				//同じ階層(index.html)
				}elseif(preg_match('/^\.\//',$record) != 1 && 
						preg_match('/^(\.\.\/){1,}/',$record) != 1 ){
					if( is_null($BASE_URL) ){
						if( $mode == 'import' ){
							list($tdomain,$PATH)=getHost(&$css_url);
						}
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						if( count($listDir) > 0 ){
							foreach($listDir as $dir)
								$listTmpDir[] = $dir;
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
//print "case8:\n";
//print $PARAM.$HOST.'/'.$tmp_path.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}else{
//print "case9:";
//print $PARAM.$HOST.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}
					}else{
//print "case10:";
//print $BASE_URL.$record."<br>\n";
						$url = trim($BASE_URL.$record);
						if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
							$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
							if( $exist_flg === false ){
								insertBadWordsDb(&$url,&$id);
							}else{
								$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
								if( is_null($exist_flg) ){
									insertBadWordsDb(&$url,&$id);
								}else{
									//別ドメインを検地しない
									if( $domain_type == 'b' ){
										$chk_domain_flg = checkOtherDomain($url,$search_host);
										if( is_null($chk_domain_flg) ){
											$listScriptLinkUrl[] = $url;
										}
									//別ドメインを検地する
									}else{
										$listScriptLinkUrl[] = $url;
									}
								}
							}
						}
					}
				//別の階層
				}elseif( preg_match('/^(\.\.\/){1,}/',$record) == 1 ){
					$folderCount = 0;
					$folderCount = substr_count ($record,'../');
					$record = preg_replace('/(\.\.\/)/','',$record,-1);
					if( is_null($BASE_URL) ){
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						$breakCount	 = count($listDir) - $folderCount;
						if( $breakCount > 0 ){
							$fcount = 0;
							if( count($listDir) > 0 ){
								foreach($listDir as $dir){
									$fcount++;
									$listTmpDir[] = $dir;
									if( $fcount == $breakCount )
										break;
								}
							}
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
//print "case11:";
//print $PARAM.$HOST.'/'.$tmp_path.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}else{
//print "case12:";
//print $PARAM.$HOST.'/'.$record."<br>\n";
							$url = trim($PARAM.$HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									$kw = 'IMG';
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}
					}else{
						setBaseUrlPath(&$BASE_URL);
						$listDir	 = explode("/",$BASE_PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						$breakCount	 = count($listDir) - $folderCount;
						if( $breakCount > 0 ){
							$fcount = 0;
							if( count($listDir) > 0 ){
								foreach($listDir as $dir){
									$fcount++;
									$listTmpDir[] = $dir;
									if( $fcount == $breakCount )
										break;
								}
							}
						}
						$BASE_PATH = implode("/",$listTmpDir);
						if( $BASE_PATH != "" ){
//print "case14:".$BASE_PARAM.$BASE_HOST.'/'.$BASE_PATH.'/'.$record."<br>\n";
							$url = trim($BASE_PARAM.$BASE_HOST.'/'.$BASE_PATH.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}else{
//print "case15:".$BASE_PARAM.$BASE_HOST.'/'.$record."<br>\n";
							$url = trim($BASE_PARAM.$BASE_HOST.'/'.$record);
							if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
								$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
								if( $exist_flg === false ){
									insertBadWordsDb(&$url,&$id);
								}else{
									$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
									if( is_null($exist_flg) ){
										insertBadWordsDb(&$url,&$id);
									}else{
										//別ドメインを検地しない
										if( $domain_type == 'b' ){
											$chk_domain_flg = checkOtherDomain($url,$search_host);
											if( is_null($chk_domain_flg) ){
												$listScriptLinkUrl[] = $url;
											}
										//別ドメインを検地する
										}else{
											$listScriptLinkUrl[] = $url;
										}
									}
								}
							}
						}
					}
				}else{
					$url = trim($record);
					if( $exclusion_mode == 'move' || $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
						$exist_flg = imgExclusionFile(&$url,&$domain_type,&$search_host);
						if( $exist_flg === false ){
							insertBadWordsDb(&$url,&$id);
						}else{
							$exist_flg = checkMoveFile(&$url,&$domain_type,&$search_host);
							if( is_null($exist_flg) ){
								insertBadWordsDb(&$url,&$id);
							}else{
								//別ドメインを検地しない
								if( $domain_type == 'b' ){
									$chk_domain_flg = checkOtherDomain($url,$search_host);
									if( is_null($chk_domain_flg) ){
										$listScriptLinkUrl[] = $url;
									}
								//別ドメインを検地する
								}else{
									$listScriptLinkUrl[] = $url;
								}
							}
						}
					}
				}
			}
		}
		//第二階層
		if( $check_flg == 2 ){
			$LIST_FRAME_URL = $listScriptLinkUrl;
			$LIST_FRAME_URL = array_unique($LIST_FRAME_URL);
			setUniqueUrl(&$LIST_FRAME_URL,$id,'',$domain_type,'db','',$exclusion_mode);		//重複URLを削除
		}
	}

	function setAnalysisImgUrl(&$record,$check_flg = 1){
		global $HOST;
		global $LIST_IMG_URL;
		global $BASE_URL;
		global $BASE_PARAM;
		global $BASE_HOST;
		global $BASE_PATH;
		global $PARAM;
		global $TARGET_URL;
		global $PATH;
		$search_host = preg_replace("/\./","\\.",$HOST);
		$exist_flg	 = preg_match("/^https?:\/\/$search_host/",$record);
		$http_flg	 = preg_match("/^https?:\/\/.*/",$record);
		if( $http_flg == 1 || $exist_flg == 1 ){
			$LIST_IMG_URL[] = trim($record);
		}else{
			$slash_flg = preg_match("/^\//",$record);
			//絶対パス
			if( $slash_flg == 1 ){
				if( is_null($BASE_URL) ){
					$LIST_IMG_URL[] = trim($PARAM.$HOST.$record);
				}else{
					$record = preg_replace('/^\//','',$record);
					$LIST_IMG_URL[] = trim($BASE_URL.$record);
				}
			}else{
				$sharp_flg = preg_match('/^#/',$record);
				if( $sharp_flg == 1 ){
					if( is_null($BASE_URL) ){
						$LIST_IMG_URL[] = trim($TARGET_URL.$record);
					}else{
						$LIST_IMG_URL[] = trim($BASE_URL.$record);
					}
					return ;
				}
				//同じ階層(./index.jpg)
				if( preg_match('/^\.\//',$record) == 1 ){
					if( is_null($BASE_URL) ){
						$record		 = preg_replace('/^(\.\/)/','',$record);
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						if( count($listDir) > 0 ){
							foreach($listDir as $dir)
								$listTmpDir[] = $dir;
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
							$LIST_IMG_URL[] = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
						}else{
							$LIST_IMG_URL[] = trim($PARAM.$HOST.'/'.$record);
						}
					}else{
						$LIST_IMG_URL[] = trim($BASE_URL.$record);
					}
				//同じ階層(index.jpg)
				}elseif(preg_match('/^\.\//',$record) != 1 && 
						preg_match('/^(\.\.\/){1,}/',$record) != 1 ){
					if( is_null($BASE_URL) ){
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						if( count($listDir) > 0 ){
							foreach($listDir as $dir)
								$listTmpDir[] = $dir;
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
							$LIST_IMG_URL[] = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
						}else{
							$LIST_IMG_URL[] = trim($PARAM.$HOST.'/'.$record);
						}
					}else{
						$LIST_IMG_URL[] = trim($BASE_URL.$record);
					}
				//別の階層
				}elseif( preg_match('/^(\.\.\/){1,}/',$record) == 1 ){
					$folderCount = 0;
					$folderCount = substr_count ($record,'../');
					$record = preg_replace('/(\.\.\/)/','',$record,-1);
					if( is_null($BASE_URL) ){
						$listDir	 = explode("/",$PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						$breakCount	 = count($listDir) - $folderCount;
						if( $breakCount > 0 ){
							$fcount = 0;
							if( count($listDir) > 0 ){
								foreach($listDir as $dir){
									$fcount++;
									$listTmpDir[] = $dir;
									if( $fcount == $breakCount )
										break;
								}
							}
						}
						$tmp_path = implode("/",$listTmpDir);
						if( $tmp_path != "" ){
							$LIST_IMG_URL[] = trim($PARAM.$HOST.'/'.$tmp_path.'/'.$record);
						}else{
							$LIST_IMG_URL[] = trim($PARAM.$HOST.'/'.$record);
						}
					}else{
						setBaseUrlPath(&$BASE_URL);
						$listDir	 = explode("/",$BASE_PATH);
						$listEndData = array_pop($listDir);
						$listTmpDir	 = array();
						$breakCount	 = count($listDir) - $folderCount;
						if( $breakCount > 0 ){
							$fcount = 0;
							if( count($listDir) > 0 ){
								foreach($listDir as $dir){
									$fcount++;
									$listTmpDir[] = $dir;
									if( $fcount == $breakCount )
										break;
								}
							}
						}
						$BASE_PATH = implode("/",$listTmpDir);
						if( $BASE_PATH != "" ){
							$LIST_IMG_URL[] = trim($BASE_PARAM.$BASE_HOST.'/'.$BASE_PATH.'/'.$record);
						}else{
							$LIST_IMG_URL[] = trim($BASE_PARAM.$BASE_HOST.'/'.$record);
						}
					}
				}else{
					$LIST_IMG_URL[] = trim($record);
				}
			}
		}
	}

	function curlConnect(&$url,$mode = null){
		global $FP;
		global $SOCK_TIMEOUT;
		global $LIST_CON_ERR_URL;
		global $CHECK_ERR_FLG;
		global $MAIL_ERR_MSG;
		global $LIST_ERR_MSG;
		global $CURL_CON_SLEEP;

		usleep($CURL_CON_SLEEP);
		$FP= curl_init();
		curl_setopt($FP, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($FP, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($FP, CURLOPT_TIMEOUT, $SOCK_TIMEOUT);
		curl_setopt($FP, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($FP, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($FP, CURLOPT_MAXREDIRS, 10);
		curl_setopt($FP, CURLOPT_URL, $url);
		if( $mode != 'chk_disp' )
			curl_setopt($FP, CURLOPT_HEADER, true);
		curl_setopt($FP, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)");
/*
		if( !curl_exec($FP) ){
			//エラーのURLを格納
			$LIST_CON_ERR_URL[]				 = "open err:".$url."\nErr No:".curl_error($FP)."\nErr Msg:".curl_error($FP)."\n";
print_r($LIST_CON_ERR_URL);
			$CHECK_ERR_FLG					 = true;
			return false;
		} else {
			return true;
		}
*/
		return true;
	}

	function curlClose(&$url){
		global $FP;
		global $HOST;
		global $PATH;
		global $LIST_CLOSE_ERR_URL;
		global $CHECK_ERR_FLG;
		global $MAIL_ERR_MSG;
		global $LIST_ERR_MSG;
		// ソケットがタイムアウトしたかどうか調べる
/*
		$stat = socket_get_status($FP);
		if ($stat["timed_out"]){
			$LIST_CLOSE_ERR_URL[]					 = "close err:".$url;
			$CHECK_ERR_FLG						 = true;
			$MAIL_ERR_MSG['socket_close_err']	 = $LIST_ERR_MSG[1];
		}
*/
		// CURLを閉じる
//		$err_flg = curl_close($FP);
		return true;
	}

	function _MicrotimeFloat(){
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	function sendMail($LIST_MAIL,$SUBJECT,$body){
		global $EMAIL_HEADER;
		global $LIMIT_MAIL_TIME;
		global $MAIL_CHAR_CODE;
		global $FROM_ADMIN_NAME;
		global $FROM_ADMIN_MAIL;

		if( $_SESSION['first_mail_time'] == '' ){
			$_SESSION['first_mail_time'] = time();
		}else{
			$_SESSION['total_time'] = time() - $_SESSION['first_mail_time'];
			$_SESSION['first_mail_time'] = time();
			if( $_SESSION['total_time'] < $LIMIT_MAIL_TIME )
				return true;
		}
/*
		mb_language("japanese");
		mb_internal_encoding("utf-8");
		$email_header = "From : ".mb_encode_mimeheader($FROM_ADMIN_NAME,"UTF-8")."<".$FROM_ADMIN_MAIL.">\n";

		if( count($LIST_MAIL) > 0 ){
			foreach($LIST_MAIL as $email){
				$result_flg = mb_send_mail($email, $SUBJECT, $body, $email_header);
				if( !$result_flg )
					return false;
				else
					return true;
			}
		}
*/
	}

	function delExclusionWord(&$html){
		global $LIST_REPLACEMENT;
		foreach($LIST_REPLACEMENT as $paterrn => $replace){
//			$html = mb_eregi_replace($paterrn,$replace,$html,"im");
			$html = preg_replace($paterrn,$replace,$html);
		}
	}

	function delSecoundUrlExclusionWord(&$html){
		global $LIST_SECOUND_URL_REPLACEMENT;
		foreach($LIST_SECOUND_URL_REPLACEMENT as $paterrn => $replace){
			$html = preg_replace($paterrn,$replace,$html);
		}
	}

	function delImgUrl(&$html,$CHAR_CODE = 'SJIS'){
		mb_regex_encoding($CHAR_CODE);
//		$html = mb_eregi_replace("<img(.*?)>","getAltMsg('\\1')",$html,"iem");
		$html = preg_replace("/<img(.*?)>/iem","getAltMsg('\\1');",$html);
	}

	function regMetaLocateUrl(&$html,&$locationUrl,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level = null){
		global $LIST_SEC_URL;
		global $LIST_CHECKED_URL;
		//リダイレクトタグがあるかどうか
		if( mb_eregi("<meta.*?http-equiv[[:space:]]{0,}=",$html) == 1 ){
			if( mb_eregi("Refresh",$html) == 1 ){
				$match_flg = mb_eregi("URL[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'[:space:]]{0,}.*?[^\"'[:space:]]{0,}.*?)[\"']{0,}",$html,$matchs);
				if( $match_flg != false ){
					//URLを解析し設定する
					setAnalysisUrl(&$matchs[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);

					if( $LIST_CHECKED_URL[0] != '' ){
						foreach($LIST_SEC_URL as $url){
							$index_flg = array_search($url,$LIST_CHECKED_URL);
							if( !$index_flg ){
								unset($LIST_SEC_URL[$index_flg]);
							}
						}
					}
					$lastindex = max(array_keys($LIST_SEC_URL));
					$locationUrl = $LIST_SEC_URL[$lastindex];
					$LIST_CHECKED_URL[] = $locationUrl;
					return false;
				}
			}
		}
		return true;
	}

	function getLocation(&$content,&$old_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,&$kwid,&$CHAR_CODE,$level = null){
		global $LIST_SEC_URL;
		if( preg_match("/Location[[:space:]]{0,}:[[:space:]]{0,}[\"']{0,}(.*?[^\"';[:space:]]{0,}.*?[^\"';[:space:]]{0,}.*?)[\"']{0,}/i",$content,$matchs) == 1 ){
			$location_url = trim($matchs[1]);
			if( preg_match("/https?:\/\//i",$location_url) != 1 ){
				setAnalysisUrl(&$matchs[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
				//metaタグで取得したURLが$LIST_SEC_URLに追加されているので削除 add 2009/10/20
				$lastindex = max(array_keys($LIST_SEC_URL));
				$location_url = $LIST_SEC_URL[$lastindex];
				unset($LIST_SEC_URL[$lastindex]);
/*	前のやり方をコメントアウト add 2010/05/06
				if( preg_match("/\/$/",$old_url) == 1 )
					$location_url = $old_url.trim($matchs[1]);
				else
					$location_url = $old_url.'/'.trim($matchs[1]);
*/
			}
			return $location_url;
		}
		if( mb_eregi("<meta.*?http-equiv[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'[:space:]]{0,}.*?[^\"'[:space:]]{0,}.*?)[\"']{0,}",$content) == 1 ){
			if( mb_eregi("Refresh",$content) == 1 ){
				$match_flg = mb_eregi("URL[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'[:space:]]{0,}.*?[^\"'[:space:]]{0,}.*?)[\"']{0,}>",$content,$matchs);
				if( $match_flg != false ){
					//URLを解析し設定する
					setAnalysisUrl(&$matchs[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
					//metaタグで取得したURLが$LIST_SEC_URLに追加されているので削除 add 2009/10/20
					$lastindex = max(array_keys($LIST_SEC_URL));
					$location_url = $LIST_SEC_URL[$lastindex];
					unset($LIST_SEC_URL[$lastindex]);
					return $location_url;
				}
			}
		}
		return null;
	}

	function checkExistFrameTag(&$html){
		if( preg_match("/<i?frame/i",$html) == 1 ){
			return true;
		}
		return null;
	}

	function setFrameTagUrl(&$html,&$url,&$frame_exist_flg,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level = null,$check_flg = 1){
		global $LIST_FRAME_URL;
		global $LIST_SEC_URL;
		$frame_url = null;
		if( preg_match("/<i?frame/i",$html) == 1 || $frame_exist_flg == true ){
			$frame_exist_flg = true;
			if( preg_match_all("/src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"';[:space:]]{0,}.*?[^\"';[:space:]]{0,}.*?)[\"']{0,}/is",$html,$matches) > 0 ){
				if( is_array($matches[1]) ){
					$listMatch = array_unique($matches[1]);
					if( count($listMatch) > 0 ){
						foreach($listMatch as $record){
							setAnalysisUrl(&$record,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level,$check_flg);
						}
					}
				}else{
					setAnalysisUrl(&$matchs[1],&$domain_type,&$search_host,&$exclusion_mode,&$id,$level,$check_flg);
				}
				if( $frame_exist_flg === false ){
					return false;
				}else{
					return true;
				}
			}
			if( preg_match("/<\/frameset>|<\/i?frame>|>/i",$html) == 1 ){
				$frame_exist_flg = false;
				return false;
			}
		}
		return false;
	}

	function setMultiFrameTagUrl(&$html,$check_flg = 1){
		if( preg_match_all("/<i?frame.*?[[:space:]]{0,}src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is",$html,$matches) > 0 ){
			if( is_array($matches[1]) ){
				$listMatch = array_unique($matches[1]);
				if( count($listMatch) > 0 ){
					foreach($listMatch as $record)
						setAnalysisUrl(&$record,$check_flg);
				}
			}else{
				setAnalysisUrl(&$matchs[4],$check_flg);
			}
			return true;
		}
		return false;
	}

	function setBaseUrl(&$content){
		global $BASE_URL;
		$BASE_URL = null;
		$pettern	 = "/<base.*?(href|target)[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
		if( preg_match($pettern,$content,$matchs) == 1 ){
			$BASE_URL = $matchs[2];
		}
	}

	function setJavaScriptTagUrl(&$content,&$url,&$javascript_close_check_flg,&$domain_type,&$search_host,&$exclusion_mode,&$id,&$kwid,&$CHAR_CODE){
		global $CHAR_CODE;
		global $LIST_SEC_URL;
		
		if( mb_eregi("<script",$content) == 1 ){
			if( mb_eregi("javascript",$content) == 1 )
				$javascript_close_check_flg = false;
			if( mb_eregi("text/javascript",$content) == 1 )
				$javascript_close_check_flg = false;
		}
		if( $javascript_close_check_flg === false ){
			//javascriptの範囲内
			if( preg_match("/^Location(\s?){0,}:(.*)/i",$content,$matches) == 1 ){
				$location_url = trim($matches[2]);
				setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id);
//print "javascript1:".$location_url."<br>\n";
			}
			if( preg_match("/src(\s?){0,}=(\s?){0,}[\"'](.*?)?[\"']/i",$content,$matches) == 1 ){
				if( preg_match("/https?:\.\.|\//i",$matches[3]) == 1 ){
					$location_url = trim($matches[3]);
					//URLを解析し設定する
					setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id);
//print "javascript2:".$location_url.":".$content."<br>\n";
				}
			}
			if( preg_match("/location\.href(.*)/i",$content,$matches) == 1 ){
				$location_url = trim($matches[1]);
				if( preg_match("/[\"'](.*?)[\"']/i",$location_url,$matches) == 1 )
					$location_url = trim($matches[1]);
				setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id);
//print "javascript4:".$location_url."<br>\n";
			}
			if(	preg_match("/^Location(\s?){0,}:(.*)/i",$content) != 1 && 
				preg_match("/src(\s?){0,}=(\s?){0,}[\"'](.*?)?[\"']/i",$content) != 1 && 
				( preg_match("/[\"']([a-zA-Z0-9_\.\/\-]*?\.js)[\"']/i",$content,$matches) == 1 || 
				  preg_match("/[\"']([a-zA-Z0-9_\.\/\-]*?\.s?html?)[\"']/i",$content,$matches) == 1 ) ){
				$location_url = trim($matches[1]);
				setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id);
//print "javascript3:".$location_url."<br>\n";
			}
			if( preg_match("/(https?:\/\/.*?)['\"]/i",$content,$matches) == 1 ){
				//別ドメインを検地しない
				if( $domain_type == 'b' ){
					$chk_domain_flg = checkOtherDomain($matches[1],$search_host);
					if( is_null($chk_domain_flg) ){
						$LIST_SEC_URL[] = $matches[1];
					}
				//別ドメインを検地する
				}else{
					$LIST_SEC_URL[] = $matches[1];
				}
//print "javascript5:".$matches[1]."<br>\n";
			}
			if( preg_match("/[\"']([a-zA-Z0-9_\.\/\-]*?\.js)[\"']/i",$content,$matches) == 1 || 
				preg_match("/[\"']([a-zA-Z0-9_\.\/\-]*?\.s?html?)[\"']/i",$content,$matches) == 1 ){
				$location_url = trim($matches[1]);
//				print "javascript6:".$location_url.":".$content."<br>";
				setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id);
			}
			if( mb_eregi("</script>",$content) == 1 )
				$javascript_close_check_flg = true;
		}
		if( mb_eregi("</script>",$content) == 1 )
			$javascript_close_check_flg = true;
	}

	function setJavaScriptTagUrl2(&$content,&$url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level = null){
		global $CHAR_CODE;
		global $LIST_SEC_URL;

		$pettern1	 = "/Location[[:space:]]{0,}:[[:space:]]{0,}[\"']{0,}(.*?[^\"';[:space:]]{0,}.*?[^\"';[:space:]]{0,}.*?)[\"']{0,}/is";
		$pettern2	 = "/src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"';[:space:]]{0,}.*?[^\"';[:space:]]{0,}.*?)[\"']{0,}/is";
/*
		$pettern1	 = "/<img.*?src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
		$pettern2	 = "/<a.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>(.*?)<\/(.+?)/is";
		$pettern3	 = "/<script.*?src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
		$pettern4	 = "/<area.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
		$pettern5	 = "/<link.*?href[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
		$pettern6	 = "/@import.*?u?r?l?\(?[\"']{0,}(.*?[^\"';\)]{0,}.*?[^\"';\)]{0,}.*?)[\"']{0,}\)?;?/is";
		$pettern7	 = "/background\-image[[:space:]]{0,}:[[:space:]]{0,}url\([\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}\);?/is";
*/

		//javascriptの範囲内
		if( preg_match($pettern1,$content,$matches) == 1 ){
			$location_url = trim($matches[1]);
			setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
//print "javascript1:".$location_url."<br>\n";
		}
		if( preg_match($pettern2,$content,$matches) == 1 ){
			if( preg_match("/https?:\.\.|\//is",$matches[1]) == 1 ){
				$location_url = trim($matches[1]);
				//URLを解析し設定する
				setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
//print "javascript2:".$location_url.":".$content."<br>\n";
			}
		}
		if( preg_match("/location\.href(.*)/i",$content,$matches) == 1 ){
			$location_url = trim($matches[1]);
			if( preg_match("/[\"']{0,}(.*?)[\"']{0,}/is",$location_url,$matches) == 1 )
				$location_url = trim($matches[1]);
			setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
//print "javascript4:".$location_url."<br>\n";
		}
		if(	preg_match($pettern1,$content) != 1 && 
			preg_match($pettern2,$content) != 1 && 
			( preg_match("/[\"']{0,}([a-zA-Z0-9_\.\/\-]*?\.js)[\"']{0,}/is",$content,$matches) == 1 || 
			  preg_match("/[\"']{0,}([a-zA-Z0-9_\.\/\-]*?\.s?html?)[\"']{0,}/is",$content,$matches) == 1 ) ){
			$location_url = trim($matches[1]);
			setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
//print "javascript3:".$location_url."<br>\n";
		}
		if( preg_match("/[\"']{0,}(https?:\/\/.*?)[\"']{0,}/is",$content,$matches) == 1 ){
			//別ドメインを検地しない
			if( $domain_type == 'b' ){
				$chk_domain_flg = checkOtherDomain($matches[1],$search_host);
				if( is_null($chk_domain_flg) ){
					$LIST_SEC_URL[] = $matches[1];
				}
			//別ドメインを検地する
			}else{
				$LIST_SEC_URL[] = $matches[1];
			}
//print "javascript5:".$matches[1]."<br>\n";
		}
		if( preg_match("/[\"']{0,}([a-zA-Z0-9_\.\/\-]*?\.js)[\"']{0,}/is",$content,$matches) == 1 || 
			preg_match("/[\"']{0,}([a-zA-Z0-9_\.\/\-]*?\.s?html?)[\"']{0,}/is",$content,$matches) == 1 ){
			$location_url = trim($matches[1]);
//			print "javascript6:".$location_url.":".$content."<br>";
			setAnalysisUrl(&$location_url,&$domain_type,&$search_host,&$exclusion_mode,&$id,$level);
		}
	}

	function exclusionBothFile(&$url,&$domain_type,&$search_host){
		global $HOST;
		$exist_flg = preg_match("/(\.bmp|\.gif|\.jpg|\.jpeg|\.png|\.pdf|\.txt|\.js|\.xml|\.scf|\.url|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.lzh|\.zip|\.gca|\.sit|\.exe|\.pdf|\.mp3|\.mp4|\.mpg|\.rm)$/i",$url);
		if( $exist_flg > 0 )
			return true;
		if( $domain_type == 'b' ){
			$exist_flg = preg_match("/$search_host/",$url);
			if( $exist_flg == 0 )
				return true;
		}
		if( $url == '' )
			return true;
		$exist_flg = preg_match("/^https:\/\/ssl\.$|^http:\/\/www\.$|^(\n){1,}$|^#.*/i",$url);
		if( $exist_flg > 0 )
			return true;
		return false;
	}

	function imgExclusionFile(&$url,&$domain_type,&$search_host){
		global $HOST;
		$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$url);
		if( $exist_flg == 0 )
			return true;
		if( $domain_type == 'b' ){
			$exist_flg = preg_match("/$search_host/",$url);
			if( $exist_flg == 0 )
				return true;
		}
		if( $url == '' )
			return true;
//		$exist_flg = preg_match("/^https:\/\/ssl\.$|^http:\/\/www\.$|^(\n){1,}$|^#/i",$url);
		$exist_flg = preg_match("/^(\n){1,}$|^#/i",$url);
		if( $exist_flg > 0 )
			return true;
		return false;
	}

	function checkMoveFile(&$url,&$domain_type,&$search_host){
		global $HOST;
		$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$url);
		if( $exist_flg > 0 )
			return null;
/*
		if( $domain_type == 'b' ){
			$exist_flg = preg_match("/$search_host/",$url);
			if( $exist_flg == 0 )
				return true;
		}
		if( $url == '' )
			return true;
		$exist_flg = preg_match("/^(\n){1,}$|^#/i",$url);
		if( $exist_flg > 0 )
			return true;
*/
		return false;
	}

	function checkImgFile(&$url,&$domain_type,&$search_host){
		global $HOST;
		$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$url);
		if( $exist_flg == 0 )
			return true;
		return false;
	}

	function mecab_analyse(&$content,&$listTmp,&$url,&$id,&$kwid,$exclusion_mode = null,$type,$CHAR_CODE){
		global $N_BEST_NUM;
		global $TOOL_A_DIC_NAME;
		global $TOOL_B_DIC_NAME;
		global $TOOL_C_DIC_NAME;
		global $MECAB_DIC_PATH;
		global $MECAB_CMD;
		global $TOOL_WORD_DIC_NAME;
/*
		if( $exclusion_mode == 'img' || $exclusion_mode == 'both' ){
			$exist_flg = preg_match("/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i",$url);
			if( $exist_flg > 0 ){
				$kw = 'IMG';
				insertBadWordsDb(&$url,&$id);
				return ;
			}
		}
*/
		if( $type == 'a' )
			$dic = $MECAB_DIC_PATH."/".$TOOL_A_DIC_NAME;
		elseif( $type == 'b' )
			$dic = $MECAB_DIC_PATH."/".$TOOL_B_DIC_NAME;
		elseif( $type == 'c' )
			$dic = $MECAB_DIC_PATH."/".$TOOL_C_DIC_NAME;
		else
			$dic = $MECAB_DIC_PATH."/".$TOOL_WORD_DIC_NAME.$type.".dic";
		$in;
		$handle;
		$in		 = tempnam("/tmp","mecab");
		$handle	 = fopen($in,"w");
		//HTMLタグ・HTMLコメント・PHPタグを除去
//		$content = strip_tags($content);
		fwrite($handle,$content);
		fclose($handle);
		$out;
		$out=tempnam("/tmp","mecab");
		//テストサーバー用
		exec($MECAB_CMD.$MECAB_DIC_PATH." -u ".$dic." -O suspend ".$in." -o ".$out);
		$tmp;
		$tmp	 = file_get_contents($out);
		$results = array();
		$results = explode("\n",$tmp);

		//最後の空白を削除する
		array_pop($results);
		unlink($out);
		unlink($in);

//		$results = array_unique($results);
		$listCount = 0;
		$listBadWords = array();
		foreach($results as $key => $record){
			$listWord = split(",",$record);
			$kw		 = mb_convert_encoding($listWord[0],"UTF-8","EUC-JP");
			$memo	 = mb_convert_encoding($listWord[1],"UTF-8","EUC-JP");
			$listBadWords[] = array($kw,$url,$memo);
			$listWord = array();
		}
		insertUrlDb(&$listBadWords,&$id,&$kwid,&$CHAR_CODE);
	}

	function insertBadWordsDb(&$url,&$id){
		global $INSERT_BADWORDS_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$kwid++;
		$sql = $INSERT_BADWORDS_SQL.$id.",'".$url."');";
		QueryDb($sql,$con);
	}

	function insertImgUrlDb(&$listUrl,&$id){
		global $INSERT_IMGURL_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$kwid++;
		$pid = getmypid();
		$listSqlParam = array();
		foreach($listUrl as $url){
			$listSqlParam[] = "(".$id.",'".$url."')";
		}
		$sql = $INSERT_IMGURL_SQL.implode(",",$listSqlParam).";";
		QueryDb($sql,$con);
	}

	function insertUrlDb(&$listUrl,&$id,&$kwid,&$CHAR_CODE){
		global $INSERT_URL_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$kwid++;
		$pid = getmypid();
		$listSqlParam = array();
		foreach($listUrl as $record){
			$listSqlParam[] = "(".$id.",".$kwid.",'".$record[1]."','".$record[0]."',".$pid.",'1','".$CHAR_CODE."','".$record[2]."')";
		}
		$sql = $INSERT_URL_SQL.implode(",",$listSqlParam).";";
		QueryDb($sql,$con);
	}

	function updateSearchedUrl(&$id,&$url){
		global $UPDATE_SEARCHED_URL_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$sql = $UPDATE_SEARCHED_URL_SQL."id = ".$id." and url = '".mysql_real_escape_string($url)."';";
		QueryDb($sql,$con);
	}

	function getStartInfoIdDb(){
		global $INSERT_START_TOOL_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$result_id = QueryDb($INSERT_START_TOOL_SQL,$con);
		return $result_id;
	}

	function setSearchDbDicWord(&$lst_type,&$search_word,&$listWord,&$con){
		global $SEARCH_LEN;
		global $CHAR_CODE;
		if( $lst_type == 'a' )
			$lst_type = 2;
		if( $lst_type == 'b' )
			$lst_type = 3;
		$sql_search_word = mb_substr($search_word,0,$SEARCH_LEN,"EUC-JP");
		if( $search_word == '' )
			return;
		$sql = "select distinct lst_text from mst_lc_suspend_text where lst_type = ".$lst_type." and lst_status = 1 and lst_text like \"".$sql_search_word."%\";";
		$listTmpData = getDbArray($sql,'str',$con);
		if( count($listTmpData) > 0 ){
			foreach($listTmpData as $record){
				if( $search_word == $record['lst_text'] )
					continue;
				$db_word = $record['lst_text'];
				if( mb_ereg($record['lst_text'],$search_word) == 1 ){
					$listWord[] = $record['lst_text'];
				}
			}
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

	function setBaseUrlPath(&$url){
		global $BASE_PARAM;
		global $BASE_HOST;
		global $BASE_PATH;

		$listParam1	 = array();
		$listParam2	 = array();
		$listCount	 = 0;
		$err_flg	 = false;

		$err_flg	 = preg_match("/(.+?:\/\/)(.+)/",$url,$listParam1);
		$BASE_PARAM	 = $listParam1[1];
		$err_flg	 = preg_match("/(.+?\/)(.*)/",$listParam1[2],$listParam2);
		$listCount	 = count($listParam2);
		if( $listCount > 0 ){
			$BASE_HOST = preg_replace("/\//","",$listParam2[1]);
			$listParam = split("/",$listParam2[2]);
			$BASE_PATH = $listParam2[2];
		}else{
			$BASE_HOST = preg_replace("/\//","",$listParam1[2]);
			$BASE_PATH = "";
		}
	}

	function delDic($dic_id){
		global $TOOL_WORD_FILE_NAME;
		global $TOOL_WORD_DIC_NAME;
		global $MECAB_DIC_PATH;
		$dic_file		 = $MECAB_DIC_PATH."/".$TOOL_WORD_FILE_NAME.$dic_id.".txt";
		$suspend_file	 = $MECAB_DIC_PATH."/".$TOOL_WORD_DIC_NAME.$dic_id.".dic";
print "del:".$dic_file."\n";
print "del:".$suspend_file."\n";
		unlink($dic_file);
		unlink($suspend_file);
	}

	function checkContentStatus($line){
		if( preg_match("/^HTTP\/1\.[0-1]/i",$line) !== false && preg_match("/^HTTP\/1\.[0-1]/i",$line) > 0 ){
			if( preg_match("/4[0-2]/i",$line) !== false && preg_match("/4[0-2]/i",$line) > 0 ){
				return false;
			}
		}
		return null;
	}

	function setContentLength(&$content_length,$line){
		if( preg_match("/^Content\-Length(\s?){0,}:(\s?){0,}(.*)/i",$line,$matches) !== false && preg_match("/^Content\-Length(\s?){0,}:(\s?){0,}(.*)/i",$line,$matches) > 0 ){
			$content_length = $matches[3];
		}
	}

	function checkContentRatio($content_length,$line){
		global $CONTENT_LENGTH_RATIO;
		$line_length = strlen($line);
		if( $line_length > 0 && $content_length > 0 ){
			$line_ratio = ( $line_length / $content_length ) * 100;
			if( (int)$line_ratio > (int)$CONTENT_LENGTH_RATIO ){
				return false;
			}
		}
		return null;
	}

	function setLastPageImageUrl(&$url,&$id,$CHAR_CODE,&$htmlSource,&$mode){
		global $FP;
		global $LIST_IMG_URL;

		$pettern1	 = "/<img.*?src[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>[:space:]]{0,}.*?[^\"'>[:space:]]{0,}.*?)[\"']{0,}[[:space:]]{0,}.*?>/is";
		$pettern2	 = "/background\-image[[:space:]]{0,}:[[:space:]]{0,}url\([\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}\);?/is";
		$pettern3	 = "/background[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}/is";
		$pettern4	 = "/background[[:space:]]{0,}:[[:space:]]{0,}url\([\"']{0,}(.*?[^;\)\"'>]{0,}.*?[^;\)\"'>]{0,}.*?)[\"']{0,}\)/is";

		$matches	 = array();
		$listMatch	 = array();
		$html		 = null;
		$char_flg	 = false;

		$HOST = null;
		$PATH = null;
		setHostPath(&$url);

			$LIST_IMG_URL		 = array();
			$start			 = _MicrotimeFloat();
			$coment_flg		 = false;
			$check_flg		 = curlClose(&$url);
			$listCurlData	 = explode("\n",$htmlSource);
			foreach($listCurlData as $record){
				//エンコード文字コード設定
				setEncodeCharCode(&$record,&$char_flg);
				$record = mb_convert_encoding($record,"EUC-JP",$CHAR_CODE);

				if( mb_eregi("<!--",$record) == 1 )
					$coment_flg = true;
				if( mb_eregi("-->",$record) == 1 )
					$coment_flg = false;
				if( $coment_flg === true )
					continue;
				$html .= $record;
			}
			//画像のURLを収集
			$hit_count = preg_match_all($pettern1,$html,$matches);
			if( $hit_count > 0 ){
				if( is_array($matches[1]) ){
					$listMatch = array_unique($matches[1]);
					if( count($listMatch) > 0 ){
						foreach($listMatch as $match_record){
							$ng_flg = preg_match("/javascript/i",$match_record);
							if( $ng_flg == 1 )
								continue;
							if( $mode == 'move' ){
								$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
							}elseif( $mode == 'both' ){
								$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
							}
							//拡張子が画像なら
							if( $exist_flg > 0 ){
								//URLを解析し設定する
								setAnalysisImgUrl(&$match_record);
							}
						}
					}
				}else{
					$ng_flg = preg_match("/javascript/i",$html);
					if( $ng_flg == 1 )
						continue;
						if( $mode == 'move' ){
							$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
						}elseif( $mode == 'both' ){
							$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
						}
					//拡張子が画像なら
					if( $exist_flg > 0 ){
						//URLを解析し設定する
						setAnalysisImgUrl(&$html);
					}
				}
			}
			$hit_count = preg_match_all($pettern2,$html,$matches);
			if( $hit_count > 0 ){
				if( is_array($matches[1]) ){
					$listMatch = array_unique($matches[1]);
					if( count($listMatch) > 0 ){
						foreach($listMatch as $match_record){
							$ng_flg = preg_match("/javascript/i",$match_record);
							if( $ng_flg == 1 )
								continue;
							if( $mode == 'move' ){
								$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
							}elseif( $mode == 'both' ){
								$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
							}
							//拡張子が画像なら
							if( $exist_flg > 0 ){
								//URLを解析し設定する
								setAnalysisImgUrl(&$match_record);
							}
						}
					}
				}else{
					$ng_flg = preg_match("/javascript/i",$html);
					if( $ng_flg == 1 )
						continue;
					if( $mode == 'move' ){
						$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
					}elseif( $mode == 'both' ){
						$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
					}
					//拡張子が画像なら
					if( $exist_flg > 0 ){
						//URLを解析し設定する
						setAnalysisImgUrl(&$html);
					}
				}
			}
			$hit_count = preg_match_all($pettern3,$html,$matches);
			if( $hit_count > 0 ){
				if( is_array($matches[1]) ){
					$listMatch = array_unique($matches[1]);
					if( count($listMatch) > 0 ){
						foreach($listMatch as $match_record){
							$ng_flg = preg_match("/javascript/i",$match_record);
							if( $ng_flg == 1 )
								continue;
							if( $mode == 'move' ){
								$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
							}elseif( $mode == 'both' ){
								$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
							}
							//拡張子が画像なら
							if( $exist_flg > 0 ){
								//URLを解析し設定する
								setAnalysisImgUrl(&$match_record);
							}
						}
					}
				}else{
					$ng_flg = preg_match("/javascript/i",$html);
					if( $ng_flg == 1 )
						continue;
					if( $mode == 'move' ){
						$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
					}elseif( $mode == 'both' ){
						$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
					}
					//拡張子が画像なら
					if( $exist_flg > 0 ){
						//URLを解析し設定する
						setAnalysisImgUrl(&$html);
					}
				}
			}
				$hit_count = preg_match_all($pettern4,$html,$matches);
			if( $hit_count > 0 ){
				if( is_array($matches[1]) ){
					$listMatch = array_unique($matches[1]);
					if( count($listMatch) > 0 ){
						foreach($listMatch as $match_record){
							$ng_flg = preg_match("/javascript/i",$match_record);
							if( $ng_flg == 1 )
								continue;
							if( $mode == 'move' ){
								$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
							}elseif( $mode == 'both' ){
								$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
							}
							//拡張子が画像なら
							if( $exist_flg > 0 ){
								//URLを解析し設定する
								setAnalysisImgUrl(&$match_record);
							}
						}
					}
				}else{
					$ng_flg = preg_match("/javascript/i",$html);
					if( $ng_flg == 1 )
						continue;
					if( $mode == 'move' ){
						$exist_flg = preg_match("/(\.lzh|\.zip|\.wmf|\.wmv|\.swf|\.asf|\.wav|\.aiff|\.avi|\.mov|\.mp3|\.mp4|\.mpg|\.rm)$/i",$match_record);
					}elseif( $mode == 'both' ){
						$exist_flg = preg_match("/\.gif|\.jpg|\.jpeg|\.png|\.bmp/i",$html);
					}
					//拡張子が画像なら
					if( $exist_flg > 0 ){
						//URLを解析し設定する
						setAnalysisImgUrl(&$html);
					}
				}
			}
			$kw = 'IMG';
			insertImgUrlDb(&$LIST_IMG_URL,&$id);
			$LIST_IMG_URL = array();

			if( $check_flg == true )
				return true;
			else
				return false;
		return true;
	}

	function getFormatInputUrl($url){
		$input_url			 = $url;
		$listUrlP = preg_split("/\//",$input_url);
		$last_url = $listUrlP[count($listUrlP)-1];
		if( preg_match("/\./",$last_url) != 1 ){
			if( $last_url != '' )
				$input_url .= "/";
		}
		return $input_url;
	}

	function getAltMsg($msg = null){
		if( !is_null($msg) ){
			$msg = preg_replace("/\\\\/","",$msg);
			$pettern	 = "/alt[[:space:]]{0,}=[[:space:]]{0,}[\"']{0,}(.*?[^\"'>]{0,}.*?[^\"'>]{0,}.*?)[\"']{0,}/i";
			$hit_count	 = preg_match_all($pettern,$msg,$matches);
			$alt_msg	 = null;
			if( $hit_count > 0 ){
				if( is_array($matches[1]) ){
					$listMatch = array_unique($matches[1]);
					if( count($listMatch) > 0 ){
						foreach($listMatch as $match_record){
							$alt_msg .= $match_record;
						}
					}
				}else{
					$alt_msg = $matches[1];
				}
			}
			return $alt_msg;
		}else{
			return false;
		}
	}

	function setListSearchUrl($id){
		global $SELECT_SEARCH_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		global $LIST_SEC_URL;
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
//		$sql = $SELECT_SEARCH_SQL."id = ".$id." order by level;";
		$sql = $SELECT_SEARCH_SQL."id = ".$id." order by level;";
		$listTmpData = getDbArray($sql,'str',$con);
		if( count($listTmpData) > 0 ){
			$LIST_SEC_URL = array();
			foreach($listTmpData as $record){
				$LIST_SEC_URL[] = $record['url'];
			}
		}else{
			return false;
		}
	}

	function setInsertCacheUrl($exist_url,$new_id,$level,$domain_type){
		global $SELECT_CACHE_SQL;
		global $INSERT_CACHE_URL_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		$listLevel = array();
		for($i=0;$i<$level;$i++){
			$listLevel[] = $i;
		}
		$sql_where = "level in(".implode(",",$listLevel).")";

		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$sql = $SELECT_CACHE_SQL.'exist_url = "'.$exist_url.'" and domain_type = "'.$domain_type.'" and '.$sql_where.';';
		$listData = getDbArray($sql,'str',$con);
		$listSqlParam = array();
		foreach($listData as $record){
			//insert into urllist (id,url,level) values 
			$listSqlParam[] = '('.$new_id.",'".$record['url']."',".$record['level'].")";
		}
		$sql = $INSERT_CACHE_URL_SQL.implode(",",$listSqlParam).";";
		QueryDb($sql,$con);
	}

	function setListExistSearchUrl($exist_url,$id,$domain_type,$level){
		global $SELECT_CACHE_SQL;
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		global $LIST_SEC_URL;
		$listLevel = array();
		for($i=0;$i<$level;$i++){
			$listLevel[] = $i;
		}
		$sql_where = "level in(".implode(",",$listLevel).")";
		$sql2 = $SELECT_CACHE_SQL.'domain_type = "'.$domain_type.'" and exist_url = "'.$exist_url.'" and '.$sql_where.' order by level asc';
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);

		$listTmpData2 = getDbArray($sql2,'str',$con);
		//指定階層のＵＲＬキャッシュのＵＲＬリストを取得
		if( count($listTmpData2) > 0 ){
			foreach($listTmpData2 as $record){
				$LIST_SEC_URL[] = $record['url'];
			}
		}else{
			return false;
		}
	}

	function setFormatUrl($msg = null){
		if( !is_null($msg) ){
			return $msg;
		}
	}

	function checkOldUrl($url,$domain_type,$level){
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;
		global $SELECT_CACHE_COUNT_SQL;
/*
		$listLevel = array();
		for($i=1;$i<=$level;$i++){
			$listLevel[] = $i;
		}
		$sql_where = "level in(".implode(",",$listLevel).")";
		$sql = $SELECT_CACHE_COUNT_SQL.'exist_url like "%'.mysql_real_escape_string($host).'%" and domain_type = "'.mysql_real_escape_string($domain_type).'" and '.mysql_real_escape_string($sql_where).' limit 1;';
*/
		$sql = $SELECT_CACHE_COUNT_SQL.'exist_url = "'.mysql_real_escape_string($url).'" limit 1;';
		$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);
		$listTmpData = getDbArray($sql,'str',$con);
		if( $listTmpData[0]['exist_url'] != '' ){
			return $listTmpData[0]['exist_url'];
		}else{
			return null;
		}
	}

	function getMultiByteEncodeString(&$str){
		$mstr = '';
		for($i=0;$i<strlen( $str );$i++){
			$c=substr($str,$i,1);
			for($m=0;$m<mb_strlen( $str ,"EUC-JP" );$m++){
				$mc=mb_substr($str,$i,1,"EUC-JP");
				if( $m == $i ){
					if( $c != $mc ){
						$mstr .= urlencode($mc);
					}else{
						$mstr .= $mc;
					}
				}
			}
		}
		return $mstr;
	}

	function getUrlFolderPath($param){
//		$param = preg_replace("/^((.+?\/){0,}).*\.?.*\?.*/ie","setFolderPath('\\1');",$param);
		$param = preg_replace("/^((.+?\/){0,}).*\.?.*\?.*/i","\\1",$param);
		return $param;
	}

	function setFolderPath($param){
		
	}

?>