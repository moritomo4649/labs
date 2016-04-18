<?php

$listChInfo	 = array();
$listBand	 = array("A");
$listCh		 = array("24","25","26");

mb_regex_encoding("UTF-8");

foreach($listBand as $band){
	foreach($listCh as $ch){
			$usen_data = @file_get_contents("http://music.usen.com/nowplay/search.php?arrow=NG&genzai=%B8%BD%BA%DF%BB%FE%B4%D6&band=".$band."&chno=".$ch);
			$usen_data = mb_convert_encoding($usen_data,"UTF-8","EUC-JP");
			preg_match_all("/<span\s+?class[[:space:]]{0,}=[[:space:]]{0,}[\"']style11[\"']>(.*?)<\/span>/u",$usen_data,$channel);
			preg_match_all("/\([[:space:]]{0,}([0-9]+?位|推薦曲)[[:space:]]{0,}\)[\s　]{0,}(.+?)\/(.+?)[\s　]{0,}<br>/iu",$usen_data,$songs);
			foreach($songs[1] as $index => $rank){
				$song			 = mb_convert_kana(trim($songs[2][$index]),"rnasK","UTF-8");
				$artist			 = mb_convert_kana(trim($songs[3][$index]),"rnasK","UTF-8");

				$song			 = mb_ereg_replace("^\s+*(.*?)$","\\1",$song);
				$song			 = mb_ereg_replace("^(.*?)\s+$","\\1",$song);
				$artist			 = mb_ereg_replace("^\s+*(.*?)$","\\1",$artist);
				$artist			 = mb_ereg_replace("^(.*?)\s+$","\\1",$artist);

				$song			 = urlencode(mb_convert_encoding($song,"SJIS","UTF-8"));
				$artist			 = urlencode(mb_convert_encoding($artist,"SJIS","UTF-8"));

				$song_result	 = @file_get_contents("http://www.uta-net.com/user/ichiran.html?Cselect=1&Fselect=&sort=2&Aselect=2&Keyword=".$song."&Bselect=4");
				$song_result = mb_convert_encoding($song_result,"UTF-8","SJIS");
				$exist_flg = mb_eregi("<DIV\sALIGN=\"center\"\sclass=\"font_base_size\">",$song_result);

				$artist_result	 = @file_get_contents("http://www.uta-net.com/user/ichiran.html?Cselect=1&Fselect=&sort=2&Aselect=1&Keyword=".$artist."&Bselect=4");
				$artist_result = mb_convert_encoding($artist_result,"UTF-8","SJIS");
				$exist_flg = mb_eregi("<DIV\sALIGN=\"center\"\sclass=\"font_base_size\">",$song_result);

				$listChInfo[$channel[1][0]][] = $rank.":".$song.":".$artist;
			}
	}
}
print_r($listChInfo);

?>