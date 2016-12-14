#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
our $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

my $req = $db_conn->prepare("select id from ezptt.topic order by id");
$req->execute;
my $count = 0;
while (my ($id) = $req->fetchrow_array) {
	++$count;
	next if ($id == $count);
	print "update $id to $count\n";
	$db_conn->do("update video set id = $count where id = $id");
}
