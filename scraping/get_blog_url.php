<?php

$LIMIT=10;

$listStr=array(
	"CSS",
	"デザイン",
	"カスタマイズ",
	"Web制作",
	"作り方",
	"ホームページ制作",
	"アプリ",
	"開発",
	"スキンチェンジ",
	"javascript",
	"プラグイン",
	"コンテスト",
	"プログラミング",
);
$join_str = implode("|",$listStr);


mb_regex_encoding("UTF-8");

$genre_data = @file_get_contents("http://ranking.ameba.jp/genre/category");
preg_match_all("/<dt><a\s+?href=\"http:\/\/ranking\.ameba\.jp\/gr_(.*?)\">.*?<\/a><\/dt>/u",$genre_data,$listGenre);

$fp = fopen("/home/nishizawa/ameblo_url.txt","w");
foreach($listGenre[1] as $genre){
print $genre."\n";
	$listCount = 1;
	do{
		usleep(100000);
		$blog_data = @file_get_contents('http://ranking.ameba.jp/genre/detail?genreCode='.$genre.'&page='.$listCount);
		preg_match_all("/<dd\s+?class=\"title\"><a\s+?href=\"(http:\/\/ameblo\.jp\/.*?)\">(.*?)<\/a><\/dd>/u",$blog_data,$matches);

		foreach($matches[1] as $index => $url){
			if( mb_eregi("{$join_str}",$matches[2][$index]) ){
				fwrite($fp,$matches[2][$index].",".$url."\n");
				print $matches[2][$index].",".$url."\n";
			}
		}
print "page:".$listCount."\n";
		if( $listCount >= $LIMIT )
			break;
		$listCount++;
		$err_flg = preg_match("/<p\s+?class=\"next\"><a\s+?href=\"http:\/\/ranking\.ameba\.jp\/genre\/detail\?genreCode=".$genre."&page=".$listCount."\">次へ<\/a><\/p>/u",$blog_data);
	}while( $err_flg > 0 );
}
fclose($fp);

?>