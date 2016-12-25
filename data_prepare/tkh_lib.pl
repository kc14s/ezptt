sub download_tkh_video {
	my $channel = 9;
	my ($sn, $db_conn) = @_;
	my $detail_html = get_url("http://www.tokyo-hot.com/product/$sn/");
	my $title = $1 if ($detail_html =~ /<h2>([\d\D]+?)<\/h2>/);
	my $snn = $1 if ($detail_html =~ /<dt>作品番号<\/dt>\s*<dd>([\w\-]*)<\/dd>/);
	return if (!defined($snn));
	my $star_html = $1 if ($detail_html =~ /<dt>出演者<\/dt>\s*<dd>([\d\D]+?)<\/dd>/);
	while ($star_html =~ /">([\d\D]+?)<\/a>/g) {
		print "$sn $snn $1";
		$db_conn->do("replace into star(sn, star) values('$sn', ".$db_conn->quote($1).")");
	}
	my $release_date = $1 if ($detail_html =~ /<dt>配信開始日<\/dt>\s*<dd>([\d\/]+)<\/dd>/);
	my $release_year = substr($release_date, 0, 4);
	my ($hour, $minute) = ($1, $2) if ($detail_html =~ /<dt>収録時間<\/dt>\s*<dd>(\d+):(\d+):\d+<\/dd>/);
	my $runtime = $hour * 60 + $minute;
	my $genre_html = $1 if ($detail_html =~ /<dt>カテゴリ<\/dt>\s*<dd>([\d\D]+?)<\/dd>/);
	while ($genre_html =~ />([\d\D]+?<\/a>)/g) {
		my $genre = $1;
		$db_conn->do("replace into genre(sn, genre) values('$sn', ".$db_conn->quote($genre).")");
	}
	my $description = '';
	$description = $1 if ($detail_html =~ /<div class="sentence">\s*([\d\D]+?)\s*<\/div>/);
	print "$sn $snn $title $runtime $release_date $release_year\n";
	my @sample_file_names = ();
	while ($detail_html =~ /<a href="http:\/\/my\.cdn\.tokyo\-hot\.com\/media\/$sn\/scap\/([\w\s\-]+)\.jpg/g) {
		push @sample_file_names, $1;
	}
	if (@sample_file_names > 0) {
		$db_conn->do("replace into sample_url(sn, url) values('$sn', '".join("\t", @sample_file_names)."')");
	}
	if (1 || execute_scalar("select count(*) from video where sn = '$sn'") == 0) {
		$db_conn->do("replace into video(sn, sn_normalized, title, runtime, company, description, channel, release_date, release_year, sample_image_num, type) values('$sn', '$snn', ".$db_conn->quote($title).", $runtime, 'Tokyo Hot', ".$db_conn->quote($description).", $channel, '$release_date', $release_year, ".scalar(@sample_file_names).", 3)");
	}
	get_seeds($sn, $snn, $channel, $db_conn);
}


1;
