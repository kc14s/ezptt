#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');
require('db_lib.pl');

our $db_conn = init_db();
$db_conn->do('use douban');

my @board_groups = ('', '/culture', '/travel', '/ent', '/fashion', '/life', '/tech');
foreach my $board_group (@board_groups) {
	for (my $start = 0; $start < 300; $start += 30) {
		my $url = "https://www.douban.com/group/explore${board_group}?start=$start";
		my $list_html = get_url($url);
		discover_boards($list_html, 1);
	}
	
}
