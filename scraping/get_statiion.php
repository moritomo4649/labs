<?php

$listStation = array(
	'1=1=吾妻線',
	'2=1=赤羽線',
	'73=1=秋田新幹線',
	'3=1=左沢線',
	'4=1=飯山線',
	'5=1=石巻線',
	'6=1=五日市線',
	'7=1=伊東線',
	'8=1=岩泉線',
	'9=1=羽越本線',
	'10=1=内房線',
	'11=1=越後線',
	'12=1=奥羽本線',
	'13=1=青梅線',
	'14=1=大糸線',
	'15=1=大船渡線',
	'16=1=大湊線',
	'17=1=男鹿線',
	'18=1=鹿島線',
	'19=1=釜石線',
	'20=1=烏山線',
	'21=1=川越線',
	'22=1=北上線',
	'23=1=久留里線',
	'24=1=京浜東北線',
	'25=1=京葉線',
	'26=1=気仙沼線',
	'27=1=小海線',
	'28=1=五能線',
	'29=1=埼京線',
	'30=1=相模線',
	'31=1=篠ノ井線',
	'74=1=上越新幹線(1)',
	'74=2=上越新幹線(2)',
	'32=1=上越線',
	'33=1=常磐・成田線',
	'34=1=常磐線',
	'35=1=信越本線(1)',
	'35=2=信越本線(2)',
	'36=1=水郡線(1)',
	'36=2=水郡線(2)',
	'37=1=仙山線',
	'38=1=仙石線',
	'39=1=総武線',
	'40=1=総武線快速',
	'41=1=総武本線',
	'42=1=外房線',
	'43=1=高崎線',
	'44=1=田沢湖線',
	'45=1=只見線',
	'46=1=中央本線',
	'47=1=津軽線',
	'48=1=鶴見線',
	'49=1=東海道本線',
	'50=1=東金線',
	'75=1=東北新幹線',
	'51=1=東北本線',
	'76=1=長野新幹線',
	'52=1=成田線(1)',
	'52=2=成田線(2)',
	'53=1=南武線(1)',
	'53=2=南武線(2)',
	'54=1=日光線',
	'55=1=根岸線',
	'56=1=白新線',
	'57=1=八高線',
	'58=1=八戸線',
	'59=1=花輪線',
	'60=1=磐越西線',
	'61=1=磐越東線',
	'62=1=水戸線',
	'63=1=武蔵野線',
	'64=1=弥彦線',
	'77=1=山形新幹線',
	'65=1=山田線',
	'66=1=山手線',
	'67=1=横須賀線',
	'68=1=横浜線',
	'69=1=米坂線',
	'70=1=陸羽西線',
	'71=1=陸羽東線',
	'72=1=両毛線'
);

$search_station = array(
);

mb_regex_encoding("UTF-8");

$listStationName = array();
foreach($listStation as $st_name){
	$st_index = preg_replace("/^.+=(.+?)$/u","$1",$st_name);
print "_/_/_/_/_/_/_/_/_/_/".$st_index."_/_/_/_/_/_/_/_/_/_/\n";
	$pageCount = 0;
	do{
		$station = @file_get_contents('http://www.jreast.co.jp/estation/result.aspx?mode=1&rosen='.$st_name.'&token==&city=&ekimei=&kana=&pc='.$pageCount);
		sleep(3);
		$station = mb_convert_encoding($station,"UTF-8","SJIS");
		preg_match_all("/<A\s+?href='\.\/station\/info\.aspx\?StationCd=[0-9]+?'>(.+?)<\/A>/u",$station,$listStation);
		foreach($listStation[1] as $name){
print $name."\n";
			$listStationName[$st_index][] = $name;
		}
		$exist_flg = preg_match("/<a\s+?href='\.\/result\.aspx\?mode=1&rosen=.+?&token==&city=&ekimei=&kana=&pc=[0-9]+?'>次の20駅<\/a>/u",$station);
		$pageCount++;
	}while( $exist_flg > 0 );
}

$fp = fopen("データ保存ファイル名","w");
foreach($listStationName as $station_index => $record){
	$station_index = mb_convert_encoding($station_index,"SJIS","UTF-8");
	fwrite($fp,$station_index.",\n");
	foreach($record as $name){
		$name = preg_replace("/^(.+)?（.+?）$/u","$1",$name);
		$name = mb_convert_encoding($name,"SJIS","UTF-8");
		fwrite($fp,",".$name."\n");
	}
}
fclose($fp);

?>