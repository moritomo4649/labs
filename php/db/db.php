<?php

//MySQLのDBと接続するのみ
function DBConnect(){
	$con = mysql_connect(DB_HOST,DB_USER,DB_PASS);
	if (!$con) {
//		die("接続失敗\n".mysql_error());
		return false;
	}else {
		mysql_query("set names utf8;",$con);
		$err_flg = mysql_select_db(DB_NAME,$con);
		if(!$err_flg){
			//echo "データベース選択失敗..\n";
			//Mysql接続CLOSE..
//			die("データベース選択失敗\n");
			mysql_close($con);
			return false;
		}else{
			return $con;
		}
	}
}

function DBSltConnect($host,$db_name,$user,$pass){
	$con = mysql_connect($host,$user,$pass);
	if (!$con) {
//		die("接続失敗\n".mysql_error());
		return false;
	}else {
		mysql_query("set names utf8;",$con);
		$err_flg = mysql_select_db($db_name,$con);
		if(!$err_flg){
			//echo "データベース選択失敗..\n";
			//Mysql接続CLOSE..
//			die("データベース選択失敗\n");
			mysql_close($con);
			return false;
		}else{
			return $con;
		}
	}
}

//DBお問い合わせ(Select)
function DbSltQueryArray($sql,$mode,$con = null){
	if( is_null($con) )
		$result = mysql_query($sql);
	else
		$result = mysql_query($sql,$con);

	if(!$result) {
//		die("$sql::失敗\n" . mysql_error());
		return false;
	}else{
		while($row = mysql_fetch_array($result)){
			$listData = array();
			foreach($row as $key => $value){
				if( is_string($key) && $mode == 'str' ){
					$listData[$key] = $value;
				}elseif( is_numeric($key) && $mode == 'num' ){
					$listData[$key] = $value;
				}elseif( $mode == '' ){
					$listData[$key] = $value;
				}
			}
			$listNewData[] = $listData;
			unset($listData);
		}
		return $listNewData;
	}
}

//DB登録・更新・削除
function DbQuery($sql,$con = null){
	if( is_null($con) )
		$result = mysql_query($sql);
	else
		$result = mysql_query($sql,$con);

	if(!$result){
//		die("$sql::失敗\n" . mysql_error());
		return false;
		exit;
	}else{
		$insert_num = mysql_insert_id($con);
		return $insert_num;
	}
}
?>
