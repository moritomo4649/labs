<?php
define('ADMIN_TO_MAIL','');
$ADMIN_TO_MAIL	 = '';

function ConnectSelectDb($host,$user,$pass,$db,$script = ''){
	global $ADMIN_TO_MAIL;
	global $LIST_EMAIL;
	if( $script != '' ){
		$_SERVER['PHP_SELF'] = $script;
	}
	$con_result	 = mysql_connect($host,$user,$pass);
	//
	if( !$con_result ){
		`echo 'db connect failed:$host' | mail $ADMIN_TO_MAIL`;
	}
	$db_result = mysql_select_db($db,$con_result);
	//DB
	if( !$db_result ){
		`echo 'db select failed:$db' | mail $ADMIN_TO_MAIL`;
	}else{
		return $con_result;
	}
}


function ConnectDb($host,$user,$pass,$db){
	$ADMIN_TO_MAIL = ADMIN_TO_MAIL;
	$con_result	 = mysql_connect($host,$user,$pass);
	//接続失敗
	if( !$con_result ){
		`echo 'db connect failed:$host' | mail $ADMIN_TO_MAIL`;
		return false;
		exit;
	}
	$db_result = mysql_select_db($db,$con_result);
	//DB選択失敗
	if( !$db_result ){
		`echo 'db select failed:$db' | mail $ADMIN_TO_MAIL`;
		return false;
		exit;
	}else{
		return $con_result;
	}
}

function getDbArray($sql,$mode = '',$con = ''){
	$ADMIN_TO_MAIL = ADMIN_TO_MAIL;
	if( $con != '' ){
		$result = mysql_query($sql,$con);
	}else{
		$result = mysql_query($sql);
	}
	$listNewData = array();
	//クエリ成功
	if( $result ){
		while( $rs = mysql_fetch_array( $result ) ){
			foreach($rs as $key => $value){
				//フィールド名・数字の両方をキーとして取得
				if( $mode == '' ){
					$listData["$key"] = $value;
				//数字をキーとして取得
				}elseif( is_numeric($key) && $mode == 'num' ){
					$listData["$key"] = $value;
				//DBのフィールド名をキーとして取得
				}elseif( is_string($key) && $mode == 'str' ){
					$listData["$key"] = $value;
				}
			}
			$listNewData[] = $listData;
			unset($listData);
		}
	//失敗
	}else{
		`echo 'db select failed:$sql' | mail $ADMIN_TO_MAIL`;
		return false;
		exit;
	}
	return $listNewData;
}

function QueryDb($sql,$con = ''){
	$ADMIN_TO_MAIL = ADMIN_TO_MAIL;
	if( $con != '' ){
		$result = mysql_query($sql,$con);
	}else{
		$result = mysql_query($sql);
	}
	//失敗
	if( !$result ){
		if( mysql_errno() == '1062' ){
			return '1062';
		}
//		`echo "db query failed:$sql" | mail $ADMIN_TO_MAIL`;
		return false;
		exit;
	}
	return mysql_insert_id($con);
}


?>
