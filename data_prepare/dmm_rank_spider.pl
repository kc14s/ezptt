#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');

my @boards = (
['av', 'http://www.dmm.co.jp/digital/videoa/-/list/=/limit=120/sort=rate/']
);
my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
my $rank = 0;
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
			my $sn = $1 if ($detail_html =~ /品番：<\/td>\s*<td>([\d\D]+?)<\/td>/);
			next if (!defined($sn));
			$db_conn->do("update video set rank = $rank where sn = '$sn'");
			print "$sn\t$rank\n";
			++$rank;
		}
		last if (@detail_urls < 120);
	}
}
