<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "パスを追加してください");
require_once('mail.php');

$listCarInfo = array(
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
*/
);

//ニュース記事ごとの正規表現
$listTagMatch = array(

	//各メーカー
	array(array('http://www.alpina.co.jp/information/news/news2014.html'),1,array(2,'http://www.alpina.co.jp/information/news/'),3,"<div\s+?class=[\"']article_information_list[\"']>[\s\S]*?<h5>(\d{4}\.\d{1,2}\.\d{1,2})<\/h5>[\s\S]*?<a\s+?href=[\"'](.+?)[\"']>([\s\S.]+?)<\/a>[\s\S]*?<\/div>"),
	array(array('http://www.smart-j.com/find/news/'),2,array(1,'http://www.smart-j.com/find/news/'),3,"<dt\s+?class=[\"']PadTop10[\"']><a\s+?href=[\"'](.+?)[\"'][\s\S.]+?>(\d{4}年\d{1,2}月\d{1,2}日)（JP）<img\s+?src=[\"'].+?[\"'][\s\S.]+?\/><\/a><\/dt>[\s\S]*?<dd>([\s\S.]+?)<\/dd>"),
	array(array('http://www.mercedes-benz.jp/news/release/'),1,array(3,'http://www.mercedes-benz.jp/'),2,"<div\s+?class=[\"'][\s\S.]+?[\"']>[\s\S]*?<p\s+?class=[\"']date[\"']>(\d{4}年\d{1,2}月\d{1,2}日)<\/p>[\s\S]*?<p\s+?class=[\"']category[\"']><span>製品情報<\/span><\/p>[\s\S]*?<div\s+?class=[\"']wrapBox[\"']>[\s\S]*?<div\s+?class=[\"']box[\"']>[\s\S]*?<p\s+?class=[\"']text[\"']>([\s\S.]+?)<\/p>[\s\S]*?<p\s+?class=[\"']pressLink[\"']><a\s+?href=[\"'](.+?)[\"'][\s\S.]+?>[\s\S.]+?<\/a><\/p>"),
	array(array('http://www.suzuki.co.jp/release/a/2014/index.html'),1,array(2,'http://www.suzuki.co.jp/'),3,"<tr>[\s\S.]*?<th>([\s\S.]*?)<\/th>[\s\S.]*?<td><a\s+?href=['\"](.+?)['\"]>([\s\S.]*?)<\/a><\/td>[\s\S.]*?<\/tr>"),
	array(array('http://www.chrysler.co.jp/news/'),1,array(3,'http://www.chrysler.co.jp/'),2,"<div\s+?class=['\"]news_row['\"]>[\s\S]+?<div\sclass=['\"]row_left['\"]>([\s\S.]+?)<br\/>[\s\S.]+?<br\s*?\/><br\/>([\s\S.]+?)<br\s*?\/><br\/>[\s\S]*?<span\s+?class=['\"]blueLink['\"]><a\s+?href=['\"](.+?)['\"][\s\S.]+?>[\s\S.]+?<\/a><\/span>[\s\S.]+?<\/div>[\s\S]*?<div\s+?class=['\"]row_right['\"]>[\s\S.]+?<\/div>[\s\S]*?<\/div>"),
	array(array('http://www.chevrolet.co.jp/'),1,array(2,'http://www.chevrolet.co.jp/'),3,"<dt\s+?class=['\"]new['\"]>([\s\S.]+?)<\/dt>[\s\S.]*?<dd><a\s+?href=['\"](.+?)['\"][\s\S.]*?><em>[\s\S.]*?<\/em><br\s+?\/>([\s\S.]*?)<\/a><\/dd>"),
	array(array('http://www.cadillac.co.jp/'),1,array(2,'http://www.cadillac.co.jp/'),3,"<div\sclass=['\"]inner['\"]>[\s\S]*?<div\s+?class=['\"]date['\"]>([\s\S.]+?)<\/div>[\s\S]*?<h3><a\s+?href=['\"](.+?)['\"][\s\S.]*?>[\s\S.]+?<\/a><\/h3>([\s\S.]+?)<\/div>"),
	array(array('http://www.jaguar.co.jp/news/index.html'),1,array(2,'http://www.jaguar.co.jp/'),3,"<div\s+?class=['\"]content['\"]>[\s\S]*?<span\s+?class=['\"]timestamp['\"]>([\s\S.]+?)<\/span>[\s\S]*?<h3\s+?class=['\"]article\-title['\"]><a\s+?href=['\"](.+?)['\"]>[\s\S.]+?<p\s+?class=['\"]summary['\"]>([\s\S.]+?)<\/p><\/div>"),
	array(array('http://www.landrover.com/jp/ja/lr/about-land-rover/news-overview/'),1,array(3,'http://www.landrover.com/'),2,"<li>[\s\S]*?<strong>([\s\S.]+?年[\s\S.]+?月[\s\S.]+?日)<\/strong><h3>[\s\S.]+?<\/h3>([\s\S.]+?)<\/p>[\s\S.]+?<\/li>[\s\S]*?<li><a\s+?href=['\"](.+?)['\"]>.+?<\/a>"),
	array(array('http://www.audi.co.jp/jp/brand/ja/company/news.html'),1,array(3,'http://www.audi.co.jp/'),2,"<article\s+?class=['\"]news['\"]\s+?data\-component=['\"]news_list_item['\"]\s+?role=['\"]article['\"]>[\s\S]*?<header>[\s\S]*?<time\s+?datetime=['\"][\s\S.]+?['\"]\s+?pubdate=['\"]['\"]>([\s\S.]+?)<\/time>[\s\S]*?<h1>[\s\S.]+?<\/h1>[\s\S]*?<\/header><p>([\s\S.]+?)<\/p><a[\s\S.]+?href=['\"](.+?)['\"][\s\S.]+?>[\s\S.]+?<\/a>[\s\S]*?<\/article>"),
	array(array('http://www.porsche.com/japan/jp/aboutporsche/pressreleases/pj/'),3,array(1,'http://www.porsche.com/japan/jp/aboutporsche/pressreleases/pj/'),2,"<tr>[\s\S]*?<td>[\s\S]*?<a\s+?href=['\"](.+?)['\"]>([\s\S.]+?)<\/a><\/td>[\s\S]*?<td>(\d{4}\/\d{1,2}\/\d{1,2})<\/td>[\s\S]*?<\/tr>"),
	array(array('http://web.peugeot.co.jp/press/'),1,array(2,''),3,"<div\s+?class=['\"]release\-contents['\"]>[\s\S]*?<span\s+?class=['\"]release\-date['\"]>([\s\S.]+?)<\/span>[\s\S]*?<h3\s+?class=['\"]release\-title['\"]>[\s\S]*?<a\s+?href=['\"](.+?)['\"][\s\S.]+?>[\s\S.]+?<div\s+?class=['\"]release\-summary['\"]><p>([\s\S.]+?)<\/p>[\s\S.]+?<\/div>"),
	array(array('http://www.volvocars.com/jp/top/about/news-events/pages/default.aspx'),3,array(2,'http://www.volvocars.com/'),1,"<div\s+?class=['\"]body\-inner['\"]>[\s\S.]*?<blockquote>([\s\S.]+?)<\/blockquote><\/div><div\s+?class=['\"]moreinfolink['\"]><a\s+?href=['\"](.+?)['\"]>詳細を読む<\/a>[\s\S.]+?<div\s+?class=['\"]info['\"]>[\s\S]*?<span>([\s\S.]+?)<\/span>[\s\S.]+?<\/div>"),
	array(array('http://www.citroen.jp/news/#news-products'),1,array(2,'http://www.citroen.jp/'),3,"<tr>[\s\S]*?<th><span>([\s\S.]+?)<\/span><\/th>[\s\S.]+?<li><a\s+?href=['\"](.+?)['\"][\s\S.]*?>[\s\S.]+?<\/a><\/li>[\s\S]+?<\/ul>[\s\S]+?<p\s+?class=['\"]plain['\"]>([\s\S.]+?)<\/p>"),
	array(array('http://www.peugeot.co.jp/product-news01/'),1,array(3,'http://www.peugeot.co.jp/'),2,"<div\s+?class=['\"]news_item['\"]>[\s\S.]+?<span\s+?class=['\"]date['\"]>([\s\S.]+?)<\/span>[\s\S.]+?<span\s+?class=['\"]text['\"]>([\s\S.]+?)<\/span>[\s\S]*?<a\s+?href=['\"](.+?)['\"][\s\S]*?>.+?<\/a>"),
	array(array('http://www.renault.jp/information/news.html'),1,array(2,'http://www.renault.jp/'),3,"<li><div\s+?class=['\"]mycarousel_text['\"]\s+?style=['\"][\s\S.]+?['\"]><span\s+?class=['\"]scroll_day['\"]>([\s\S.]+?)<\/span>[\s\S.]+?<a\s+?href=[\"'](.+?)[\"'][\s\S]*?><span\s+?class=['\"]scroll_text['\"]>([\s\S.]+?)<\/span><\/a><\/div><\/li>"),
	array(array('http://www.fiat-auto.co.jp/news/'),1,array(3,''),2,"<li\s+?class=['\"]ndate['\"]>([\s\S.]+?)<\/li>[\s\S.]+?<li\s+?class=['\"]npost['\"]>([\s\S.]+?)<\/il>[\s\S]*?<li\s+?class=\"buttons\"><a\s+?href=\"(.+?)\"[\s\S.]+?><span>続きはこちら<\/span><\/a><\/li>"),

	//情報サイト
	array(array('http://response.jp/category/newmodel/latest/?page=1','http://response.jp/category/newmodel/latest/?page=2'),2,array(1,'http://response.jp/'),3,"<li\s+?class=['\"]list['\"]><a\s+?class=['\"][\s\S.]+?['\"]\s+?href=['\"](.+?)['\"]\s+?data\-action=['\"].+?['\"]>[\s\S]*?<div\s+?class=['\"]figure['\"]>[\s\S]*?<img[\s\S.]+?>[\s\S]*?<\/div>[\s\S]*?<div\s+?class=['\"]figcaption['\"]>[\s\S]*?<span\s+?class=['\"]category['\"]>[\s\S.]+?<\/span>[\s\S]*?<span\s+?class=['\"]date['\"]\s+?data\-date=['\"][\s\S.]+?['\"]>([\s\S.]+?)<\/span>[\s\S]*?<h3\s+?class=['\"]title['\"]>[\s\S.]+?<\/h3>[\s\S]*?<p\s+?class=['\"]summary['\"]>([\s\S.]+?)<\/p>"),
	array(array('http://carinfoj.blog.fc2.com/'),1,array(3,''),2,"<ul\s+?class=['\"]entry_date['\"]>[\s\S]*?<li>(\d{4}\/\d{2}\/\d{2})<\/li>[\s\S]*?<\/ul>[\s\S.]*?<div\s+?class=['\"]entry_discription['\"]>([\s\S.]+?)<\/div>[\s\S]*?<p\s+?class=\"entry_more\"><a\s+?href=\"(http:\/\/carinfoj\.blog\.fc2\.com\/.*?)\"[\s\S.]+?>続きを読む<\/a><\/p>"),
	array(array('http://carview.yahoo.co.jp/news/?page=1','http://carview.yahoo.co.jp/news/?page=2','http://carview.yahoo.co.jp/news/?page=3','http://carview.yahoo.co.jp/news/?page=4','http://carview.yahoo.co.jp/news/?page=5'),3,array(1,''),2,"<li>[\s\S]*?<a\s+?href=['\"](http:\/\/carview\.yahoo\.co\.jp\/news\/.*?)['\"]>[\s\S]*?<img\s+?src=['\"].+?['\"]\s+?[\s\S.]+?>([\s\S.]+?)<span\s+?class=['\"]newicon['\"]><\/span><\/a>[\s\S]*?<br>[\s\S]*?<span\s+?class=['\"]date['\"]>(\d{4}\.\d{1,2}\.\d{1,2})[\s\S.]+?<\/span>[\s\S]*?<\/li>"),
	array(array('http://openers.jp/car/exclusive_auto_collection/'),1,array(2,'http://openers.jp/'),3,"<span\s+?class=['\"]txtDate['\"]>(\d{4}\.\d{1,2}\.\d{1,2})<\/span><br>[\s\S]*?<a\s+?class=['\"]link12lh15b['\"]\s+?href=['\"](\/car\/exclusive_auto_collection\/.+?)['\"]>([\s\S.]+?)<\/a>"),

);
$CON_USLEEP				 = 1000000;
$LIST_NEW_CAR_WORD		 = "発売|特別仕様車|限定車|デビュー";
$CAR_UPDATE_FLG			 = false;
$LIST_DATA				 = array();
$LIST_NEW_UPDATE		 = array();
$SAVE_LAST_UPDATE_FILE	 = 'データ保存ファイル名';
$LIST_UPDATE			 = @file($SAVE_LAST_UPDATE_FILE);
$DEL_TAG				 = "<br\s*?\/?>|<!\-\-[\s\S.]+?\-\->";

mb_regex_encoding("UTF-8");

//ニュース記事をチェック
$index_count = 0;
foreach($listTagMatch as $car_info){
	$new_flg = 'ng';
	foreach($car_info[0] as $url){
print $url."\n";
		$html = getAccessSource($CON_USLEEP,$url);
		$html = mb_convert_encoding($html,"UTF8","auto");
		//ニュース記事抽出
		if( preg_match_all("/{$car_info[4]}/u",$html,$listUpdate) > 0 ){
			$last_update = trim($LIST_UPDATE[$index_count]);
//print_r($listUpdate);
			foreach($listUpdate[$car_info[3]] as $index => $description){
				$hit_flg = mb_ereg($LIST_NEW_CAR_WORD,$description);
				if( $hit_flg == 1 ){
//print trim($listUpdate[$car_info[1]][$index]).":".htmlspecialchars_decode($car_info[2][1].$listUpdate[$car_info[2][0]][$index]).":".$listUpdate[$car_info[3]][$index]."\n";
					$update = getMktime(trim($listUpdate[$car_info[1]][$index]));
					$diff_time = $update - $last_update;
//print $update;
					if( $diff_time > 0 ){
						if( $new_flg == 'ng' ){
print "update1:$index_count\n";
							$LIST_NEW_UPDATE[] = $update;
						}
						$disp_update = preg_replace("/[\s\S.]*?(\d{1,2}\.\d{1,2}\.\d{4})[\s\S.]*?/u","$1",trim($listUpdate[$car_info[1]][$index]));
						$description = preg_replace("/$DEL_TAG/u","",trim($listUpdate[$car_info[3]][$index]));
						$description = preg_replace("/\r\n\r\n/u","\r\n",$description);
						$description = preg_replace("/^[\s]+?/u","",$description);
						$description = preg_replace("/[\s]+?$/u","",$description);
						$description = preg_replace("/[\s]+?/u","",$description);
						if( preg_match("/https?:\/\/.+?/u",$listUpdate[$car_info[2][0]][$index]) == 0 ){
							$listUpdate[$car_info[2][0]][$index] = preg_replace("/^\//u","",$listUpdate[$car_info[2][0]][$index]);
							$link = htmlspecialchars_decode(trim($car_info[2][1].$listUpdate[$car_info[2][0]][$index]));
						}else{
							$link = htmlspecialchars_decode(trim($listUpdate[$car_info[2][0]][$index]));
						}
						$LIST_DATA[] = array($disp_update,$link,$description);
						$CAR_UPDATE_FLG = true;
						$new_flg = 'ok';
						continue;
					}
					break;
				}
			}
		}
	}
	if( $new_flg == 'ng' ){
print "update2:$index_count\n";
		$LIST_NEW_UPDATE[] = $last_update;
	}
	$index_count++;
}

$fp = fopen($SAVE_LAST_UPDATE_FILE,'w');
foreach($LIST_NEW_UPDATE as $update){
	fwrite($fp,$update."\n");
}
fclose($fp);

//車情報が更新されたら配信
if( $CAR_UPDATE_FLG ){
	$EMAIL_BODY =<<<__END_OF_MAIL__
To：担当者


__END_OF_MAIL__;

	foreach($LIST_DATA as $record){
		$CAR_DATA .=<<<__END_OF_MAIL__
■$record[0]
$record[2]
$record[1]


__END_OF_MAIL__;
	}

	$LIST_TEMP_STR = array();
	foreach($LIST_EMAIL as $mail){
		sendAttachMail( $FROMMAIL, $mail, $SUBJECT, $EMAIL_BODY.$CAR_DATA, $LIST_TEMP_STR);
	}

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
//	$content = mb_ereg_replace("\r","",$content);
//	$content = mb_ereg_replace("\n","",$content);
	return $content;
}

function getMktime($date){
	$match_flg = preg_match("/(\d{4})(年|\.|\/)[\s\S]*?(\d{1,2})(月|\.|\/)[\s\S]*?(\d{1,2})(日)?/",$date,$match);
	if( $match_flg > 0 ){
//print_r($match);
		return mktime(0,0,0,$match[3],$match[5],$match[1]);
	}
	$match_flg = preg_match("/[\s\S.]*?(\d{1,2})\.(\d{1,2})\.(\d{4})[\s\S.]*?/",$date,$match);
	if( $match_flg > 0 ){
//print_r($match);
		return mktime(0,0,0,$match[1],$match[2],$match[3]);
	}
	return strtotime($date);
//exit;
}


?>
