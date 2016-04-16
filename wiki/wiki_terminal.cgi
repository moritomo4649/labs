#!/usr/bin/perl

# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# +
# +	̾��	: Wiki�����ߥʥ�
# + ����	: Wiki�롼���§�äƺ����������ͽ񡦺�̳ɽ�ʤɡ�����Ȥ�����ɥ������
# +			: �򸫱�������HTML�������Ѵ������Ƥ���ޤ���
# + ����	: SE��PG�����Υץ�ե��å���ʥ���ͤΤ��ᡢ�����ߥʥ뤫��¹Ԥ�����
# +			: ���Ȥ��Ǥ���ͤΤߤȤʤäƤ��ޤ���
# + Version	: 1.0
# + �Ȥ���	: Ʊ���ǥ��쥯�ȥ����CGI�ե����롢CSS�ե����롢
# +			: �Ѵ����Υե�������֤��������ߥʥ���
# +			: cat �Ѵ����Υե�����̾ | perl convertHTML.cgi
# +			: �����Ϥ��������
# +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

#==============================================================================
#	use
#==============================================================================
use strict;

#==============================================================================
#	define
#==============================================================================
my $OUTPUT_FILE	 = 'convert.html';			#�Ѵ����줿�ɥ�����Ȥ���¸����ե�����̾
my $CSS_FILE	 = 'wiki_terminal.css';		#���Ϥ����HTML���̤Υǥ���������CSS�ե�����

&formatData;
exit;

#==============================================================================
#	����	: Web��ˤ���Wiki�ġ���Υ롼��ǽ񤫤줿�ɥ�����Ȥ�
#			: HTML�������Ѵ�����
#	return	: 1
#==============================================================================
sub formatData {

	my $record = join '', <>;

	open FH_WRITE,">$OUTPUT_FILE";

	&printHeader;

	#���Ф����Ѵ�
	&convertTitle(\$record);

	#�ꥹ�Ȥ��Ѵ�
	&convertList(\$record);

	#�ơ��֥���Ѵ�
	$record =~ s/((\|.+\|\n){1,})[^\|]/&convertTable( $1 )/meg;

	#�ƥ����Ȥ���������
	&convertTextDesign( \$record );

	#�֥�å����Ѵ�
	&convertBlock(\$record);

	#������Ѵ�
	$record =~ s/~/<br>/g;

	print FH_WRITE $record;

	&printFooter;

	close FH_WRITE;

	return 1;

}

#==============================================================================
#	����	: '*','**','***'��<h4>,<h3>,<h2>���Ѵ�
#	return	: 1
#==============================================================================
sub convertTitle {

	my $r_data = shift;

	#���Ф���
	$$r_data =~ s/^(\*\*\*)(.+)$/<h4>$2<\/h4>\n/mg;

	#���Ф���
	$$r_data =~ s/^(\*\*)(.+)$/<h3>$2<\/h3>\n/mg;

	#���Ф���
	$$r_data =~ s/^(\*)(.+)$/<h2>$2<\/h2>\n/mg;

	return 1;
}

#==============================================================================
#	����	: '-','--','---','+','++','+++'��<ol>,<ul>�βվ��,�ꥹ�Ȥ��Ѵ�
#	return	: 1
#==============================================================================
sub convertList {
	my $r_str	= shift;
	#�ǡ�����ʣ���ԤȤ��Ʋ��(����ɽ��/m)
	$$r_str =~ s/^((\-{1,3}.+\n){1,})/&createList( $1 )/meg;
	$$r_str =~ s/^((\+{1,3}.+\n){1,})/&createItems( $1 )/meg;
	return 1;
}

#==============================================================================
#	����	: '+','++','+++'��<ol>���Ѵ�
#	return	: $convert
#==============================================================================
sub createItems {
	#�ꥹ��ʸ����򣱹Ԥ��Ȥ˶��ڤ�
	my @listUl = split /\n/,shift;

	my $convert;
	my $start_flg1 = 0;
	my $start_flg2 = 0;
	my $start_flg3 = 0;
	my @listItems;

	for(my $listCount=0;$listCount <= $#listUl;$listCount++){
		#�������Ѵ�
		&convertText( \$listUl[$listCount] );
		#�Ԥ���Ƭ��'+'�ǻϤޤ�Ȥ�
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
		#�Ԥ���Ƭ��'++'�ǻϤޤ�Ȥ�
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
		#�Ԥ���Ƭ��'+++'�ǻϤޤ�Ȥ�
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
#	���� 	: '-','--','---'��<ul>���Ѵ�
#	return	: $convert
#==============================================================================
sub createList {
	#�վ��ʸ����򣱹Ԥ��Ȥ˶��ڤ�
	my @listUl = split /\n/,shift;

	my $convert;
	my $start_flg1 = 0;
	my $start_flg2 = 0;
	my $start_flg3 = 0;
	my @listItems;

	for(my $listCount=0;$listCount <= $#listUl;$listCount++){
		&convertText( \$listUl[$listCount] );
		#�Ԥ���Ƭ��'-'�ǻϤޤäƤ�����
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
		#�Ԥ���Ƭ��'--'�ǻϤޤäƤ�����
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
		#�Ԥ���Ƭ��'---'�ǻϤޤäƤ�����
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
#	����	: �ơ��֥���Υƥ����Ȥΰ��֡������������ʤɤ����ꤹ��
#	return	: 1
#==============================================================================
sub setTableText {

	my $r_table_data = shift;
	my $r_hash_data  = shift;

	my $match = 0;
	$r_hash_data->{align} = 'left';

	#���ֻ���
	if( $$r_table_data =~ s/CENTER:// ){
		$r_hash_data->{align} = 'center';
	}
	if( $$r_table_data =~ s/RIGHT:// ){
		$r_hash_data->{align} = 'right';
	}
	if( $$r_table_data =~ s/LEFT:// ){
		$r_hash_data->{align} = 'left';
	}

	#������طʿ�
	if( $$r_table_data =~ s/(BGCOLOR\((.+?)\):)// ){
		$r_hash_data->{bgcolor} = $2;
	}

	#�ե���ȿ�
	if( $$r_table_data =~ s/(COLOR\((.+?)\):)// ){
		$match = 1;
		$r_hash_data->{color} = $2;
	}

	#�ե���ȥ�����
	if( $$r_table_data =~ s/(SIZE\((.+?)\):)// ){
		$match = 1;
		$r_hash_data->{size} = $2;
	}

	#�ƥ����Ȥ�����˥ޥå����뤫Ĵ�٤�
	if( $match ){
		&convertReflexive( $r_table_data );
	}

	#�롼��˽��äƥƥ����Ȥ��Ѵ�
	&convertText( $r_table_data );

	return 1;
}

#==============================================================================
#	����	; setTableText��Ĵ�٤�ʸ����ǥޥå�������Τ�����й���Ĵ�٤�
#	return	: 1
#==============================================================================
sub convertReflexive {

	my $r_text_data = shift;
	
	#�ե���ȿ����ե���ȥ������ǻ���
	if( $$r_text_data =~ s/(COLOR\((.+?)\):)(SIZE\((.+?)\):)(.*)/<\/span>\n<span style=\"color:$2;font-size:$4px;\">$5/ ){
		&convertReflexive( $r_text_data );
		return ;
	}

	#�ե���ȥ������ե���ȿ��ǻ���
	if( $$r_text_data =~ s/(SIZE\((.+?)\):)(COLOR\((.+?)\):)(.*)/<\/span>\n<span style=\"color:$4;font-size:$2px;\">$5/ ){
		&convertReflexive( $r_text_data );
		return ;
	}

	#�ե���ȿ��Τߤǻ���
	if( $$r_text_data =~ s/(COLOR\((.+?)\):)(.*)/<\/span>\n<span style=\"color:$2;\">$3/ ){
		&convertReflexive( $r_text_data );
		return ;
	}

	#�ե���ȥ������Τߤǻ���
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
#	����	; �ƥ��������ʸ�������������
#	return	: 1
#==============================================================================
sub convertTextDesign {

	my $r_text_data = shift;

	#���ֻ���
	if( $$r_text_data =~ s/^CENTER:(.+)/<div style=\"text-align:center;\">$1<\/div>/m ){
		&convertTextDesign( $r_text_data );
	}
	if( $$r_text_data =~ s/^RIGHT:(.+)/<div style=\"text-align:right;\">$1<\/div>/m ){
		&convertTextDesign( $r_text_data );
	}
	if( $$r_text_data =~ s/^LEFT:(.+)/<div style=\"text-align:left;\">$1<\/div>/m ){
		&convertTextDesign( $r_text_data );
	}

	#�ե���ȿ����طʿ��ǻ���
	if( $$r_text_data =~ s/(COLOR\((.+?),(.+?)\))({(.*?)})/<span
	style=\"color:$2;background-color:$3;\">$5<\/span>\n/s ){
		&convertTextDesign( $r_text_data );
	}
	
	#�ե���ȿ��Τߤǻ���
	if( $$r_text_data =~ s/(COLOR\((.+?)\))({(.*?)})/<span style=\"color:$2;\">$4<\/span>\n/s ){
		&convertTextDesign( $r_text_data );
	}

	
	#�ե���ȥ������Τߤǻ���
	if( $$r_text_data =~ s/(SIZE\((.+?)\))({(.*?)})/<span style=\"font-size:$2px;\">$4<\/span>\n/s ){
		&convertTextDesign( $r_text_data );
	}

	return 1;
}

#==============================================================================
#	����	: �������Ѵ���''�Ϻ��
#	return	: 1
#==============================================================================
sub convertText {

	my $r_text_data = shift;

	#�������Ѵ�
	$$r_text_data =~ s/''(.+)''/<b>$1<\/b>/g;

	#ʸ�����''������к��
	$$r_text_data =~ s/''//g;

	return 1;
}

#==============================================================================
#	����	: '|'����ڤ�Ȥ���ʸ�����ơ��֥륿�����Ѵ�
#	return	: $convert
#==============================================================================
sub convertTable {
	my $str = shift;

	#�ơ��֥륿���θ��ˤʤ�'|'����'|'��ʸ����򣱹Ԥ��Ĥ˶��ڤ�
	my @listTable = split /\|\n/, $str;
	my $convert;
	my $count=0;
	my $tr_width;
	my %HashData = {};

	foreach my $table_param(@listTable){
		#��������ʸ����˴ޤޤ����Ԥ�<br>���Ѵ�
		$table_param =~ s/\n/<br>/g;
		#�������
		$table_param =~ s/\ //;

		#���ԤΥǡ����򥻥��ʬ��
		my @listTr = split /\|/, $table_param;

		#�ơ��֥�Σ�����
		if( $count == 0 ){
			#�ơ��֥�Σ����ܤΥ���ο������
			$tr_width = $#listTr;

			for(my $listCount=0;$listCount <= $#listTr;$listCount++){
				#�ƥ����Ȥ�����
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
		#�ơ��֥�κǸ�ι�
		}elsif( $count == $#listTable ){
			for(my $listCount=0;$listCount <= $tr_width;$listCount++){
				#�ƥ����Ȥ�����
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
		#�ơ��֥�����
		}else{
			for(my $listCount=0;$listCount <= $tr_width;$listCount++){
				#�ƥ����Ȥ�����
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
#	����	: �Ԥ���Ƭ������Ǥ��뤫�ɤ���Ĵ�٤�
#	return	: 1
#==============================================================================
sub convertBlock {
	my $r_data = shift;
	#�ǡ�����ʣ���ԤȤ��Ʋ��(����ɽ��/m)
	$$r_data =~ s/^((\ .*\n){1,})/&createBlock( $1 )/meg;
	return 1;
}

#==============================================================================
#	����	: �������Ϥ��줿�ǡ�����<pre>�����ǰϤ�
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
#	����	: HTML�إå���ե�����˽񤭹���
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
#	����	: HTML�եå���ե�����˽񤭹���
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

