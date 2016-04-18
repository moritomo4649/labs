<?php

	header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	header("Access-Control-Allow-Origin: *");

	while (1) {
		$html = getChatData();
		echo $html."\n\n";
		ob_flush();
		flush();
		sleep(3);
	}

function getChatData(){
	$data = file("データ保存ファイルまでのパス");
//	$data = preg_replace("/\n/g","|",trim($data));
//	$data = implode("<br>",trim($data));
	foreach($data as $val){
		echo "data:".$val;
	}
	return trim($data);
}

?>