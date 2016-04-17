<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "書き換えてください");
require_once('utility/crawler_utility.php');

/* ***************************************************************

	このファイルはブラウザから閲覧可能な場所へ置きます

**************************************************************** */

$DISP_DATA = array();

setSearchTopicNews(&$DISP_DATA);

print $DISP_DATA['disp_img'];

function setSearchTopicNews(&$disp_data){
		global $DB_JUDGE_HOST;
		global $DB_JUDGE_USER;
		global $DB_JUDGE_PASS;
		global $DB_JUDGE_NAME;

	$con = ConnectSelectDb($DB_JUDGE_HOST,$DB_JUDGE_USER,$DB_JUDGE_PASS,$DB_JUDGE_NAME);

	$sql = "select * from badwords order by createdate;";
	$listAmeblo	 = getDbArray($sql,'str',$con);
	if( empty($listAmeblo) === false ){
		foreach($listAmeblo as $record){
			$err_flg = checkFilter(&$record['url']);
			if( is_null($err_flg) ){
				$disp_data['disp_img'] .= "<a href='".$record['url']."'><img src='".$record['url']."' border='0'></a>";
			}
		}

	}
	return null;
}

function checkFilter(&$url){
//	$exist_flg = preg_match("/^http:\/\/(official\.stat\.ameba\.jp|stat\.ameba\.jp|stat100\.ameba\.jp)/",$url);
//	$exist_flg = preg_match("/^http:\/\/official\.stat\.ameba\.jp/",$url);
//	if( $exist_flg > 0 )
		return null;

//	return false;
}

?>