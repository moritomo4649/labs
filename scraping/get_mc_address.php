<?php
$USER_AGENT		 = 'DoCoMo/2.0 P903i';
$CON_USLEEP		 = 2500000;
$SAVE_FILE		 = 'mc_';
$LIST_PREF		 = array(
	'kanto',
	'kansai',
	'chubu',
	'kyushu',
	'tohoku',
	'chugoku',
	'hokuriku',
	'hokkaido',
	'shikoku',
	'okinawa'
);
$listMc			 = array();

mb_regex_encoding('UTF-8');

//都道府県別にURLを収集
for($i=0;$i<count($LIST_PREF);$i++){
	$listMcShop = array();
	$key = sprintf("%02d",$LIST_PREF[$i]);
	$search_url = "http://brand.gnavi.co.jp/brand/mcdonalds/".$LIST_PREF[$i]."/shop/";
	do{
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		if( preg_match_all("/<a\s+?class=\"name\"\s+?href=\"(http:\/\/r\.gnavi\.co\.jp\/.+?\/)\"\s+?target=\"_blank\">(.+?)<img\s+?alt=\"\"\s+?src=\".+?\"\s+?class=\"thm\"\s+?\/><\/a>/u",$search_source,$listPref) > 0 ){
print_r($listPref);
			foreach($listPref[1] as $index => $pref_url){
				$listMcShop[] = array($pref_url,$listPref[2][$index]);
			}
		}
		$next_flg = 'ok';
		if( preg_match("/.+?<li\s+?class=\"next\"><a\s+?href=\"(http:\/\/brand\.gnavi\.co\.jp\/brand\/mcdonalds\/.+?\/shop\/.+?\/)\">次を表示<\/a><\/li><\/ul>/u",$search_source,$listNext) > 0 ){
			$search_url = $listNext[1];
			$next_flg = 'ng';
		}
	}while( $next_flg == 'ng' );

foreach($listMcShop as $record){
usleep(250000);
	$search_url = $record[0];
	$search_source = getAccessSource($CON_USLEEP,$search_url);
print $record[1].':'.$record[0].":";
	//市町村
	if( preg_match_all("/<th>住所<\/th><td\s+?class=\"slink\"><p\s+?class=\"adr\">〒[0-9]+?\-[0-9]+?&nbsp;<span\s+?class=\"region\">([\s\S.]+?)<\/span><\/p>/im",$search_source,$listAddr) > 0 ){
		$listAddr[1][0] = preg_replace("/<a[\s\S.]+?>/","",$listAddr[1][0]);
		$listAddr[1][0] = preg_replace("/<\/a>/","",$listAddr[1][0]);
		$listAddr[1][0] = preg_replace("/<span[\s\S.]+?>/","",$listAddr[1][0]);
		$listAddr[1][0] = preg_replace("/<\/span>/","",$listAddr[1][0]);
		$listAddr[1][0] = preg_replace("/&nbsp;/","",$listAddr[1][0]);
print $listAddr[1][0]."\n";
		$listMc[$record[1]] = $listAddr[1][0];
	}
}

	$fp = fopen($SAVE_FILE.$key.".txt","w");
	foreach($listMc as $shop_name => $address){
print $shop_name.":".$address."\n";
		fwrite($fp,$shop_name.",".$address."\n");
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