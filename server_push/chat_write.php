<?php

	writeChatData($_GET['msg']);
	echo "ok";

function writeChatData($data){
	$fp = fopen("�f�[�^�ۑ��t�@�C���܂ł̃p�X","a");
	fwrite($fp,$data."\n");
	fclose($fp);
}

?>