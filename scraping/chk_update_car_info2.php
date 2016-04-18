<?php
$CON_USLEEP		 = 1000000;
$listCarInfo = array(
	'http://www.suzuki.co.jp/release/a/2014/index.html',
	'http://www.chevrolet.co.jp/',
	'http://www.cadillac.co.jp/',
	'http://www.chrysler.co.jp/news/',
	'http://www.jeep-japan.com/news/',
	'http://www.bentleymotors.jp/Corporate/display.aspx?id=41&c_id=103',
	'http://www.cornesmotor.com/news/list',
	'http://www.jaguar.co.jp/news/index.html',
	'http://www.landrover.com/jp/ja/lr/about-land-rover/news-overview/',
	'http://www.lotus-cars.jp/news/',
	'http://www.audi.co.jp/jp/brand/ja/company/news.html',
	'http://www.porsche.com/japan/jp/aboutporsche/pressreleases/pj/',
	'http://www.mercedes-benz.jp/news/release/',
	'http://web.peugeot.co.jp/press/',
	'http://www.volvocars.com/jp/top/about/news-events/pages/default.aspx',
	'http://www.citroen.jp/news/#news-products',

/*	取得がやや困難
	'http://www.daihatsu.co.jp/news/index.htm',
	'http://www.hummer.co.jp/',
	'http://www.ford.co.jp/special-offer',
	'http://www.maserati.co.jp/maserati/jp/ja/index/maserati/news-events/',
	'http://www.tokyo.mclaren.com/news-and-gallery/news_events',
	'http://carlsson.co.jp/news/?cat=3',
	'http://www.jeep-japan.com/news/',
	'http://www.bentleymotors.jp/Corporate/display.aspx?id=41&c_id=103',
	'http://www.cornesmotor.com/news/list',
	'http://www.lotus-cars.jp/news/',
	'http://www.mercedes-benz.jp/news/release/' => "<p\s+?class=['\"]date['\"]>(.+?)<\/p>",
*/
);

//ニュース記事ごとの正規表現
$listTagMatch = array(
/*
	'http://www.suzuki.co.jp/release/a/2014/index.html' => "<div id="information">[\s\S]+?<table[\s\S.]+?>([\s\S.]+?)<\/table>[\s\S]+?<\/div>",
	'http://www.chevrolet.co.jp/' => "<dt\s+?class=['\"]new['\"]>([\s\S.]+?)<\/dt>[\s\S.]*?<dd><a\s+?[\s\S.]+?>([\s\S.]*?)<\/a><\/dd>",
	'http://www.cadillac.co.jp/' => "<div\sclass=['\"]inner['\"]>[\s\S]*?<div\s+?class=['\"]date['\"]>([\s\S.]+?)<\/div>([\s\S.]+?)<\/div>",
	'http://www.chrysler.co.jp/news/' => "<div\s+?class=['\"]news_row['\"]>[\s\S]+?<div\sclass=['\"]row_left['\"]>([\s\S.]+?)<br\/>([\s\S.]+?)<\/div>[\s\S]*?<div\s+?class=['\"]row_right['\"]>[\s\S.]+?<\/div>[\s\S]*?<\/div>",
	'http://www.jaguar.co.jp/news/index.html' => "<div\s+?class=['\"]content['\"]>[\s\S]*?<span\s+?class=['\"]timestamp['\"]>([\s\S.]+?)<\/span>([\s\S.]+?)<\/div>",
	'http://www.landrover.com/jp/ja/lr/about-land-rover/news-overview/' => "<li>[\s\S]*?<strong>([\s\S.]+?年[\s\S.]+?月[\s\S.]+?日)<\/strong>([\s\S.]+?)<\/li>",
	'http://www.audi.co.jp/jp/brand/ja/company/news.html' => "<article\s+?class=['\"]news['\"]\s+?data\-component=['\"]news_list_item['\"]\s+?role=['\"]article['\"]>[\s\S]*?<header>[\s\S]*?<time\s+?datetime=['\"][\s\S.]+?['\"]\s+?pubdate=['\"]['\"]>([\s\S.]+?)<\/time>([\s\S.]+?)<\/article>",
	'http://www.porsche.com/japan/jp/aboutporsche/pressreleases/pj/' => "<tr>[\s\S]*?<td>[\s\S.]+?<\/td>[\s\S]*?<td>(\d{4}\/\d{1,2}\/\d{1,2})<\/td>[\s\S]*?<\/tr>",
	'http://web.peugeot.co.jp/press/' => "<div\s+?class=['\"]release\-contents['\"]>[\s\S]*?<span\s+?class=['\"]release\-date['\"]>([\s\S.]+?)<\/span>[\s\S.]+?<div\s+?class=['\"]release\-summary['\"]>([\s\S.]+?)<\/div>",
	'http://www.volvocars.com/jp/top/about/news-events/pages/default.aspx' => "<div\s+?class=['\"]body\-inner['\"]>[\s\S.]*?<blockquote>([\s\S.]+?)<\/blockquote>[\s\S]*?<\/div>[\s\S.]+?<div\s+?class=['\"]info['\"]>[\s\S]*?<span>([\s\S.]+?)<\/span>[\s\S.]+?<\/div>",
	'http://www.citroen.jp/news/#news-products' => "<tr>[\s\S]*?<th><span>([\s\S.]+?)<\/span><\/th>[\s\S.]+?<p\s+?class=['\"]plain['\"]>([\s\S.]+?)<\/p>",
	'http://www.peugeot.co.jp/product-news01/' => "<div\s+?class=['\"]news_item['\"]>[\s\S.]+?<span\s+?class=['\"]date['\"]>([\s\S.]+?)<\/span>[\s\S.]+?<span\s+?class=['\"]text['\"]>([\s\S.]+?)<\/span>",
	'http://www.renault.jp/information/news.html' => "<li><div\s+?class=['\"]mycarousel_text['\"]\s+?style=['\"][\s\S.]+?['\"]><span\s+?class=['\"]scroll_day['\"]>([\s\S.]+?)<\/span>[\s\S.]+?<span\s+?class=['\"]scroll_text['\"]>([\s\S.]+?)<\/span><\/a><\/div><\/li>",
	'http://www.fiat-auto.co.jp/news/' => "<li\s+?class=['\"]ndate['\"]>([\s\S.]+?)<\/li>[\s\S.]+?<li\s+?class=['\"]npost['\"]>([\s\S.]+?)<\/li>",
*/
);


foreach($listTagMatch as $url => $match){
	$html = getAccessSource($CON_USLEEP,$url);
	if( preg_match_all("/{$match}/u",$html,$listUpdate) > 0 ){
print_r($listUpdate);
	}
exit;
}


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
