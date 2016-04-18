<?
define(MAIL_CHAR_CODE,'ISO-2022-JP');
$LIST_EMAIL		 = array('送信先メールアドレス');
$FROMMAIL		 = '送信元メールアドレス';
$NOW_DATE		 = date('Y年m月d日', time() + (3600 * 24 * -1) );
$SUBJECT		 = "件名";

$EMAIL_KPI_BODY =<<<__END_OF_MAIL__
To：担当者

__END_OF_MAIL__;

function sendAttachMail ( $from, $to, $subject, $body, $attach) {
	mb_language("japanese");
	mb_internal_encoding("utf-8") ;
	$boundary = "-*-*-*-*-*-*-*-*-Boundary_" . uniqid("b");

	### サブジェクトを jis にして、MIME エンコード
//	$subject = i18n_mime_header_encode( i18n_convert($subject, "JIS") );
//	$subject = i18n_mime_header_encode( mb_convert_encoding($subject,'ISO-2022-JP','EUC-JP') );
	$subject = mb_convert_encoding($subject,'ISO-2022-JP','UTF-8');

	### 本文を jis に
//	$body = i18n_convert($body, "JIS");
	$body = mb_convert_encoding($body,'ISO-2022-JP','UTF-8');

	$listAttach = array();
	foreach($attach as $filename => $record){
//		foreach($record as $data){
			### 添付するデータを、base64 でエンコードして、RFC に適した書式に
			$data = mb_convert_encoding($record,'SJIS','UTF-8');
			$listAttach[$filename][] = chunk_split(base64_encode($data));
//		}
	}

	### ファイル名を sjis にして MIME エンコード。
	### RFC 違反なので日本語ファイル名は使用しないほうがいい。
	## $filename = i18n_mime_header_encode( i18n_convert($filename, "SJIS") );

	### メールの送信
	$mp = popen("/usr/sbin/sendmail -f $from $to", "w");

	########################## メールの組み上げ
	### 全体のヘッダ
	fputs($mp, "MIME-Version: 1.0\n");
	fputs($mp, "Content-Type: Multipart/Mixed; boundary=\"$boundary\"\n");
	fputs($mp, "Content-Transfer-Encoding:Base64\n");
	fputs($mp, "From: $from\n");
	fputs($mp, "To: $to\n");
	fputs($mp, "Subject: $subject\n");

	### メール本文のパート
	fputs($mp, "--$boundary\n");
	fputs($mp, "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n");
	fputs($mp, "\n");
	fputs($mp, "$body\n");

	if( is_array($listAttach) ){
		foreach($listAttach as $filename => $record){
			$filename = mb_convert_encoding($filename,'ISO-2022-JP','UTF-8');
			foreach($record as $data){
//				print $filename ."=>". $data."<br>";
				### 添付ファイルのパート
				fputs($mp, "--$boundary\n");
				fputs($mp, "Content-Type: application/octet-stream; name=\"$filename\"\n");
				fputs($mp, "Content-Transfer-Encoding: base64\n");
				fputs($mp, "Content-Disposition: attachment; filename=\"$filename\"\n");
				fputs($mp, "\n");
				fputs($mp, "$data\n");
				fputs($mp, "\n");
				### マルチパートのおわり。
				fputs($mp, "--$boundary" . "--\n");
			}
		}
	}
	pclose($mp);
}

//sendAttachMail( $from, $to, $subject, $body, $attach_data, $filename);

?>
