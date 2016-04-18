<?php
$USER_AGENT		 = 'DoCoMo/2.0 P903i';
$CON_USLEEP		 = 2500000;
$SAVE_FILE		 = 'threef_shop_';

mb_regex_encoding('UTF-8');

//都道府県別にURLを収集
for($i=39;$i<=39;$i++){
	$listPrefSeven = array();
	$key = sprintf("%02d",$i);
	$search_url = "http://standard.navitime.biz/three-f/CondAreaSpotCount.act?acode=".$key."&page=0&cond=&area=1&cateCd=&menuName=&scate=&uid=01msimmsim00&guid=ON";
	do{
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		$search_source = mb_convert_encoding($search_source,"UTF-8","SJIS");
		if( preg_match_all("/<a\s+?href=[\"'](CondAreaSpotCount\.act\?uid=.*?&guid=ON&acode=[0-9]*?&count=[0-9]*?&area=[0-9]*?)[\"'][\s\S.]*?>([\s\S.]*?)<\/a>/im",$search_source,$listPref) > 0 ){
			foreach($listPref[1] as $index => $pref_url){
				$listPrefSeven[] = array($pref_url,$listPref[2][$index]);
			}
		}
		$next_flg = 'ok';
		if( preg_match_all("/<a\s+?href=[\"'](CondAreaSpotCount\.act\?acode=[0-9]*?&page=[0-9]*?&cond=&area=[0-9]*?&cateCd=&menuName=.*?&scate=&uid=.*?&guid=ON)[\"']\s+?accesskey=\"#\">次へ<\/a><br\/>/u",$search_source,$listPref) > 0 ){
			$search_url = "http://standard.navitime.biz/three-f/".$listPref[1][0];
			$next_flg = 'ng';
		}
	}while( $next_flg == 'ng' );

//市区町村名
$listPrefSeven1 = array();
foreach($listPrefSeven as $record){
print $record[1].':'.$record[0]."\n";
usleep(250000);
	do{
		$search_url = "http://standard.navitime.biz/three-f/".$record[0];
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		$search_source = mb_convert_encoding($search_source,"UTF-8","SJIS");
		//店舗一覧
		if( preg_match_all("/<a\s+?href=\"(Spot\.act\?uid=.*?&guid=ON&dnvSpt=.*?)\"\s*?accesskey=\"[0-9]+?\">([\s\S.]*?)<\/a>/im",$search_source,$listPref) > 0 ){
			foreach($listPref[1] as $index => $pref_url){
				$listPrefSeven1[$record[1]][] = array($pref_url,$listPref[2][$index]);
			}
		}
		$next_flg = 'ok';
		if( preg_match_all("/#<a\s+?href=[\"'](CondAreaSpotList\.act\?acode=[0-9]*?&page=[0-9]*?&cond=.*?&area=[0-9]*?&menuName=.*?&count=[0-9]*?&uid=.*?&guid=ON)[\"']\s+?accesskey=\"#\">次へ<\/a>/u",$search_source,$listPref) > 0 ){
			$record[0] = $listPref[2][0];
			$next_flg = 'ng';
		}
	}while( $next_flg == 'ng' );
}

$listSevenShop = array();
foreach($listPrefSeven1 as $city => $listTown){
	foreach($listTown as $record){
print $record[1].':'.$record[0]."\n";
usleep(250000);
		$search_url = "http://standard.navitime.biz/three-f/".$record[0];
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		$search_source = mb_convert_encoding($search_source,"UTF-8","SJIS");
		$listAddr = array();
		if( preg_match("/<font\s+?color=\".*?\">([\s\S.]+?)<\/font>/im",$search_source,$listShop) > 0 ){
			$listAddr[] = trim($listShop[1]);
		}
		if( preg_match("/･<a\s+?href=\"tel:[0-9]+?\"\s+?>[0-9-]*?<\/a><br\/>[\s\S]*?･([\s\S.]+?)<br>/im",$search_source,$listShop) > 0 ){
			$listAddr[] = trim($listShop[1]);
		}
		$listSevenShop[$city][] = $listAddr;
	}
}

	$fp = fopen($SAVE_FILE.$key.".txt","w");
	foreach($listSevenShop as $city => $listTown){
		$city = mb_ereg_replace("\([0-9]+\)","",$city);
print $city."\n";
		foreach($listTown as $record){
print $record[0]."|".$record[1]."\n";
			fwrite($fp,$city.",".$record[0].",".$record[1]."\n");
		}
	}
	fclose($fp);
}
print "終了\n";
exit;

function getAccessSource($con_sleep = 1000000,$url = null,$mode = null,$params = null){
	global $USER_AGENT;

	usleep($con_sleep);

	//ログインしてアクセス
	if( $mode == 'login' ){
		$fpw = fopen("tmp_debug", "w");
		$FP = curl_init($url); 
		curl_setopt($FP, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($FP, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($FP, CURLOPT_TIMEOUT, 10);
		curl_setopt($FP, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($FP, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($FP, CURLOPT_COOKIEJAR, "cookie"); 
		curl_setopt($FP, CURLOPT_POST, TRUE); 
		curl_setopt($FP, CURLOPT_POSTFIELDS, $params); 
		curl_setopt($FP, CURLOPT_WRITEHEADER, $fpw);
		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
		curl_setopt($FP, CURLOPT_HTTPHEADER, array('Expect:'));

		$content	 = curl_exec($FP); 
		$check_flg	 = curl_close($FP);
		fclose($fpw);

	//SESSION引継ぎ
	}elseif( $mode == 'session' ){
		$FP = curl_init($url); 
		curl_setopt($FP, CURLOPT_HEADER, false);
		curl_setopt($FP, CURLOPT_SSLVERSION, 3);
		curl_setopt($FP, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($FP, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($FP, CURLOPT_TIMEOUT, 10);
		curl_setopt($FP, CURLOPT_COOKIEJAR, "cookie"); 
		curl_setopt($FP, CURLOPT_COOKIEFILE, "tmp_debug");
		curl_setopt($FP, CURLOPT_POST, TRUE); 
		curl_setopt($FP, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
		curl_setopt($FP, CURLOPT_HTTPHEADER, array('Expect:'));

		$content	 = curl_exec($FP); 
		$check_flg	 = curl_close($FP);

	}elseif( $mode == 'session_post' ){
		$FP = curl_init($url); 
		curl_setopt($FP, CURLOPT_HEADER, false);
		curl_setopt($FP, CURLOPT_SSLVERSION, 3);
		curl_setopt($FP, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($FP, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($FP, CURLOPT_TIMEOUT, 10);
		curl_setopt($FP, CURLOPT_COOKIEJAR, "cookie"); 
		curl_setopt($FP, CURLOPT_COOKIEFILE, "tmp_debug");
		curl_setopt($FP, CURLOPT_POST, TRUE); 
		curl_setopt($FP, CURLOPT_POSTFIELDS, $params); 
		curl_setopt($FP, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($FP, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
		curl_setopt($FP, CURLOPT_HTTPHEADER, array('Expect:'));

		$content	 = curl_exec($FP); 
		$check_flg	 = curl_close($FP);
	}else{
		$FP = curl_init($url); 
		curl_setopt($FP, CURLOPT_HEADER, false);
		curl_setopt($FP, CURLOPT_SSLVERSION, 3);
		curl_setopt($FP, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($FP, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($FP, CURLOPT_TIMEOUT, 10);
		curl_setopt($FP, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($FP, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
		curl_setopt($FP, CURLOPT_HTTPHEADER, array('Expect:'));

		$content	 = curl_exec($FP); 
		$check_flg	 = curl_close($FP);
	}
	$content = mb_ereg_replace("\n","",$content);
	return $content;
}


?>