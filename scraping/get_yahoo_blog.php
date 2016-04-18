<?php

$LIMIT=10;

$listStr=array(
	"CSSデザイン",
	"CSSカスタマイズ",
	"CSS編集",
	"画像加工",
	"スキン変更",
	"スキンデザイン",
	"スキンカスタマイズ",
	"スキンチェンジ",
	"Web制作",
	"Web開発",
	"ホームページ制作",
	"ホームページ作成",
	"アプリ開発",
	"javascript",
	"プログラミング",
);
$join_str = implode("|",$listStr);


mb_regex_encoding("UTF-8");


$genre_data = @file_get_contents("http://blogs.yahoo.co.jp/FRONT/cat.html");
$genre_data = mb_convert_encoding($genre_data,"UTF-8","EUC-JP");
preg_match_all("/<a\s+?href=\"http:\/\/rd\.yahoo\.co\.jp\/blog\/category\/\*http:\/\/blogs\.yahoo\.co\.jp\/DIRECTORY\/cat\.html\?cid=([0-9]+?)\">(.*?)<\/a>/u",$genre_data,$listGenre);

$fp = fopen("データ保存ファイル名","w");
foreach($listGenre[1] as $genre_index => $genre){
print $listGenre[2][$genre_index]."\n";
	$listCount = 1;
	do{
		usleep(100000);
		$blog_data = @file_get_contents('http://rd.yahoo.co.jp/blog/category/*http://blogs.yahoo.co.jp/DIRECTORY/cat.html?cid='.$genre.'&page='.$listCount);
		$blog_data = mb_convert_encoding($blog_data,"UTF-8","EUC-JP");
		preg_match_all("/<dt><a\s+?href=\"(http:\/\/rd\.yahoo\.co\.jp\/blog\/category\/list\/title\/p{$listCount}\/\*http:\/\/blogs\.yahoo\.co\.jp\/.*?\/.*?\.html)\">(.*?)<\/a><\/dt>/u",$blog_data,$matches);
		foreach($matches[1] as $index => $url){
			if( mb_eregi("{$join_str}",$matches[2][$index]) ){
print $matches[2][$index]."\n";
				fwrite($fp,$matches[2][$index].",".$url."\n");
				print $matches[2][$index].",".$url."\n";
			}
		}
print "page:".$listCount."\n";
		if( $listCount >= $LIMIT )
			break;
		$listCount++;
		$err_flg = preg_match("/<a\s+?href=\"http:\/\/blogs\.yahoo\.co\.jp\/DIRECTORY\/cat\.html\?page=".$listCount."&cid=".$genre."\">次のページ<\/a>/u",$blog_data);
	}while( $err_flg > 0 );
}
fclose($fp);

?>