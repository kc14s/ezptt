#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('ck_lib.pl');

my $db_conn = init_db();
$db_conn->do("use $ENV{'database_ck101'}");
#my $boards = get_ck_boards();
my $boards = get_ck_boards_from_db();
foreach my $bid (keys %$boards) {
	get_ck_topics($bid);
}
