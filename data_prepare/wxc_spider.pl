#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('wxc_lib.pl');

my $db_conn = init_db();
$db_conn->do("use wxc");

my $boards = get_wxc_boards($db_conn);
for my $board (@$boards) {
	get_wxc_topic($board);
}
