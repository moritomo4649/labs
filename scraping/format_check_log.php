<?php
$USER_AGENT		 = 'DoCoMo/2.0 P903i';
$CON_USLEEP		 = 100000;
$SAVE_FILE		 = 'shop_';
$listShop		 = @file('データファイルまでのパス'.$argv[1]);

$fp = fopen('/home/nishizawa/shop_log/check_log/format_log/'.$argv[1],"w");
foreach($listShop as $shop){
	$encode_shop = mb_convert_encoding($shop,"UTF-8","SJIS");
//	list($dammy,$shop_name,$shop_addr,$geocode) =explode(",",trim($encode_shop));
	list($shop_name,$shop_addr,$geocode) = explode(",",trim($encode_shop));
	if ($geocode == '') {
		usleep(100000);
		$shop = trim($shop);
		$geocode = strAddrToLatLng4($shop_addr);
print trim($shop).$geocode;
		$geocode = mb_convert_encoding($geocode,"SJIS","UTF-8");
		list($shop_name,$shop_addr) = explode(",",trim($shop));
//		list($city,$shop_name,$shop_addr) = explode(",",trim($shop));
		fwrite($fp,"$shop_name,$shop_addr,".$geocode);
//		fwrite($fp,"$city,$shop_name,$shop_addr,".$geocode);
	}
}
fclose($fp);

print "終了\n";
exit;

function strAddrToLatLng( $strAddr )
{
    $strRes = file_get_contents( 
         'http://maps.google.com/maps/api/geocode/json'
        . '?address=' . urlencode( $strAddr )
        . '&sensor=false'
    );
    $aryGeo = json_decode( $strRes, TRUE );
    if ( !isset( $aryGeo['results'][0] ) )
        return '';

    $strLat = (string)$aryGeo['results'][0]['geometry']['location']['lat'];
    $strLng = (string)$aryGeo['results'][0]['geometry']['location']['lng'];
    return ','.$strLat.','.$strLng."\n";
}

function strAddrToLatLng2( $strAddr )
{
//    $strRes = file_get_contents("http://www.geocoding.jp/api/?q=".urlencode($strAddr));
	$strRes = simplexml_load_file("http://www.geocoding.jp/api/?q=".urlencode($strAddr));
//    $aryGeo = json_decode( $strRes, TRUE );
//    if ( !isset( $aryGeo['results'][0] ) )
//        return '';
//    if ( !isset( $aryGeo->coordinate ) )
//        return '';

//    $strLat = (string)$aryGeo['results'][0]['geometry']['location']['lat'];
//    $strLng = (string)$aryGeo['results'][0]['geometry']['location']['lng'];
	foreach($strRes->coordinate as $item){
    $strLat = (string)$item->lat;
    $strLng = (string)$item->lng;
	}
    return ','.$strLat.','.$strLng."\n";
}

function strAddrToLatLng3( $strAddr )
{
	$strRes = simplexml_load_file("http://geoapi.heartrails.com/api/xml?method=suggest&matching=prefix&keyword=".urlencode(mb_convert_encoding($strAddr,'UTF-8')));
print_r($strRes);
    $aryGeo = json_decode( $strRes, TRUE );
    if ( !isset( $aryGeo['results'][0] ) )
        return '';

    $strLat = (string)$aryGeo['results'][0]['geometry']['location']['lat'];
    $strLng = (string)$aryGeo['results'][0]['geometry']['location']['lng'];
    return ','.$strLat.','.$strLng."\n";
}

function strAddrToLatLng4( $strAddr )
{
	$strRes = simplexml_load_file("http://geo.search.olp.yahooapis.jp/OpenLocalPlatform/V1/geoCoder?appid=dj0zaiZpPWViSFhmTUxIVVhhcyZzPWNvbnN1bWVyc2VjcmV0Jng9N2Q-&output=xml&query=".urlencode(mb_convert_encoding($strAddr,'UTF-8')));
	if ( !isset( $strRes->Feature ) ){
		$strRes = simplexml_load_file("http://geo.search.olp.yahooapis.jp/OpenLocalPlatform/V1/geoCoder?appid=dj0zaiZpPWViSFhmTUxIVVhhcyZzPWNvbnN1bWVyc2VjcmV0Jng9N2Q-&output=xml&query=".urlencode(mb_convert_encoding($strAddr.' セブンイレブン','UTF-8')));
		if ( !isset( $strRes->Feature ) )
			return "\n";
	}
	list($strLat,$strLng) = explode(",",$strRes->Feature->Geometry->Coordinates);
	return ",".$strLng.','.$strLat."\n";
}

?>