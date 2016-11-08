my $language_id = 2;
my %dept_id_2_channel = (
29 => 5,
31 => 6,
43 => 7
);

sub download_video_list {
	my %product_ids;
	my $page_size = 200;
	foreach my $video_list_url (@_) {
		for (my $page = 1; ; ++$page) {
			if (index($video_list_url, 'subdept_products') > 0) {
				$page_size = 20;
			}
			$video_list_url .= "&CountPage=$page&SortBy=1&ShowWhichOne=0&HowManyRecords=$page_size";
			my $video_list_html = get_url($video_list_url);
			my $prev_count = scalar keys %product_ids;
			while ($video_list_html =~ /http:\/\/www\.aventertainments\.com\/product_lists\.aspx\?product_id=(\d+)&languageID=\d+&dept_id=(\d+)/g) {
				if (!defined($product_ids{$1})) {
					download_video($1, $2);
					$product_ids{$1} = 0;
				}
			}
#			last;
			if ((scalar keys %product_ids) - $prev_count < $page_size) {
				last;
			}
			last if ($page_size == 20);
		}
	}
}

sub download_video {
	my ($product_id, $dept_id) = @_;
	if ($dept_id == 43) {
		$language_id = 1;
	}
	else {
		$language_id = 2;
	}
	my $url = "http://www.aventertainments.com/product_lists.aspx?product_id=$product_id&languageID=$language_id&dept_id=$dept_id";
	my $html = get_url($url);
	my ($title, $release_date, $runtime, $company, $sn, $channel, $snn, $release_year, $description, $fav_count, $rating);
	$channel = $dept_id_2_channel{$dept_id};
	$title = $1 if ($html =~ /<meta property="og:title" content="([\d\D]+?)"\s*\/>/);
	if ($html =~ /(\d+)\/(\d+)\/(\d{4})/) {
		$release_date = "$3-$1-$2";
		$release_year = $3;
	}
	if ($html =~ /<div class="title2">この作品のレビュー<\/div>\s*<p>\s*([\d\D]*?)\s*<\/p>/) {
		$description = $1 
	}
	elsif ($html =~ /<div class="title2">Description<\/div>\s*<p>\s*([\d\D]*?)\s*<\/p>/) {
		$description = $1 
	}
	$description =~ s/\x00//g;
	if ($description =~ /^\s/) {
		$description = '';
	}
	$runtime = 0;
	if ($html =~ /収録時間:<\/span>.+?(\d+)\s*(\w+)/) {
		$runtime = $1;
		if (index(lc($2), 'h') == 0) {
			$runtime *= 60;
		}
	}
	elsif ($html =~ /Playing time:<\/span>.+?(\d+)\s*(\w+)/) {
		$runtime = $1;
		if (index(lc($2), 'h') == 0) {
			$runtime *= 60;
		}
	}
	if ($html =~ /スタジオ:<\/span>.+?>([\d\D]+?)<\/a><\/li>/) {
		$company = $1;
	}
	elsif ($html =~ /Studio:<\/span>.+?>([\d\D]+?)<\/a><\/li>/) {
		$company = $1;
	}
	$fav_count = $product_id;
	$fav_count = 0;
	if ($html =~ /商品番号:\s*([\w\-]+)/) {
		$sn = lc($1);
	}
	elsif ($html =~ /Item#:\s*([\w\-]+)/) {
		$sn = lc($1);
	}
	my $sn_trimmed = $sn;
	$sn_trimmed =~ s/\-//g;
	$snn = normalize_sn($sn_trimmed);
	if (!defined($snn) || !defined($title)) {
		print "illegal snn/title $product_id $dept_id\n";
		return;
	}
	$rating = 2;
	if ($html =~ /http:\/\/imgs\.aventertainments\.com\/(\w+)\/bigcover/) {
		if ($1 eq 'new') {
			$rating = 1;
		}
	}
	print "$product_id $dept_id $snn $title, $release_date, $runtime, $company\n";
	if (0 || execute_scalar("select count(*) from video where sn = '$sn'") == 0) {
		$db_conn->do("replace into video(sn, sn_normalized, title, release_date, runtime, company, channel, release_year, fav_count, rating, description) values('$sn', '$snn', ".$db_conn->quote($title).", '$release_date', $runtime, ".$db_conn->quote($company).", $channel, $release_year, $fav_count, $rating, ".$db_conn->quote($description).")");
	}
	my $star_html;
	if ($html =~ /主演女優: <\/span>([\d\D]+?)<\/li>/) {
		$star_html = $1;
	}
	elsif ($html =~ /Starring: <\/span>([\d\D]+?)<\/li>/) {
		$star_html = $1;
	}
	if (defined($star_html)) {
		while ($star_html =~ />\s*([\d\D]+?)<\/a>/g) {
			print "$product_id $dept_id $snn $1\n";
#			$db_conn->do("replace into ave_prod_id_star(product_id, star) values($product_id, '$1')");
			$db_conn->do("replace into ave_sn_star(sn, star) values('$sn', ".$db_conn->quote($1).")");
		}
	}
	get_seeds($sn, $snn, $channel, $db_conn);
}


1;
