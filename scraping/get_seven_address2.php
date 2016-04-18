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

mb_regex_encoding('UTF-8');

//都道府県別にURLを収集
for($i=0;$i<count($LIST_PREF);$i++){
	$listMcShop = array();
	$key = sprintf("%02d",$i);
	$search_url = "http://brand.gnavi.co.jp/brand/mcdonalds/".$LIST_PREF[$i]."/shop/";
	do{
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		$search_source = mb_convert_encoding($search_source,"UTF-8","SJIS");
		if( preg_match_all("/<a\s+?class=\"name\"\s+?href=\"(http:\/\/r\.gnavi\.co\.jp\/.+?\/)\"[\s\S.]+?>([\s\S.]+?)<img[\s\S.]+?src=\".*?\"[\s\S.]+?\/><\/a>/im",$search_source,$listPref) > 0 ){
print_r($listPref);
			foreach($listPref[1] as $index => $pref_url){
				$listMcShop[] = array($pref_url,$listPref[2][$index]);
			}
		}
		$next_flg = 'ok';
		if( preg_match_all("/<a\s+?href=\"(.+?)\">次を表示<\/a>/u",$search_source,$listNext) > 0 ){
print_r($listNext);
			$search_url = $listNext[1][0];
			$next_flg = 'ng';
		}
exit;
	}while( $next_flg == 'ng' );

$listPrefSeven1 = array();
foreach($listPrefSeven as $record){
print $record[1].':'.$record[0]."\n";
usleep(250000);
	do{
		$search_url = "http://www.e-map.ne.jp/mobile/711map/".$record[0];
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		$search_source = mb_convert_encoding($search_source,"UTF-8","SJIS");
		//市町村
		if( preg_match_all("/<a\s+?href=[\"'](d\.htm\?id=[0-9]*?&p_s1=[0-9]*?&p_f2=[0-9]*?&p_f4=[0-9]*?&&optcd=[0-9]*?&sej_nwshp=.*?)[\"'][\s\S.]*?>(.*?)<\/a>/im",$search_source,$listPref) > 0 ){
			foreach($listPref[1] as $index => $pref_url){
				$listPrefSeven1[$record[1]][] = array($pref_url,$listPref[2][$index]);
			}
		}
		$next_flg = 'ok';
		if( preg_match_all("/(.*?前ﾍﾟｰｼﾞ<\/a>\/)?<A\s+?href\s*?=\s*?[\"'](cl\.htm\?p=.*?&slg=[0-9]*?&key=.*?&pg=[0-9]*?&tod=COL_.+?&shk=COL_.+?&&p_s1=[0-9]*?&p_f2=[0-9]*?&p_f4=[0-9]*?&&optcd=[0-9]*?)[\"']>次ﾍﾟｰｼﾞ<\/a>/u",$search_source,$listPref) > 0 ){
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
		$search_url = "http://www.e-map.ne.jp/mobile/711map/".$record[0];
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
		$search_source = mb_convert_encoding($search_source,"UTF-8","SJIS");
		$listAddr = array();
		if( preg_match("/■店舗名<BR>(.+?)<BR>/im",$search_source,$listShop) > 0 ){
			$listAddr[] = $listShop[1];
		}
		if( preg_match("/■住所<BR>(.+?)<BR>/im",$search_source,$listShop) > 0 ){
			$listAddr[] = $listShop[1];
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