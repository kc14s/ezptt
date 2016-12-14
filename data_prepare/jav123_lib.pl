use strict;

sub extract_vids {
	my $list_html = shift;
	my %vids;
	my (@vids, @pvs);
	while ($list_html =~ /http:\/\/jav123\.com\/(\d+)/g) {
		push @vids, $1;
	}
	while ($list_html =~ /([\d,]+) views/g) {
		my $pv = $1;
		$pv =~ s/,//;
		push @pvs, $pv;
	}
	for (my $i = 0; $i < @pvs; ++$i) {
		$vids{$vids[$i * 2]} = $pvs[$i];
	}
	return \%vids;
}

sub download_vids {
	my $vids = shift;
	my $download_count = 0;
	my $db = $ENV{'db_conn'};
	while (my ($vid, $pv) = each %$vids) {
		my $url = "http://jav123.com/$vid";
		my $html = get_url($url);
		my $title = $1 if ($html =~ /<title>([\d\D]+?)<\/title>/);
		if (!defined($title)) {
			print "title not found $url\n";
			next;
		}
		my $snn = '';
		if ($title =~ /([A-Za-z]+)\-(\d+)/) {
			$snn = sprintf("%s%03d", lc($1), $2);
		}
		elsif ($title =~ /(MKD)\-S(\d+)/) {
			$snn = sprintf("%s%03d", lc($1), $2);
		}
		elsif ($title =~ /(\d+_\d+)/) {
			$snn = "1pondo$1";
		}
		elsif ($title =~ /(k\d{4})/) {
			$snn = $1;
		}
		elsif ($title =~ /(n\d{4})/) {
			$snn = $1;
		}
		elsif ($title =~ /(mywife|gana) (\d+)/) {
			$snn = sprintf("%s%03d", lc($1), $2);
		}
		else {
			print "snn not found $title $url\n";
			next;
		}
		if (index($snn, 'cute') == 0) {
			$snn = "s$snn";
		}
		if (execute_scalar("select count(*) from video where sn_normalized = '$snn'") == 0) {
			print "video snn does not exist $snn $title $url\n";
			next;
		}
		if (execute_scalar("select count(*) from sixav where snn = '$snn'") > 0) {
			next;
		}
		my $video_id = $1 if ($html =~ /videoid=(\w+)/);
		print "$snn\t$video_id\t$pv\n";
		my $google_url = '';
		$html = get_url("https://sixav.com/video.php?videoid=$video_id");
		if ($html =~ /file: "(.+?)"/) {
			$google_url = $1;
		}
		$db->do("replace into sixav(snn, video_id, pv, google_url) values('$snn', '$video_id', $pv, '$google_url')");
		++$download_count
	}
	return $download_count;
}

1;
