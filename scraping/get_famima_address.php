<?php
$USER_AGENT		 = 'DoCoMo/2.0 P903i';
$CON_USLEEP		 = 1200000;
$SAVE_FILE		 = 'famima_shop_';

mb_regex_encoding('UTF-8');

/*
	http://as.chizumaru.com/famima/m/mList?acc=famima0&arg=&c2=1&bsort=1&dc=01&adr=01
	http://as.chizumaru.com/famima/m/mList?acc=famima0&arg=&c2=1&bsort=1&dc=02&adr=02
	http://as.chizumaru.com/famima/m/mList?acc=famima0&arg=&c2=1&bsort=1&dc=02&adr=03
	http://as.chizumaru.com/famima/m/mList?acc=famima0&arg=&c2=1&bsort=1&dc=03&adr=11
*/

//都道府県別にURLを収集
for($i=27;$i<=27;$i++){
	$listPrefSeven = array();
	$key = sprintf("%02d",$i);
	$search_url = "http://as.chizumaru.com/famima/m/mList?acc=famima0&arg=&c2=1&bsort=1&dc=01&adr=".$key;
	do{
print $search_url."\n";
		$search_source = getAccessSource($CON_USLEEP,$search_url);
/*
		if( preg_match("/<span[\s\S.]+?>■<\/span>住所で探す<br\s+?\/>[\s\S]+?<span[\s\S.]+?>■<\/span>([\s\S.]+?)<br\s+?\/>/u",$search_source,$listPref) > 0 ){
			$pref = $listPref[1];
		}
*/
		if( preg_match_all("/<a\s+?href=\"\.\/(mDtl\?acc=.*?&arg=.*?&bid=[0-9]+?)\"\s+?accesskey=\"[0-9]+?\">([\s\S.]+?)<\/a>/u",$search_source,$listPref) > 0 ){
			foreach($listPref[1] as $index => $pref_url){
				$listPrefSeven[] = array($pref_url,$listPref[2][$index]);
			}
		}
		$next_flg = 'ok';
		if( preg_match_all("/\[#\]<a\s+?href=\"(\/famima\/m\/mList\?acc=.*?&arg=.*?&c2=[0-9]+?&bsort=[0-9]+?&dc=[0-9]+?&adr=[0-9]+?&pg=[0-9]+?)\"\s+?accesskey=\"#\">次へ<\/a>/u",$search_source,$listPref) > 0 ){
			$search_url = "http://as.chizumaru.com".$listPref[1][0];
			$next_flg = 'ng';
		}
	}while( $next_flg == 'ng' );

//市区町村名
$listSevenShop = array();
foreach($listPrefSeven as $record){
print $record[1].':'.$record[0]."\n";
usleep(150000);
	$search_url = "http://as.chizumaru.com/famima/m/".$record[0];
print $search_url."\n";
	$search_source = getAccessSource($CON_USLEEP,$search_url);
	$listAddr = array();
	if( preg_match("/<h1><span[\s\S.]+?>●([\s\S.]+?)<\/span><\/h1>/u",$search_source,$listShop) > 0 ){
		$listAddr[] = trim($listShop[1]);
	}
	if( preg_match("/<span[\s\S.]+?>●住所<\/span><br\s+?\/>[\s\S]*?〒[0-9\-]*?<br\s+?\/>[\s\S]*?([\s\S.]+?)<br\s+?\/>/u",$search_source,$listShop) > 0 ){
		$listAddr[] = trim($listShop[1]);
	}
	$listSevenShop[] = $listAddr;
}

	$fp = fopen($SAVE_FILE.$key.".txt","w");
	foreach($listSevenShop as $index => $record){
print $record[0]."|".$record[1]."\n";
		fwrite($fp,$city.",".$record[0].",".$record[1]."\n");
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