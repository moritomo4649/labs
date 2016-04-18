<?php
$USER_AGENT		 = 'DoCoMo/2.0 P903i';
$CON_USLEEP		 = 2500000;
$SAVE_FILE		 = 'starbucks_';
$listMc			 = array();

mb_regex_encoding('UTF-8');

//都道府県別にURLを収集
for($i=1;$i<=47;$i++){
	$listMcShop = array();
	$search_url = "http://www.starbucks.co.jp/store/search/result_store.php?pref_code=".$i;
	do{
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		if( preg_match_all("/<td\s+?class=\"storeName\"><a\s+?href=\"\/(store\/search\/detail\.php\?id=[0-9]+?&search_condition=.+?&pref_code=[0-9]+?(&pageID=[0-9]+?)?)\">(.+?)<\/a><br\s+?\/>/u",$search_source,$listPref) > 0 ){
			foreach($listPref[1] as $index => $pref_url){
				$listMcShop[] = array('http://www.starbucks.co.jp/'.$pref_url,$listPref[3][$index]);
print $pref_url.":".$listPref[3][$index]."\n";
			}
		}
		$next_flg = 'ok';
		if( preg_match_all("/<a\s+?href=\"\/(store\/search\/result_store\.php\?pref_code=[0-9]+?&amp;pageID=[0-9]+?)\"\s+?title=\"next\s+?page\">/u",$search_source,$listNext) > 0 ){
			$search_url = 'http://www.starbucks.co.jp/'.$listNext[1][0];
			$search_url = preg_replace('/&amp;/','&',$search_url);
print $search_url."\n";
			$next_flg = 'ng';
		}
	}while( $next_flg == 'ng' );

	foreach($listMcShop as $record){
		usleep(200000);
		$search_url = $record[0];
		$search_source = getAccessSource($CON_USLEEP,$search_url);
print $record[1].':'.$record[0].":";
		//市町村
		if( preg_match_all("/<th>住所<\/th>[\s\S]*?<td>[0-9]+?\-[0-9]+?<br\s+?\/>([\s\S.]+?)<\/td>/im",$search_source,$listAddr) > 0 ){
print $listAddr[1][0]."\n";
			$listMc[$record[1]] = $listAddr[1][0];
		}
	}

	$fp = fopen($SAVE_FILE.$i.".txt","w");
	foreach($listMc as $shop_name => $address){
		$address = preg_replace("/\s/u",'',trim($address));
print $shop_name.":".$address."\n";
		fwrite($fp,trim($shop_name).",".$address."\n");
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
//		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
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
//		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
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
//		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
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
//		curl_setopt($FP, CURLOPT_USERAGENT, $USER_AGENT);
		curl_setopt($FP, CURLOPT_HTTPHEADER, array('Expect:'));

		$content	 = curl_exec($FP); 
		$check_flg	 = curl_close($FP);
	}
	$content = mb_ereg_replace("\n","",$content);
	return $content;
}


?>