#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');

my @boards = (
['bookmark_rank', 'http://www.dmm.co.jp/digital/videoa/-/list/=/sort=bookmark_desc/'],
['rank', 'http://www.dmm.co.jp/digital/videoa/-/list/=/limit=120/sort=ranking/']
);
my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
for (my $channel = 1; $channel <= @boards; ++$channel) {
#	next if ($channel == 1);
	my $board = $boards[$channel - 1];
	my ($field, $board_url_template) = @$board;
	my @sns;
	for (my $page = 1; ; ++$page) {
		my $board_url = "${board_url_template}page=$page/";
		my $board_html = get_url($board_url);
		my $page_size = 0;
		while ($board_html =~ /<a href="http:\/\/www\.dmm\.co\.jp\/digital\/\w+\/\-\/detail\/=\/cid=(\w+)\//g) {
			push @sns, $1;
			++$page_size;
		}
		last if ($page_size < 120);
	}
	if (@sns < 10000) {
		print "video too few ".(scalar @sns)."\n";
		next;
	}
	my $diff_count = 0;
	for (my $rank = 0; $rank < @sns; ++$rank) {
		if ($rank != execute_scalar("select $field from video where sn = '$sns[$rank]'")) {
			++$diff_count;
		}
	}
	if ($diff_count * 3 > @sns) {
#		my $period = execute_scalar("select max(period) from rank") + 1;
		$db_conn->do("update video set $field = 1000000");
		for (my $rank = 0; $rank < @sns; ++$rank) {
			$db_conn->do("update video set $field = $rank where sn = '$sns[$rank]'");
#			$db_conn->do("insert into rank(period, sn, rank) values($period, '$sns[$rank]', $rank)") if ($field eq 'rank');
		}
	}
	else {
		print "diff too few $diff_count\n";
	}
}
