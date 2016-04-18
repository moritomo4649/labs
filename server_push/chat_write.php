<?php

	writeChatData($_GET['msg']);
	echo "ok";

function writeChatData($data){
	$fp = fopen("データ保存ファイルまでのパス","a");
	fwrite($fp,$data."\n");
	fclose($fp);
}

?>