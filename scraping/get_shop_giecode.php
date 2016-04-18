<?php

$listShop = @file($argv[1]);
$items		 = array();
$train_id	 = '';
$totalCount	 = 0;

foreach($listShop as $shop){
	if ($shop != '') {
		usleep(2000000);
		if (($n = getPointsV3_all($shop, &$items)) <= 0)
			$errmsg .= "$station:この住所では検索できません\n";
	}
}

mb_regex_encoding("UTF-8");

$fp = fopen("データ保存ファイル名","w");
foreach ($items as $index => $record){
	foreach($record as $station_info){
		$totalCount++;
		fwrite($fp,mb_convert_encoding($station_info,"SJIS","UTF-8")."\n");
		$html .="$station_info\n";
	}
}
fclose($fp);


/**
 * PHP5かどうか検査する
 * @return	bool TRUE:PHP5である／FALSE:それ以外のバージョン
*/
function isphp5() {
	return preg_match('/^5/', phpversion()) == 0 ? FALSE : TRUE;
}

/**
 * 指定したパラメータを取り出す
 * @param	string $key  パラメータ名（省略不可）
 * @param	bool   $auto TRUE＝自動コード変換あり／FALSE＝なし（省略時：TRUE）
 * @param	mixed  $def  初期値（省略時：空文字）
 * @return	string パラメータ／NULL＝パラメータ無し
*/
function getParam($key, $auto=TRUE, $def='') {
	if (isset($_GET[$key]))			$param = $_GET[$key];
	else if (isset($_POST[$key]))	$param = $_POST[$key];
	else							$param = $def;
	if ($auto)	$param = mb_convert_encoding($param, INTERNAL_ENCODING, 'auto');
	return $param;
}

/**
 * GoogleMaps API GeoCode API(V3) のURLを取得する
 * @param	string $query 検索キーワード
 * @return	string URL URL
*/
function getURL_GeoCodeAPI_V3($query) {
//	return "http://maps.googleapis.com/maps/api/geocode/xml?language=ja&address=" . urlencode($query) . "&sensor=false&components=country:JP|";
//	return "http://maps.google.com/maps/api/geocode/xml?language=ja&address=" . urlencode($query) . "&sensor=false";
	return "http://www.geocoding.jp/api/?q=".urlencode(trim($query))."&v=1.1&sensor=false";
}

/**
 * 指定XMLファイルを読み込んでDOMを返す
 * @param	string $xml XMLファイル名
 * @return	object DOMオブジェクト／NULL 失敗
*/
function read_xml($xml) {
//	if (isphp5())	return NULL;
	if (($fp = fopen($xml, 'r')) == FALSE)	return NULL;

	//いったん変数に読み込む
	$str = fgets($fp);
	$str = preg_replace('/UTF-8/', 'utf-8', $str);

	while (! feof($fp)) {
		$str = $str . fgets($fp);
	}
	fclose($fp);

	//DOMを返す
	$dom = domxml_open_mem($str);
	if ($dom == NULL) {
		echo "\n>Error while parsing the document - " . $xml . "\n";
		exit(1);
	}

	return $dom;
}

/**
 * Google Geocoding API V3 を用いて住所・駅名の緯度・経度を求める
 * @param	string $query 検索キーワード
 * @param	array  $items 情報を格納する配列
 * @return	int ヒットした施設数
*/
function getPointsV3_all($query, &$items) {

	list($dammy,$shop_name,$shop_addr) = explode(",",$query);
	$url = getURL_GeoCodeAPI_V3($shop_addr);			//リクエストURL
	$n = 0;

//PHP4用; DOM XML利用
	if (isphp5() == FALSE) {
		if (($dom = read_xml($url)) == NULL)	return FALSE;
		$gr = $dom->get_elements_by_tagname('GeocodeResponse');
		//レスポンス・チェック
		$res  = $gr[0]->get_elements_by_tagname('status');
		if (preg_match("/ok/i", $res[0]->get_content()) == 0)	return 0;
		//位置情報
		$res = $gr[0]->get_elements_by_tagname('result');
		foreach ($res as $val) {
			$geo = $val->get_elements_by_tagname('geometry');
			$loc = $geo[0]->get_elements_by_tagname('location');
			$lat = $loc[0]->get_elements_by_tagname('lat');
			$lng = $loc[0]->get_elements_by_tagname('lng');
//			$addr = $val->get_elements_by_tagname('formatted_address');
print $shop_name.",".$shop_addr.",".$element->geometry->location->lat.",".$element->geometry->location->lng.",".$element->geometry->location->lat_dms.",".$element->geometry->location->lng_dms."\n";
			$items[count($items)][$n] = $shop_name.",".$shop_addr.",".$element->geometry->location->lat.",".$element->geometry->location->lng.",".$element->geometry->location->lat_dms.",".$element->geometry->location->lng_dms;
			$n++;
		}
//PHP5用; SimpleXML利用
	} else {
		$res = simplexml_load_file($url);
print_r($res);
/*
		$addr = $res->google_maps;
		foreach($res->coordinate as $elements){
			if( mb_ereg("{$sation}駅?",$addr) > 0 )
				$items[count($items)][$n] = $addr.",".$sation.",".$train_id.",".$elements->lat.",".$elements->lng;
			$n++;
*/
		//レスポンス・チェック
		if (preg_match("/ok/i", $res->status) == 0) return 0;
		$exist_flg = 'ng';
		foreach ($res->result as $element) {
			$addr = $element->formatted_address;
			$exist_flg = 'ok';
print $shop_name.",".$shop_addr.",".$element->geometry->location->lat.",".$element->geometry->location->lng.",".$element->geometry->location->lat_dms.",".$element->geometry->location->lng_dms."\n";
			$items[count($items)][$n] = $shop_name.",".$shop_addr.",".$element->geometry->location->lat.",".$element->geometry->location->lng.",".$element->geometry->location->lat_dms.",".$element->geometry->location->lng_dms;
			$n++;
		}
		if( $exist_flg == 'ng' ){
			$n++;
print "データなし";
			$items[count($items)][$n] = "$shop_name,$shop_addr,,,,\n";
		}
	}
	return $n;
}




?>