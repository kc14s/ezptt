sub download_mgs_video {
	my ($sn, $db_conn) = @_;
	my $url = "http://www.mgstage.com/product/product_detail/$sn/";
	my $html = get_url($url);
	my $title = $1 if ($html =~ /MGS動画：「([\d\D]+?)」/);
	my $fav_count = 0;
	$fav_count = $1 if ($html =~ /お気に入り登録数　(\d+)/);
#	print "$sn fav_count $fav_count\n";
	my ($company, $company_en);
	if ($html =~ /<th>メーカー：<\/th>\s*<td>\s*<a .+?image_word_ids\[\]=([\w\-]+)">\s*([\d\D]+?)\s*<\/a>/) {
		($company_en, $company) = ($1, $2);
		print "mgs company\t$company_en $company\n";
		$db_conn->do("replace into mgs_company(company, company_en) values(".$db_conn->quote($company).", ".$db_conn->quote($company_en).")");
	}
	my $runtime = 0;
	$runtime = $1 if ($html =~ /<th>収録時間：<\/th>\s*<td>(\d+)\s*min<\/td>/);
	my $release_date = $1 if ($html =~ /<th>配信開始日：<\/th>\s*<td>([\d\-]+)<\/td>/);
	my $series = '';
	$series = $1 if ($html =~ /<th>シリーズ：<\/th>\s*<td>\s*<a .+>\s*([\d\D]+?)\s*<\/a>/);
	my $rating = 0;
	$rating = $1 if ($html =~ /<span class="star_[\d_]+"><\/span>([\d\.]+)/);
	$rating *= 10;
	if ($html =~ /<th>出演：<\/th>([\d\D]+?)<\/tr>/) {
		my $actress_html = $1;
		while ($actress_html =~ /">\s*([\d\D]+?)\s*<\/a>/g) {
			$db_conn->do("replace into star(sn, star) values('$sn', '$1')");
		}
	}
	if ($html =~ /<th>ジャンル：<\/th>([\d\D]+?)<\/tr>/) {
		my $genre_html = $1;
		while ($genre_html =~ /">\s*([\d\D]+?)\s*<\/a>/g) {
			next if (index($1, '発売') >= 0);
			next if (index($1, '配信') >= 0);
			$db_conn->do("replace into genre(sn, genre) values('$sn', '$1')");
		}
	}
	my $sample_image_num = 0;
	while ($html =~ /cap_e_(\d+)/g) {
		++$sample_image_num;
	}
#	print "$sn sample_image_num $sample_image_num\n";
	$sn = lc($sn);
	my $snn = normalize_sn($sn);
	print "video $sn $snn $title $fav_count $company $runtime $release_date $series $rating\n";
	my $release_year = substr($release_date, 0, 4);
	my $type = 2;
	if (index($sn, 'chn') >= 0 || index($sn, 'bgn') >= 0 || index($sn, 'abp') >= 0 || index($sn, 'mdtm') >= 0 || index($sn, 'fsgd') >= 0 || index($sn, 'hiz') >= 0 || index($sn, 'sdmu') >= 0 || index($sn, 'mdb') >= 0 || index($sn, 'gvg') >= 0 || index($sn, 'hodv') >= 0 || index($sn, 'hfd') >= 0 || index($sn, 'jksr') >= 0) {
		$type = 1;
	}
	if (execute_scalar("select count(*) from video where sn = '$sn'") == 0) {
		$db_conn->do("insert into video(sn, sn_normalized, title, fav_count, company, runtime, release_date, release_year, series, rating, channel, sample_image_num, type) values('$sn', '$snn', ".$db_conn->quote($title).", $fav_count, ".$db_conn->quote($company).", $runtime, '$release_date', '$release_year', ".$db_conn->quote($series).", $rating, 8, $sample_image_num, $type)");
	}
	else {
		$db_conn->do("replace into video(sn, sn_normalized, title, fav_count, company, runtime, release_date, release_year, series, rating, channel, sample_image_num, type) values('$sn', '$snn', ".$db_conn->quote($title).", $fav_count, ".$db_conn->quote($company).", $runtime, '$release_date', '$release_year', ".$db_conn->quote($series).", $rating, 8, $sample_image_num, $type)");
#		$db_conn->do("update video set fav_count = $fav_count, rating = $rating where sn = '$sn'");
	}
	get_seeds($sn, $snn, 8, $db_conn);
}

1;
