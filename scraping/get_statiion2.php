<?php

mb_regex_encoding("UTF-8");

$listStationName = array();
$station = @file_get_contents('http://www.ekikara.jp/newdata/line/1103011.htm');
$station = mb_convert_encoding($station,"UTF-8","SJIS");
preg_match_all("/<span\s+?class=\"textBold\">(.+?)<\/span><\/a>/u",$station,$listStation);
foreach($listStation[1] as $name){
	$name = preg_replace("/^(.+)?\(.+?\)$/u","$1",$name);
print $name."\n";
	$listStationName[] = $name;
}

$fp = fopen("データ保存ファイル名","w");
foreach($listStationName as $name){
	$name = mb_convert_encoding($name,"SJIS","UTF-8");
	fwrite($fp,$name."\n");
}
fclose($fp);

?>