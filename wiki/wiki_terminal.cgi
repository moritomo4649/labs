#!/usr/bin/perl

# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# +
# +	名前	: Wikiターミナル
# + 用途	: Wikiルールに則って作成した仕様書・財務表など、ありとあらゆるドキュメント
# +			: を見応えあるHTMLタグに変換させてくれます。
# + 仕様	: SE・PG向けのプロフェッショナル仕様のため、ターミナルから実行させる
# +			: ことができる人のみとなっています。
# + Version	: 1.0
# + 使い方	: 同じディレクトリ内にCGIファイル、CSSファイル、
# +			: 変換前のファイルを置き、ターミナル上で
# +			: cat 変換前のファイル名 | perl convertHTML.cgi
# +			: と入力するだけ。
# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

#==============================================================================
#	use
#==============================================================================
use strict;

#==============================================================================
#	define
#==============================================================================
my $OUTPUT_FILE	 = 'convert.html';			#変換されたドキュメントを保存するファイル名
my $CSS_FILE	 = 'wiki_terminal.css';		#出力されるHTML画面のデザインを決めるCSSファイル

&formatData;
exit;

#==============================================================================
#	内容	: Web上にあるWikiツールのルールで書かれたドキュメントを
#			: HTMLタグに変換する
#	return	: 1
#==============================================================================
sub formatData {

	my $record = join '', <>;

	open FH_WRITE,">$OUTPUT_FILE";

	&printHeader;

	#見出しに変換
	&convertTitle(\$record);

	#リストに変換
	&convertList(\$record);

	#テーブルに変換
	$record =~ s/((\|.+\|\n){1,})[^\|]/&convertTable( $1 )/meg;

	#テキストを装飾する
	&convertTextDesign( \$record );

	#ブロックに変換
	&convertBlock(\$record);

	#段落に変換
	$record =~ s/~/<br>/g;

	print FH_WRITE $record;

	&printFooter;

	close FH_WRITE;

	return 1;

}

#==============================================================================
#	内容	: '*','**','***'を<h4>,<h3>,<h2>に変換
#	return	: 1
#==============================================================================
sub convertTitle {

	my $r_data = shift;

	#見出し３
	$$r_data =~ s/^(\*\*\*)(.+)$/<h4>$2<\/h4>\n/mg;

	#見出し２
	$$r_data =~ s/^(\*\*)(.+)$/<h3>$2<\/h3>\n/mg;

	#見出し１
	$$r_data =~ s/^(\*)(.+)$/<h2>$2<\/h2>\n/mg;

	return 1;
}

#==============================================================================
#	内容	: '-','--','---','+','++','+++'を<ol>,<ul>の箇条書き,リストに変換
#	return	: 1
#==============================================================================
sub convertList {
	my $r_str	= shift;
	#データを複数行として解釈(正規表現/m)
	$$r_str =~ s/^((\-{1,3}.+\n){1,})/&createList( $1 )/meg;
	$$r_str =~ s/^((\+{1,3}.+\n){1,})/&createItems( $1 )/meg;
	return 1;
}

#==============================================================================
#	内容	: '+','++','+++'を<ol>に変換
#	return	: $convert
#==============================================================================
sub createItems {
	#リスト文字列を１行ごとに区切る
	my @listUl = split /\n/,shift;

	my $convert;
	my $start_flg1 = 0;
	my $start_flg2 = 0;
	my $start_flg3 = 0;
	my @listItems;

	for(my $listCount=0;$listCount <= $#listUl;$listCount++){
		#太字に変換
		&convertText( \$listUl[$listCount] );
		#行の先頭が'+'で始まるとき
		if( $listUl[$listCount] =~ /^(\+){1}([^\+][^\+].+)$/ ){
			if( $start_flg2 == 1 || $start_flg3 == 1 ){
				foreach(@listItems){
					$convert .= $_;
				}
				$start_flg2 = 0;
				$start_flg3 = 0;
				my $listCount = @listItems;
				if( $listCount == 0 ){
					$convert .= "</ol>\n";
				}
				undef @listItems;
			}
			if( $start_flg1 == 0 ){
				$start_flg1 = 1;
				$convert .= "<ol class=list1 style='padding-left:16px;margin-left:16px'>\n";
			}
			$convert .= "<li>$2</li>\n";
		}
		#行の先頭が'++'で始まるとき
		if( $listUl[$listCount] =~ /^(\+){2}([^\+].+)$/ ){
			if( $start_flg2 == 0 && $start_flg1 == 1 ){
				$convert =~ s/<\/li>$//;
			}
			if( $start_flg3 == 1 ){
				shift @listItems;
				$start_flg3 = 0;
				$convert .= "</ol>\n";
			}
			if( $start_flg2 == 0 ){
				$start_flg2 = 1;
				$convert .= "<ol class=list2 style='padding-left:16px;margin-left:16px'>\n";
				if( $start_flg1 == 1 ){
					push @listItems,"</ol></li>\n";
				}
			}
			$convert .= "<li>$2</li>\n";
		}
		#行の先頭が'+++'で始まるとき
		if( $listUl[$listCount] =~ /^(\+){3}(.+)$/ ){
			if( $start_flg3 == 0 && ( $start_flg1 == 1 || $start_flg2 == 1 ) ){
				$convert =~ s/<\/li>$//;
			}
			if( $start_flg3 == 0 ){
				$start_flg3 = 1;
				$convert .= "<ol class=list3 style='padding-left:32px;margin-left:32px'>\n";
				if( $start_flg1 == 1 || $start_flg2 == 1 ){
					push @listItems,"</ol></li>\n";
				}
			}
			$convert .= "<li>$2</li>\n";
		}
	}
	foreach(@listItems){
		$convert .= $_;
	}
	$convert .= "</ol>\n";

	return $convert;
}

#==============================================================================
#	内容 	: '-','--','---'を<ul>に変換
#	return	: $convert
#==============================================================================
sub createList {
	#箇条書き文字列を１行ごとに区切る
	my @listUl = split /\n/,shift;

	my $convert;
	my $start_flg1 = 0;
	my $start_flg2 = 0;
	my $start_flg3 = 0;
	my @listItems;

	for(my $listCount=0;$listCount <= $#listUl;$listCount++){
		&convertText( \$listUl[$listCount] );
		#行の先頭が'-'で始まっていたら
		if( $listUl[$listCount] =~ /^(\-){1}([^\-][^\-].+)$/ ){
			if( $start_flg2 == 1 || $start_flg3 == 1 ){
				foreach(@listItems){
					$convert .= $_;
				}
				$start_flg2 = 0;
				$start_flg3 = 0;
				my $listCount = @listItems;
				if( $listCount == 0 ){
					$convert .= "</ul>\n";
				}
				undef @listItems;
			}
			if( $start_flg1 == 0 ){
				$start_flg1 = 1;
				$convert .= "<ul class=list1 style='padding-left:16px;margin-left:16px'>\n";
			}
			$convert .= "<li>$2</li>\n";
		}
		#行の先頭が'--'で始まっていたら
		if( $listUl[$listCount] =~ /^(\-){2}([^\-].+)$/ ){
			if( $start_flg2 == 0 && $start_flg1 == 1 ){
				$convert =~ s/<\/li>$//;
			}
			if( $start_flg3 == 1 ){
				shift @listItems;
				$start_flg3 = 0;
				$convert .= "</ul>\n";
			}
			if( $start_flg2 == 0 ){
				$start_flg2 = 1;
				$convert .= "<ul class=list2 style='padding-left:16px;margin-left:16px'>\n";
				if( $start_flg1 == 1 ){
					push @listItems,"</ul></li>\n";
				}
			}
			$convert .= "<li>$2</li>\n";
		}
		#行の先頭が'---'で始まっていたら
		if( $listUl[$listCount] =~ /^(\-){3}(.+)$/ ){
			if( $start_flg3 == 0 && ( $start_flg1 == 1 || $start_flg2 == 1 ) ){
				$convert =~ s/<\/li>$//;
			}
			if( $start_flg3 == 0 ){
				$start_flg3 = 1;
				$convert .= "<ul class=list3 style='padding-left:32px;margin-left:32px'>\n";
				if( $start_flg1 == 1 || $start_flg2 == 1 ){
					push @listItems,"</ul></li>\n";
				}
			}
			$convert .= "<li>$2</li>\n";
		}
	}
	foreach(@listItems){
		$convert .= $_;
	}
	$convert .= "</ul>\n";

	return $convert;
}

#==============================================================================
#	内容	: テーブル内のテキストの位置・サイズ・色などを設定する
#	return	: 1
#==============================================================================
sub setTableText {

	my $r_table_data = shift;
	my $r_hash_data  = shift;

	my $match = 0;
	$r_hash_data->{align} = 'left';

	#位置指定
	if( $$r_table_data =~ s/CENTER:// ){
		$r_hash_data->{align} = 'center';
	}
	if( $$r_table_data =~ s/RIGHT:// ){
		$r_hash_data->{align} = 'right';
	}
	if( $$r_table_data =~ s/LEFT:// ){
		$r_hash_data->{align} = 'left';
	}

	#セルの背景色
	if( $$r_table_data =~ s/(BGCOLOR\((.+?)\):)// ){
		$r_hash_data->{bgcolor} = $2;
	}

	#フォント色
	if( $$r_table_data =~ s/(COLOR\((.+?)\):)// ){
		$match = 1;
		$r_hash_data->{color} = $2;
	}

	#フォントサイズ
	if( $$r_table_data =~ s/(SIZE\((.+?)\):)// ){
		$match = 1;
		$r_hash_data->{size} = $2;
	}

	#テキストの途中にマッチするか調べる
	if( $match ){
		&convertReflexive( $r_table_data );
	}

	#ルールに従ってテキストを変換
	&convertText( $r_table_data );

	return 1;
}

#==============================================================================
#	内容	; setTableTextで調べた文字列でマッチしたものがあれば更に調べる
#	return	: 1
#==============================================================================
sub convertReflexive {

	my $r_text_data = shift;
	
	#フォント色・フォントサイズで指定
	if( $$r_text_data =~ s/(COLOR\((.+?)\):)(SIZE\((.+?)\):)(.*)/<\/span>\n<span style=\"color:$2;font-size:$4px;\">$5/ ){
		&convertReflexive( $r_text_data );
		return ;
	}

	#フォントサイズフォント色で指定
	if( $$r_text_data =~ s/(SIZE\((.+?)\):)(COLOR\((.+?)\):)(.*)/<\/span>\n<span style=\"color:$4;font-size:$2px;\">$5/ ){
		&convertReflexive( $r_text_data );
		return ;
	}

	#フォント色のみで指定
	if( $$r_text_data =~ s/(COLOR\((.+?)\):)(.*)/<\/span>\n<span style=\"color:$2;\">$3/ ){
		&convertReflexive( $r_text_data );
		return ;
	}

	#フォントサイズのみで指定
	if( $$r_text_data =~ s/(SIZE\((.+?)\):)(.*)/<\/span>\n<span style=\"font-size:$2px;\">$3/ ){
		&convertReflexive( $r_text_data );
		return ;
	}

	if( $$r_text_data =~ s/(<\/span>)// ){
		$$r_text_data .= "</span>\n";
	}

	return 1;
}

#==============================================================================
#	内容	; テキスト中も文字列を装飾する
#	return	: 1
#==============================================================================
sub convertTextDesign {

	my $r_text_data = shift;

	#位置指定
	if( $$r_text_data =~ s/^CENTER:(.+)/<div style=\"text-align:center;\">$1<\/div>/m ){
		&convertTextDesign( $r_text_data );
	}
	if( $$r_text_data =~ s/^RIGHT:(.+)/<div style=\"text-align:right;\">$1<\/div>/m ){
		&convertTextDesign( $r_text_data );
	}
	if( $$r_text_data =~ s/^LEFT:(.+)/<div style=\"text-align:left;\">$1<\/div>/m ){
		&convertTextDesign( $r_text_data );
	}

	#フォント色・背景色で指定
	if( $$r_text_data =~ s/(COLOR\((.+?),(.+?)\))({(.*?)})/<span
	style=\"color:$2;background-color:$3;\">$5<\/span>\n/s ){
		&convertTextDesign( $r_text_data );
	}
	
	#フォント色のみで指定
	if( $$r_text_data =~ s/(COLOR\((.+?)\))({(.*?)})/<span style=\"color:$2;\">$4<\/span>\n/s ){
		&convertTextDesign( $r_text_data );
	}

	
	#フォントサイズのみで指定
	if( $$r_text_data =~ s/(SIZE\((.+?)\))({(.*?)})/<span style=\"font-size:$2px;\">$4<\/span>\n/s ){
		&convertTextDesign( $r_text_data );
	}

	return 1;
}

#==============================================================================
#	内容	: 太字に変換、''は削除
#	return	: 1
#==============================================================================
sub convertText {

	my $r_text_data = shift;

	#太字に変換
	$$r_text_data =~ s/''(.+)''/<b>$1<\/b>/g;

	#文字列に''があれば削除
	$$r_text_data =~ s/''//g;

	return 1;
}

#==============================================================================
#	内容	: '|'を区切りとした文字列をテーブルタグに変換
#	return	: $convert
#==============================================================================
sub convertTable {
	my $str = shift;

	#テーブルタグの元になる'|'から'|'の文字列を１行ずつに区切る
	my @listTable = split /\|\n/, $str;
	my $convert;
	my $count=0;
	my $tr_width;
	my %HashData = {};

	foreach my $table_param(@listTable){
		#セルの中の文字列に含まれる改行を<br>に変換
		$table_param =~ s/\n/<br>/g;
		#空白を削除
		$table_param =~ s/\ //;

		#１行のデータをセルに分割
		my @listTr = split /\|/, $table_param;

		#テーブルの１行目
		if( $count == 0 ){
			#テーブルの１行目のセルの数を取得
			$tr_width = $#listTr;

			for(my $listCount=0;$listCount <= $#listTr;$listCount++){
				#テキストを設定
				&setTableText( \$listTr[$listCount], \%HashData );
				if( $listCount == 0 ){
					$convert .= "<div class=ie5>\n<table class=style_table cellspacing='1' border='0'>\n<tr>\n";
				}elsif( $listCount == $#listTr ){
					if( $#listTable > 0 ){
						$convert .= sprintf( "<td class=style_td style=\"text-align:%s;font-size:%spx;color=%s; background-color=%s;\">%s</td>\n</tr>\n",
									$HashData{align}, $HashData{size}, $HashData{color}, $HashData{bgcolor}, $listTr[$listCount] );
					}
					if( $#listTable == 0 ){
						$convert .= sprintf( "<td class=style_td style=\"text-align:%s;font-size:%spx;color:%s;background-color:%s;\">%s</td>\n</tr>\n</table>\n</div>\n",
								$HashData{align}, $HashData{size}, $HashData{color}, $HashData{bgcolor}, $listTr[$listCount] );
					}
				}else{
					$convert .= sprintf( "<td class=style_td style=\"text-align:%s;font-size:%spx;color:%s;background-color:%s;\">%s</td>\n",
								$HashData{align}, $HashData{size}, $HashData{color}, $HashData{bgcolor}, $listTr[$listCount] );
				}
				undef %HashData;
			}
		#テーブルの最後の行
		}elsif( $count == $#listTable ){
			for(my $listCount=0;$listCount <= $tr_width;$listCount++){
				#テキストを設定
				&setTableText( \$listTr[$listCount], \%HashData );
				if( $listCount == 0 ){
					$convert .= "<tr>";
				}elsif( $listCount == $tr_width ){
					$convert .= sprintf( "<td class=style_td style=\"text-align:%s;font-size:%spx;color:%s;background-color:%s;\">%s</td>\n</tr>\n</table>\n</div>\n",
							$HashData{align}, $HashData{size}, $HashData{color}, $HashData{bgcolor}, $listTr[$tr_width] );
				}else{
					$convert .= sprintf( "<td class=style_td style=\"text-align:%s;font-size:%spx;color:%s;background-color:%s;\">%s</td>\n",
							$HashData{align}, $HashData{size}, $HashData{color}, $HashData{bgcolor}, $listTr[$listCount] );
				}
				undef %HashData;
			}
		#テーブルの中間
		}else{
			for(my $listCount=0;$listCount <= $tr_width;$listCount++){
				#テキストを設定
				&setTableText( \$listTr[$listCount], \%HashData );
				if( $listCount == 0 ){
					$convert .= "<tr>\n";
				}elsif( $listCount == $tr_width ){
					$convert .=sprintf( "<td class=style_td style=\"text-align:%s;font-size:%s;font-color:%s;background-color:%s;\">%s</td>\n</tr>\n",
							$HashData{align}, $HashData{size}, $HashData{color}, $HashData{bgcolor}, $listTr[$tr_width] );
				}else{
					$convert .=sprintf( "<td class=style_td style=\"text-align:%s;font-size:%s;font-color:%s;background-color:%s;\">%s</td>\n",
							$HashData{align}, $HashData{size}, $HashData{color}, $HashData{bgcolor}, $listTr[$listCount] );
				}
				undef %HashData;
			}
		}
		$count++;
	}
	return $convert;
}

#==============================================================================
#	内容	: 行の先頭が空白であるかどうか調べる
#	return	: 1
#==============================================================================
sub convertBlock {
	my $r_data = shift;
	#データを複数行として解釈(正規表現/m)
	$$r_data =~ s/^((\ .*\n){1,})/&createBlock( $1 )/meg;
	return 1;
}

#==============================================================================
#	内容	: 引数で渡されたデータを<pre>タグで囲む
#	return	: $convert
#==============================================================================
sub createBlock {
	my $str = shift;
	my $convert;
	$convert = "<pre>\n";
	$convert .= "$str";
	$convert .= "</pre>\n";
	return $convert;
}

#==============================================================================
#	内容	: HTMLヘッダをファイルに書き込む
#	return	: 1
#==============================================================================
sub printHeader {

	print FH_WRITE <<__END_OF_HTML__;
<html>
<head>
<title></title>
<META Http-Equiv="content-type" Content="text/html;charset=euc-jp">
<META HTTP-EQUIV="Content-Style-Type" CONTENT="text/css">
<link rel="stylesheet" type="text/css" href="$CSS_FILE" media="screen">
</head>
<body>
<div>
__END_OF_HTML__

	return 1;
}

#==============================================================================
#	内容	: HTMLフッダをファイルに書き込む
#	return	: 1
#==============================================================================
sub printFooter {

	print FH_WRITE <<__END_OF_HTML__;
</div>
</body>
</html>
__END_OF_HTML__

	return 1;
}

