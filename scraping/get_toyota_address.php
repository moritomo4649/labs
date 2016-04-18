<?php
$USER_AGENT		 = 'DoCoMo/2.0 P903i';
$CON_USLEEP		 = 2000000;
$SAVE_FILE		 = 'sunks_shop_';
$LIST_PREF_SHOP	 = array(
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=10',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=20',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=21',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=22',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=23',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=24',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=25',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=30',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=31',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=32',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=33',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=34',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=35',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=36',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=44',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=40',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=41',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=42',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=43',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=45',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=46',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=47',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=48',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=49',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=50',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=51',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=52',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=53',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=54',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=55',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=60',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=61',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=62',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=63',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=64',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=70',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=71',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=72',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=74',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=80',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=81',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=82',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=83',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=84',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=85',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=86',
	'http://toyota.jp/service/dealer/spt/search-area?mode=channel&PREFECTURE_CD=87'
);

$LIST_PREF_SHOP = array(
	'http://toyota-dealers.jp/01101/store/store_list.html',
	'http://www.hakodatetoyota.co.jp/store/store_list.html',
	'http://toyota-dealers.jp/11501/store/store_list.html',
	'http://corolla-asahikawa.toyota-dealers.jp/store/store_list.html',
	'http://corolla-dohoku.toyota-dealers.jp/store/store_list.html',
	'http://toyota-dealers.jp/31301/store/store_list.html',
	'http://corolla-kushiro.toyota-dealers.jp/store/store_list.html',
	'http://toyota-dealers.jp/31501/store/store_list.html',
	'http://toyota-dealers.jp/41101/store/store_list.html',
	'http://netz-taisetsu.co.jp/store/store_list.html',
	'http://www.netzdoto.jp/store/store_list.html',
	'http://toyota-dealers.jp/41301/store/store_list.html',
	'http://toyota-dealers.jp/41401/store/store_list.html',
	'http://toyota-dealers.jp/41501/store/store_list.html'
);



mb_regex_encoding('UTF-8');


//都道府県別にURLを収集
/*
$listBrachShop = array();
foreach($LIST_PREF_SHOP as $search_url){
print $search_url."\n";
	$search_source = getAccessSource($CON_USLEEP,$search_url);
//	if( preg_match_all("/\"(search\?CN=.+?&OFFICE_CD=[0-9]+?)\"/",$search_source,$listShop) > 0 ){
	if( preg_match_all("/<th\sclass=\"title\sleft\sstore_list\">[\s\S.]*?<a\shref=\"(.+?)\">.+?<\/a>[\s\S.]*?<\/th>/",$search_source,$listShop) > 0 ){
		foreach($listShop[1] as $index => $branch_url){
//			$listBrachShop[] = 'http://toyota.jp/service/dealer/spt/'.trim($branch_url);
			$listBrachShop[] = trim($branch_url);
		}
	}
	break;
}
*/
$listBrachShop[] = 'http://www.hakodatetoyota.co.jp/store/store_837_837.html';
print_r($listBrachShop);

$listShop = array();
foreach($listBrachShop as $url){
print $url."\n";
	$search_source = getAccessSource($CON_USLEEP,$url);
	if( preg_match("/<p>(.+?)&nbsp;&nbsp;<\/p>/u",$search_source,$listTmpShop) > 0 ){
print_r($listTmpShop);
		$name = $listTmpShop[1][0];
//		$listShop[] = $listTmpShop[1][0];
	}

	if( preg_match("/<td\sclass=\"left\">[\s\S]*?(〒[0-9]{3}\-[0-9]{4}[\s\S]*?.+?[\s\S]*?)<\/td>/u",$search_source,$listTmpShop) > 0 ){
print_r($listTmpShop);
		$address = $listTmpShop[1][0];
	}

	if( preg_match("/<tr><td\swidth=50>U\-Car<\/td><td>：&nbsp;<\/td><td>([0-9]+?)<\/td><\/tr>/u",$search_source,$listTmpShop) > 0 ){
print_r($listTmpShop);
		$tel = $listTmpShop[1][0];
	}

	if( preg_match("/<tr><td\swidth=50>新&nbsp;&nbsp;車<\/td><td>：&nbsp;<\/td><td>([0-9]+?)<\/td><\/tr>/u",$search_source,$listTmpShop) > 0 ){
print_r($listTmpShop);
		$tel = $listTmpShop[1][0];
	}

	if( preg_match("/<span\scolor=.+?\sstyle=\"[\s\S.]+?\">\s+?営業時間<\/span><\/TD>\r\n<TD\sstyle=\"[\s\S.]+?\"\sclass=x125><span\scolor=.+?\sstyle=\"[\s\S.]+?\">(.+?)<\/span>/u",$search_source,$listTmpShop) > 0 ){
print_r($listTmpShop);
		$tel = $listTmpShop[1][0];
	}
exit;
/*
	$fp = fopen($SAVE_FILE.$key.".txt","w");
	foreach($listBrachShop as $index => $record){
print $record[0]."|".$record[1]."\n";
		fwrite($fp,$record[0].",".$record[1]."\n");
	}
	fclose($fp);
*/
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
	//$content = mb_ereg_replace("\r","",$content);
	//$content = mb_ereg_replace("\n","",$content);
	return $content;
}


?>