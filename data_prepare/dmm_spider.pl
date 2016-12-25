#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my %channels = (
1 => 'dmm av',
2 => 'dmm amateur',
3 => 'dmm cartoon',
4 => 'dmm av',
5 => 'ave unsensored',
6 => 'ave cartoon',
7 => 'ave west',
8 => 'mgs amateur',
9 => 'tkh unsensored',
10 => '1pondo unsensored',
11 => '5xsq',
12 => '',
13 => ''
);

my %types = (
1 => 'av',
2 => 'amateur',
3 => 'unsensored',
4 => 'cartoon',
5 => 'west',
6 => 'homebrew',
7 => '',
8 => '',
9 => ''
);

my @boards = (
['av', 'http://www.dmm.co.jp/digital/videoa/-/list/=/limit=120/sort=date/'],
['amateur', 'http://www.dmm.co.jp/digital/videoc/-/list/=/limit=120/sort=date/'],
['anime', 'http://www.dmm.co.jp/digital/anime/-/list/=/limit=120/sort=date/'],
['av', 'http://www.dmm.co.jp/digital/nikkatsu/-/list/=/limit=120/sort=date/']
);
my $db_conn = init_db('dmm');
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
for (my $channel = 1; $channel <= @boards; ++$channel) {
#	next if ($channel == 1);
	my $board = $boards[$channel - 1];
	my ($type, $board_url_template) = @$board;
	for (my $page = 1; ; ++$page) {
		my $board_url = "${board_url_template}page=$page/";
		my $board_html = get_url($board_url);
		#print $board_html;
		my @detail_urls;
		while ($board_html =~ /<a href="(http:\/\/www\.dmm\.co\.jp\/digital\/\w+\/\-\/detail\/=\/cid=\w+\/)/g) {
			push @detail_urls, $1;
#			print "$1\n";
		}
		foreach my $detail_url (@detail_urls) {
			my $detail_html = get_url($detail_url);
			my ($title, $release_date, $runtime, $director, $series, $company, $sn, $fav_count, $rating, $desc, $sample_image_num);
			#$title = $1 if ($detail_html =~ /<meta property="og:title" content="([\d\D]+?)"\s*\/>/);
			$title = $1 if ($detail_html =~ /<h1 id="title" class="item fn">([\d\D]+?)<\/h1><\/div>/);
			$release_date = $1 if ($detail_html =~ /発売日：<\/td>\s*<td>\s*([\d\/]+)/);
			if (!defined($release_date)) {
				$release_date = $1 if ($detail_html =~ /配信開始日：<\/td>\s*<td>\s*([\d\/]+)/);
			}
			if (!defined($release_date)) {
				$release_date = $1 if ($detail_html =~ /配信期間：<\/td>\s*<td>\s*([\d\/]+)/);
			}
			if (!defined($release_date)) {
				print "$detail_url release date not found\n";
			}
			$runtime = $1 if ($detail_html =~ />収録時間：<\/td>\s*<td>(\d+)/);
			if ($channel == 2) {
				$director = '';
				$series = '';
			}
			elsif ($channel == 3) {
				$director = '';
			}
			else {
				$director = $1 if ($detail_html =~ /監督：<\/td>\s*<td>([\d\D]+?)<\/td>/);
				if (!defined($director)) {
					print "$detail_url director not found\n";
				}
				$director =~ s/<.+>//g;
				$director =~ s/\-+//;
				$series = $1 if ($detail_html =~ /シリーズ：<\/td>\s*<td>([\d\D]+?)<\/td>/);
				if (!defined($series)) {
					print "$detail_url series not found\n";
				}
				$series =~ s/<.+>//g;
				$series =~ s/\-+//;
			}
			$company = $1 if ($detail_html =~ /article=maker\/id=\d+\/">([^<]+?)<\/a><\/td>/);
			if (!defined($company)) {
				$company = $1 if ($detail_html =~ /レーベル：<\/td>\s*<td><a href="\/digital\/\w+\/\-\/list\/=\/article=label\/id=\d+\/">([^<]*?)<\/a>/);
			}
			if (!defined($company)) {
				print "$detail_url company not found\n";
			}
			$sn = $1 if ($detail_html =~ /品番：<\/td>\s*<td>([\d\D]+?)<\/td>/);
			$fav_count = $1 if ($detail_html =~ /お気に入り登録数<span class="tx-count"><span>(\d+)<\/span>/);
			$rating = $1 if ($detail_html =~ /p\.dmm\.co\.jp\/p\/ms\/review\/([_\d]+)\.gif/);
			if (!defined($rating)) {
				print "$detail_url rating not found\n";
			}
			$rating =~ s/_/./;
			$rating *= 10;
			$desc = $1 if ($detail_html =~ /<div class="mg-b20 lh4">\s*([\d\D]+?)\s*</);
			my @genres;
			if ($detail_html =~ /ジャンル：<\/td>\s*<td>\s*([\d\D]+?)<\/td>/) {
				my $tags = $1;
				while ($tags =~ /article=keyword\/id=\d+\/">([\d\D]+?)<\/a>/g) {
					push @genres, $1;
				}
			}
			my @stars;
			my %stars;
			if ($detail_html =~ /出演者：<\/td>([\d\D]+?)<\/td>/) {
				my $span = $1;
				while ($span =~ /article=actress\/id=(\d+)\/">([\d\D]+?)<\/a>/g) {
					push @stars, $2;
					$stars{$2} = $1;
					if (execute_scalar("select count(*) from star_info where id = $1") == 0) {
						$db_conn->do("insert into star_info(id, name) values($1, '$2')");
					}
				}
			}
#			while ($detail_html =~ /<a href="\/digital\/videoa\/\-\/list\/=\/article=actress\/id=\d+\/">([\d\D]+?)<\/a>/g) {
#				push @stars, $1;
#			}
			if (@stars == 0) {
				if ($detail_html =~ /名前：<\/td>\s*<td>\s*([^<]+?)</) {
					my $star = $1;
					my $pos = index($star, '(');
					if ($pos > 0) {
						$star = substr($star, 0, $pos);
					}
					push @stars, $star;
				}
			}
			$sample_image_num = 0;
			while ($detail_html =~ /sample-image(\d+)/g) {
				$sample_image_num = $1 if ($1 > $sample_image_num);
			}
			my $snn = normalize_sn($sn);
			print "$title, $release_date, $runtime, $director, $series, $company, $sn, $snn, $fav_count, $rating, $desc, $sample_image_num\n";
			next if (!defined($title) || !defined($sn));
			print "genres ".join(',', @genres)."\n";
			print "stars ".join(', ', @stars)."\n";
			if (execute_scalar("select count(*) from video where sn = '$sn'") == 0) {
				my $type = channel_to_type($channel);
				$db_conn->do("replace into video(title, release_date, runtime, director, series, company, sn, sn_normalized, fav_count, rating, description, sample_image_num, channel, type) values('$title', '$release_date', $runtime, '$director', '$series', '$company', '$sn', '$snn', $fav_count, $rating, ".$db_conn->quote($desc).", $sample_image_num, $channel, $type)");
#				$db_conn->do("delete from genre where sn = '$sn'");
				foreach my $genre (@genres) {
					$db_conn->do("replace into genre(sn, genre) values('$sn', '$genre')");
				}
#				$db_conn->do("delete from star where sn = '$sn'");
				#foreach my $star (@stars) {
			}
			else {
				$db_conn->do("update video set title = ".$db_conn->quote($title).", release_date = '$release_date', runtime = $runtime, director = '$director', series = '$series', company = '$company', description = ".$db_conn->quote($desc).", sample_image_num = $sample_image_num, channel = $channel, fav_count = $fav_count, rating = $rating where sn = '$sn'");
			}
			$db_conn->do("delete from star where sn = '$sn'");
			while (my ($star, $star_id) = each %stars) {
				$db_conn->do("replace into star(sn, star, star_id) values('$sn', '$star', $star_id)");
			}
			if (1 && execute_scalar("select count(*) from seed where sn = '$sn'") == 0) {
				get_seeds($sn, $snn, $channel, $db_conn);
			}
			if (0 && execute_scalar("select count(*) from emule where sn = '$sn'") == 0) {
				get_emule($sn, $snn, $channel, $db_conn);
			}
			if (execute_scalar("select count(*) from sample_url where sn = 'v_$sn'") == 0) {
				my $detail_html = get_url("http://www.r18.com/videos/vod/movies/detail/Intense-Cum-Hina-Kinami/id=$sn/");
				if ($detail_html =~ /fid=(\w+)/) {
					$db_conn->do("replace into sample_url(sn, url) values('v_$sn', '$1')");
					print "preview video $sn $1\n";
				}
			}
			next;
			my %recommend_params;
			$recommend_params{target_content_id} = $1 if ($detail_html =~ /target_content_id\s*:\s*'(\w+)'/);
			$recommend_params{target_content_shoptable} = $1 if ($detail_html =~ /target_content_shoptable\s*:\s*'([\w\\]+)'/);
			$recommend_params{target_content_shoptable} =~ s/\\u005/_/g;
			$recommend_params{from_GET} = $1 if ($detail_html =~ /from_GET\s*:\s*'([\w\\]+)'/);
			$recommend_params{from_GET} =~ s/\\u005/_/g;
			#print "recommend: $target_content_id $target_content_shoptable\n";
			if (scalar keys %recommend_params != 3) {
				print "illegal recommend_params $sn\n";
				next;
			}
			next;
		}
		last if (@detail_urls < 120);
=cutklfa
=cut
	}
}
