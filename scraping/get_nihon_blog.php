<?php

$LIMIT=2;

$listSkipUrl = array(
	"overseas.blogmura.com",
	"travel.blogmura.com",
	"localhokkaido.blogmura.com",
	"localeast.blogmura.com",
	"localtokyo.blogmura.com",
	"localkantou.blogmura.com",
	"localchubu.blogmura.com",
	"localkansai.blogmura.com",
	"localwest.blogmura.com",
	"localshikoku.blogmura.com",
	"localkyushu.blogmura.com",
	"localokinawa.blogmura.com",
	"local.blogmura.com",
	"beauty.blogmura.com",
	"diet.blogmura.com",
	"health.blogmura.com",
	"mental.blogmura.com",
	"sick.blogmura.com",
	"care.blogmura.com",
	"maternity.blogmura.com",
	"baby.blogmura.com",
	"family.blogmura.com",
	"food.blogmura.com",
	"gourmet.blogmura.com",
	"sweets.blogmura.com",
	"sake.blogmura.com",
	"house.blogmura.com",
	"interior.blogmura.com",
	"goods.blogmura.com",
	"lifestyle.blogmura.com",
	"dog.blogmura.com",
	"cat.blogmura.com",
	"rabbit.blogmura.com",
	"hamster.blogmura.com",
	"smallanimal.blogmura.com",
	"birds.blogmura.com",
	"aquarium.blogmura.com",
	"pet.blogmura.com",
	"music.blogmura.com",
	"classic.blogmura.com",
	"entertainments.blogmura.com",
	"movie.blogmura.com",
	"tv.blogmura.com",
	"show.blogmura.com",
	"humor.blogmura.com",
	"game.blogmura.com",
	"animation.blogmura.com",
	"comic.blogmura.com",
	"collection.blogmura.com",
	"book.blogmura.com",
	"novel.blogmura.com",
	"poem.blogmura.com",
	"car.blogmura.com",
	"bike.blogmura.com",
	"railroad.blogmura.com",
	"art.blogmura.com",
	"handmade.blogmura.com",
	"flower.blogmura.com",
	"horserace.blogmura.com",
	"gambling.blogmura.com",
	"pachinko.blogmura.com",
	"slot.blogmura.com",
	"taste.blogmura.com",
	"fishing.blogmura.com",
	"outdoor.blogmura.com",
	"cycle.blogmura.com",
	"soccer.blogmura.com",
	"baseball.blogmura.com",
	"fight.blogmura.com",
	"golf.blogmura.com",
	"tennis.blogmura.com",
	"marine.blogmura.com",
	"snow.blogmura.com",
	"sports.blogmura.com",
	"stock.blogmura.com",
	"fx.blogmura.com",
	"futures.blogmura.com",
	"english.blogmura.com",
	"foreign.blogmura.com",
	"qualification.blogmura.com",
	"career.blogmura.com",
	"job.blogmura.com",
	"samurai.blogmura.com",
	"business.blogmura.com",
	"economy.blogmura.com",
	"management.blogmura.com",
	"venture.blogmura.com",
	"politics.blogmura.com",
	"news.blogmura.com",
	"education.blogmura.com",
	"juken.blogmura.com",
	"history.blogmura.com",
	"philosophy.blogmura.com",
	"science.blogmura.com",
	"eco.blogmura.com",
	"douga.blogmura.com",
	"pckaden.blogmura.com",
	"senior.blogmura.com",
	"oyaji.blogmura.com",
	"housewife.blogmura.com",
	"salaryman.blogmura.com",
	"juniorschool.blogmura.com",
	"school.blogmura.com",
	"ec.blogmura.com",
);
$join_domain_str = implode("|",$listSkipUrl);

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

$genre_data = @file_get_contents("http://www.blogmura.com/category.html");
$genre_data = mb_convert_encoding($genre_data,"UTF-8","EUC-JP");

preg_match_all("/<li><a\s+?href=\"(http:\/\/.*?\.blogmura\.com\/.*?\/)\"><span>(.*?)<\/span><\/a><\/li>/u",$genre_data,$listGenre);

$fp = fopen("データ保存ファイル名","w");
foreach($listGenre[1] as $genre){
	$exist_index = preg_match("/$join_domain_str/",$genre);
	if( $exist_index > 0 )
		continue;
print $genre."\n";
	$listCount = 1;
	do{
		if( $listCount == 1 )
			$listCount = "";
		usleep(100000);
		$blog_data = @file_get_contents($genre.'index'.$listCount.".html");
		$blog_data = mb_convert_encoding($blog_data,"UTF-8","EUC-JP");
		preg_match_all("/<a\s+?href=\"(http:\/\/link\.blogmura\.com\/out\/\?ch=[0-9]+?&url=.*?)\"\s+?target=\"_blank\">(.*?)<\/a>/u",$blog_data,$matches);
		preg_match_all("/<li\s+?class=\"entry\-disc\">(.*?)<\/li>/u",$blog_data,$info);

		foreach($matches[1] as $index => $url){
			if( mb_eregi("{$join_str}",$info[1][$index]) ){
				fwrite($fp,$matches[2][$index].",".$url."\n");
				print $info[1][$index].",".$matches[2][$index].",".$url."\n";
			}
		}
		if( $listCount == "" )
			$listCount = 1;
print "page:".$listCount."\n";
		if( $listCount >= $LIMIT )
			break;
		$listCount++;
		$err_flg = preg_match("/<li><a\s+?href=\"index".$listCount.".html\"><span>次へ<\/span><\/a><\/li>/u",$blog_data);
	}while( $err_flg > 0 );
}
fclose($fp);

?>