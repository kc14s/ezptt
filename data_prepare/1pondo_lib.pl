binmode STDOUT, ':utf8';
my $json = JSON->new->utf8(1);
my $channel = 10;
my $company = decode('utf8', '一本道');

sub parse_1pondo_list {
	my ($list_html, $db_conn) = @_;
	return 0 if (index($list_html, '{') != 0);
	my $list_json = $json->decode($list_html);
	my $rows = $list_json->{'Rows'};
	my $counter = 0;
	foreach my $video (@$rows) {
		my $actor = $video->{'Actor'};
		my $actor_id = $video->{ActorID}->[0];
		my $sn = $video->{MovieID};
		my $snn = "1pondo$sn";
		my $release_date = $video->{Release};
		my $release_year = substr($release_date, 0, 4);
		my $series = $video->{Series};
		my $series_id = $video->{SeriesID};
		my $title = $video->{Title};
		my $desc = $video->{Desc};
		my $runtime = $video->{Duration};
		my $genre_ids = $video->{UC};
		my $genre_texts = $video->{UCNAME};
		my $sample_image_num = 0;
		my $gallery_html = get_url("http://www.1pondo.tv/dyn/ren/movie_galleries/movie_id/$sn.json");
		if (index($gallery_html, '{') == 0) {
			my $gallery_json = $json->decode($gallery_html);
			my $gallery_rows = $gallery_json->{Rows};
			for (; $sample_image_num < @$gallery_rows; ++$sample_image_num) {
				last if ($gallery_rows->[$sample_image_num]->{Protected});
			}
		}
		if (defined($series_id)) {
			$db_conn->do("replace into 1pondo_series(id, name) values($series_id, ".$db_conn->quote($series).")");
		}
		else {
			$series_id = 0;
		}
		if (defined($genre_ids)) {
			for (my $i = 0; $i < @$genre_ids; ++$i) {
				$db_conn->do("replace into 1pondo_genre(id, genre) values($genre_ids->[$i], '$genre_texts->[$i]')");
				$db_conn->do("replace into genre(sn, genre) values('$sn', '$genre_texts->[$i]')");
			}
			$director = join(',', @$genre_ids);
		}
		$db_conn->do("replace into 1pondo_star_info(id, name) values($actor_id, '$actor')");
		$db_conn->do("replace into 1pondo_sn_star(sn, star_id) values('$sn', $actor_id)");
		$title = $db_conn->quote($title);
		$desc = $db_conn->quote($desc);
		print "$sn $actor $release_date $title $runtime\n";
		$runtime = int($runtime / 60);
		if (execute_scalar("select count(*) from video where sn = '$sn'") == 0) {
			$db_conn->do("replace into video(sn, sn_normalized, title, release_date, runtime, director, company, sample_image_num, description, channel, release_year, series_id) values('$sn', '$snn', $title, '$release_date', $runtime, '$director', '$company', $sample_image_num, $desc, $channel, $release_year, $series_id)");
		}
		get_seeds($sn, $snn, $channel, $db_conn);
		++$counter;
	}
	return $counter;
}


1;
