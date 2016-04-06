#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');

if (@ARGV < 1) {
	print "usage: ./delete_user.pl user_name\n";
	exit;
}
my $db_conn = init_db();
$db_conn->do("use $ENV{'database_ck101'}");
my @tids = execute_column("select distinct(tid) from topic where author = '$ARGV[0]'");
$db_conn->do("delete from article where tid in (".join(',', @tids).")");
$db_conn->do("delete from  topic where author = '$ARGV[0]'");
