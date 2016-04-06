#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');

if (@ARGV < 1) {
	print "usage: ./delete_topic.pl tid1\n";
	exit;
}
my $db_conn = init_db();
$db_conn->do('use ck101');
for (@ARGV) {
	print "delete from topic where tid = $_\n";
	$db_conn->do("delete from topic where tid = $_");
	$db_conn->do("delete from article where tid = $_");
}
