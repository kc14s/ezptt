#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
my $series_rank = 0;
my %series_ids;
my $series_continue = 1;
for (my $page = 1; ; ++$page) {
	my $url = "http://www.dmm.co.jp/digital/videoa/-/series/=/sort=ranking/page=$page/";
	my $series_list_html = get_url($url);
	my $series_found = 0;
	while ($series_list_html =~ /<a href="http:\/\/www\.dmm\.co\.jp\/digital\/videoa\/\-\/list\/=\/article=series\/id=(\d+)\/">([\d\D]+?)<\/a>/g) {
		my ($series_id, $series_name) = ($1, $2);
		if (defined($series_ids{$series_id})) {
			$series_continue = 0;
			last;
		}
		next if (index($series_name, '<img') == 0);
		$series_ids{$series_id} = 0;
		$db_conn->do("replace into series(id, name, rank) values($series_id, '$series_name', $series_rank)");
		++$series_rank;
		++$series_found;
		my $video_continue = 1;
		my %sns;
		for (my $series_page = 1; ; ++$series_page) {
			my $list_html = get_url("http://www.dmm.co.jp/digital/videoa/-/list/=/article=series/id=$series_id/page=$series_page/");
			my $video_found = 0;
			while ($list_html =~ /http:\/\/www\.dmm\.co\.jp\/digital\/videoa\/\-\/detail\/=\/cid=(\w+)\//g) {
				if (defined($sns{$1})) {
					$video_continue = 0;
					last;
				}
				$sns{$1} = 0;
				++$video_found;
				my $detail_html = get_url("http://www.dmm.co.jp/digital/videoa/-/detail/=/cid=$1/");
				my $sn = $1 if ($detail_html =~ /品番：<\/td>\s*<td>([\d\D]+?)<\/td>/);
				$db_conn->do("update video set series_id = $series_id where sn = '$sn'");
			}
			last if ($video_found != 120);
			last if ($video_continue == 0);
		}
	}
	last if ($series_found != 20);
	last if ($series_continue == 0);
}
