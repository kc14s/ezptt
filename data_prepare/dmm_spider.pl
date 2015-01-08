#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');

my @boards = (
['av', 'http://www.dmm.co.jp/digital/videoa/-/list/=/limit=120/sort=date/'],
['amateur', 'http://www.dmm.co.jp/digital/videoc/-/list/=/limit=120/sort=date/'],
['anime', 'http://www.dmm.co.jp/digital/anime/-/list/=/limit=120/sort=date/'],
['av', 'http://www.dmm.co.jp/digital/nikkatsu/-/list/=/limit=120/sort=date/']
);
my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
for (my $channel = 1; $channel <= @boards; ++$channel) {
#	next if ($channel == 1);
	my $board = $boards[$channel - 1];
	my ($type, $board_url_template) = @$board;
	for (my $page = 1; ; ++$page) {
		my $board_url = "${board_url_template}page=$page/";
		my $board_html = get_url($board_url);
		my @detail_urls;
		while ($board_html =~ /<a href="(http:\/\/www\.dmm\.co\.jp\/digital\/\w+\/\-\/detail\/=\/cid=\w+\/)">/g) {
			push @detail_urls, $1;
		}
		foreach my $detail_url (@detail_urls) {
			my $detail_html = get_url($detail_url);
			my ($title, $release_date, $runtime, $director, $series, $company, $sn, $fav_count, $rating, $desc, $sample_image_num);
			#$title = $1 if ($detail_html =~ /<meta property="og:title" content="([\d\D]+?)"\s*\/>/);
			$title = $1 if ($detail_html =~ /<h1 id="title" class="item fn">([\d\D]+?)<\/h1><\/div>/);
			$release_date = $1 if ($detail_html =~ /商品発売日：<\/td>\s*<td>\s*([\d\/]+)/);
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
			if ($detail_html =~ /出演者：<\/td>([\d\D]+?)<\/td>/) {
				my $span = $1;
				while ($span =~ /article=actress\/id=\d+\/">([\d\D]+?)<\/a>/g) {
					push @stars, $1;
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
			print "$title, $release_date, $runtime, $director, $series, $company, $sn, $fav_count, $rating, $desc, $sample_image_num\n";
			print "genres ".join(',', @genres)."\n";
			print "stars ".join(', ', @stars)."\n";
			if (execute_scalar("select count(*) from video where sn = '$sn'") == 0) {
				$db_conn->do("insert into video(title, release_date, runtime, director, series, company, sn, fav_count, rating, description, sample_image_num, channel) values('$title', '$release_date', $runtime, '$director', '$series', '$company', '$sn', $fav_count, $rating, ".$db_conn->quote($desc).", $sample_image_num, $channel)");
#				$db_conn->do("delete from genre where sn = '$sn'");
				foreach my $genre (@genres) {
					$db_conn->do("replace into genre(sn, genre) values('$sn', '$genre')");
				}
#				$db_conn->do("delete from star where sn = '$sn'");
				foreach my $star (@stars) {
					$db_conn->do("replace into star(sn, star) values('$sn', '$star')");
				}
			}
		}
		last if (@detail_urls < 120);
=cutklfa
=cut
	}
}
