<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/home/ftpuser/gente_car_info");
require_once('mail.php');

$listCarRss = array(
	'http://www2.toyota.co.jp/toyotajp/toyota/jp/carinformation_rss.xml',															//トヨタ
	'http://www2.toyota.co.jp/lexusjp/lexus/jp/news_rss.xml',																		//レクサス
	'http://www.nissan.co.jp/NEWS/rss.xml',																							//日産
	'http://www.mitsubishi-motors.co.jp/component/documents/news_message.xml',														//三菱
	'http://www.honda.co.jp/rss/auto.xml',																							//ホンダ
	'http://www.mazda.co.jp/rss/carsiteinfo.xml',																					//マツダ
	'http://www.subaru.jp/rss/news.xml',																							//スバル
	'http://www.subaru.jp/rss/upinfo.xml',																							//スバル
	'http://www.ferrari.com/japanese/_layouts/ferrari/Ferrari_RSSModule.ashx?Category=news&Keyword=|GT|',							//フェラーリ
	'http://www.mini.jp/dyn/news_rss?&root_path=/news_events&locale=jp/jp&pagecontext=http://www.mini.jp&category=models',			//ミニ
	'http://www.mini.jp/dyn/news_rss?&root_path=/news_events&locale=jp/jp&pagecontext=http://www.mini.jp&category=press_release',	//ミニ
	'http://web.volkswagen.co.jp/rss/pc.xml',																						//フォルクスワーゲン
	'http://news.bmw.co.jp/rss.xml',																								//BMW
	'http://news.bmw.co.jp/newcar/rss.xml',																							//BMW
	'http://news.bmw.co.jp/brand/rss.xml'																							//BMW
);

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
	'http://www.peugeot.co.jp/peugeot-topics/?top_uslide_what',
	'http://www.renault.jp/information/news.html',
	'http://www.fiat-auto.co.jp/news/'
*/
);

$LIST_NEW_CAR_WORD		 = "発売|特別仕様車|発表|限定";
$CAR_UPDATE_FLG			 = false;
$LIST_DATA				 = array();
$LIST_NEW_UPDATE		 = array();
$SAVE_LAST_UPDATE_FILE	 = 'データ保存ファイル名';
$LIST_UPDATE			 = @file($SAVE_LAST_UPDATE_FILE);
print_r($LIST_UPDATE);
mb_regex_encoding("UTF-8");

foreach($listCarRss as $index => $rss){
	$none_update_flg = true;
	$xml = @simplexml_load_file ($rss);
	$last_update = '';
	$old_update = trim($LIST_UPDATE[$index]);
	$noupdate_flg = true;
	$update_flg = true;
	if(empty($xml->channel->item))
		$xml_data = $xml->item;
	else
		$xml_data = $xml->channel->item;
	if($xml != ''){
		foreach ($xml_data as $entry) {
			$pubdate = $entry->pubDate;
			if( $pubdate == "" ){
				$pubdate = $entry->pubdate;
				if( $pubdate == "" ){
					$nameSpaces = $xml->getNamespaces(true);
					$pubdate = $entry->children($nameSpaces['dc'])->date;
				}
			}
			$pubdate = strtotime($pubdate);
			$diff_news_update = $pubdate - $old_update;
			if( $diff_news_update > 0 ){
				if( $last_update == '' ){
//print "1:".$diff_news_update.":".$old_update.":".$update.":".$entry->title."\n";
					$last_update = $pubdate;
					$LIST_NEW_UPDATE[] = $last_update;
print "test1:".$index.":".$rss."\n";
				}
				$description = $entry->description;
				//文中に特別仕様車が含まれているかチェック
				$hit_flg = mb_ereg($LIST_NEW_CAR_WORD,$description);
				if( $hit_flg == 1 ){
					$CAR_UPDATE_FLG = true;
					$update = date("Y年m月d日 H:i:s", $pubdate);
//print "2:".$diff_news_update.":".$old_update.":".$update.":".$entry->title."\n";
					$LIST_DATA[] = array($entry->title,$entry->link,$entry->description,$update,$entry->author);
				}
			//更新されていない
			}else if( $diff_news_update == 0 && $old_update != '' && $noupdate_flg === true ){
//print "no update1:".$rss."\n";
				$noupdate_flg = false;
				break;
			//更新日時がないRSS
			}else if( $pubdate == '' && $update_flg === true ){
				$update_flg = false;
			}
		}
	//データ取得失敗
	}else{
//print "no data:".$rss."\n";
		$update_flg = false;
	}
	if( !$update_flg ){
		$LIST_NEW_UPDATE[] = '';
print "test2:".$index.":".$rss."\n";
	}
	if( !$noupdate_flg ){
print "test3:".$index.":".$rss."\n";
		$LIST_NEW_UPDATE[] = $old_update;
	}
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
$record[3]


__END_OF_MAIL__;
	}

	$LIST_TEMP_STR = array();
	foreach($LIST_EMAIL as $mail){
		sendAttachMail( $FROMMAIL, $mail, $SUBJECT, $EMAIL_BODY.$CAR_DATA, $LIST_TEMP_STR);
	}

}

function last_modified($rss){
	$time = '';
	if( preg_match("/^http:\/\/(.+?)(\/.+?)$/",$rss,$match) > 0 ){
print_r($match);
		$hostname = $match[1];
		$path = $match[2];
	}

	if(!$fp = fsockopen($hostname, 80))
		return -1;
	$out = "HEAD http://{$hostname}{$path} HTTP/1.1\n\n";
	fwrite($fp,$out);
	while (!feof($fp)) {
		$line = fgets($fp,128);
		if(preg_match("/(Expires|Date|Last-Modified):\s*?(.+)/", $line, $regs)){
print_r($regs);
			$time = strtotime($regs[2]);
			fclose($fp);
			return $time;
		}
	}
	fclose($fp);
	return $time;
}


?>