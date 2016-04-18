<?php

$LIMIT=20;

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


$genre_data = @file_get_contents("http://blog.goo.ne.jp/portal/genre_list");
$genre_data = mb_convert_encoding($genre_data,"UTF-8","EUC-JP");
preg_match_all("/<a\s+?href=\"(\/genre\/[0-9]+?)\">(.*?)<\/a>/u",$genre_data,$listGenre);

$fp = fopen("データ保存ファイル名","w");
foreach($listGenre[1] as $genre_index => $genre){
print $listGenre[2][$genre_index]."\n";
	$listCount = 1;
	do{
print "http://blog.goo.ne.jp".$genre."/?p=".$listCount."\n";
		$blog_data = @file_get_contents("http://blog.goo.ne.jp".$genre."/?p=".$listCount);
		$blog_data = mb_convert_encoding($blog_data,"UTF-8","EUC-JP");
		usleep(100000);
		$blog_data = preg_replace("/\n/u","",$blog_data);
		preg_match_all("/<span\s+?class=\"blg_link\"><a\s+?href=\"(http:\/\/blog\.goo\.ne\.jp\/.*?)\">(.*?)<\/a><\/span>/u",$blog_data,$matches_body);
		foreach($matches_body[2] as $target_index => $target_title){
			if( mb_eregi("{$join_str}",$target_title) ){
				fwrite($fp,$target_title.",".$matches_body[1][$target_index]."\n");
				print $target_title.",".$matches_body[1][$target_index]."\n";
			}
		}
		$listCount++;
		$pettern_genre = preg_replace("/\//u","\\/",$genre);
		$err_flg = preg_match("/<a\s+?id=\"page_link_next\"\s+?href=\"{$pettern_genre}\/\?p={$listCount}\">次へ<\/a>/u",$blog_data);
print "page:".$listCount."\n";
		if( $listCount >= $LIMIT )
			break;
	}while( $err_flg > 0 );
}
fclose($fp);

?>