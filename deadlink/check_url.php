<?php

/*

	使い方：cat url.txt | php check_url.php
	出力結果:logディレクトリ配下
	出力フォーマット:URL,最終アクセスURL,ステータスコード,タイトル

	※ログのディレクトリ作成やパス設定は個別に行ってください。

*/

$URL_DIR		 = '/home/nishizawa/deadlink/test/';
$SAVE_DIR		 = '/home/nishizawa/deadlink/test/url/';
$listSkipDomain	 = array(

);
$listMergeDomain = array(

);
$listFp			 = array();
$listDomain		 = array();

while(!feof(STDIN)) {
	$url = trim(fgets(STDIN));
	if($url === FALSE) {
		if(feof(STDIN)) {
			break;
		}
		continue;
	}
	if( preg_match("/https?:\/\/(.+)/",$url,$first_domain) ){
		preg_match("/^([a-zA-Z0-9_\.\-]+?)\//",$first_domain[1],$domain);
		if( $domain[1] == '' )
			$domain[1] = $first_domain[1];
		//除外ドメイン
		if( isset($listSkipDomain) ){
			$search_index = array_search($domain[1],$listSkipDomain);
			if( $search_index !== false )
				continue;
		}
		if( isset($listMergeDomain) ){
			foreach($listMergeDomain as $merge_domain){
				if( preg_match("/({$merge_domain})/",$url,$merge) ){
					$domain[1] = $merge[1];
					break;
				}
			}
		}
		if( empty($listFp[$domain[1]]) ){
			$fp = fopen($SAVE_DIR.$domain[1].'_tmp',"w");
			$listFp[$domain[1]] = $fp;
		}
		if( !empty($listFp[$domain[1]]) ){
			fwrite($listFp[$domain[1]],$url."\n");
		}
	}else{
print "no match:{$url}\n";
	}
}

foreach($listFp as $key => $fp){
	fclose($fp);
print "start:{$key}\n";
	`sort -k 1,1 -t '\t' {$SAVE_DIR}{$key}_tmp > {$SAVE_DIR}{$key}.txt`;
	`rm -rf {$SAVE_DIR}{$key}_tmp`;
	`cat {$SAVE_DIR}{$key}.txt | php {$URL_DIR}get_content.php {$key}.txt {$argv[1]} > /dev/null &`;
}
print "end\n";

?> 
