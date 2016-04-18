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


$genre_data = @file_get_contents("http://www.cocolog-nifty.com/");
preg_match_all("/<strong><a\s+?href=\"(http:\/\/squares\.cocolog\-nifty\.com\/genres\/[0-9]+?)\"\s+?class=\"genre\">(.*?)<\/a><\/strong>/u",$genre_data,$listGenre);

$fp = fopen("データ保存ファイル名","w");
foreach($listGenre[1] as $genre_index => $genre){
print $listGenre[2][$genre_index]."\n";
		$blog_data = @file_get_contents($genre);
		preg_match_all("/<a\s+?href=\"(http:\/\/squares\.cocolog\-nifty\.com\/communities\/[0-9]+?)\">(.*?)<\/a>/u",$blog_data,$matches);
		foreach($matches[1] as $index => $url){
print $matches[2][$index]."\n";
			$listCount = 1;
			do{
				$blog_data = @file_get_contents($url."/blogs?page=".$listCount);
				usleep(100000);
				preg_match_all("/<span\s+?class=\"post\-footers\"><a\s+?href=\"(http:\/\/.*?\.cocolog\-nifty\.com\/blog\/)\">(.*?)<\/a><\/span>/u",$blog_data,$matches_body);
				foreach($matches_body[2] as $target_index => $target_title){
					if( mb_eregi("{$join_str}",$target_title) ){
						fwrite($fp,$target_title.",".$matches_body[1][$target_index]."\n");
						print $target_title.",".$matches_body[1][$target_index]."\n";
					}
				}
				$listCount++;
				$pettern_url = preg_replace("/http:\/\/squares\.cocolog\-nifty\.com(.+)$/u","$1",$url);
				$pettern_url = preg_replace("/\//u","\\/",$pettern_url);
				$err_flg = preg_match("/<a\s+?href=\"".$pettern_url."\/blogs\?page=".$listCount."&amp;per_page=[0-9]+?\"\s+?class=\"next_page\"\s+?rel=\"next\">次へ<\/a>/u",$blog_data);
print "page:".$listCount."\n";
				if( $listCount >= $LIMIT )
					break;
			}while( $err_flg > 0 );
		}
}
fclose($fp);

?>