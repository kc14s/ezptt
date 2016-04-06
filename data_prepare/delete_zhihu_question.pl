#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('zhihu_lib.pl');

our $db_conn = init_db();
$db_conn->do("use zhihu");
$db_conn->do("set names utf8");

for (@ARGV) {
	my $qid = execute_scalar("select qid from answer where aid = $_");
	$db_conn->do("delete from question where qid = $qid");
	my @aids = execute_vector("select aid from answer where qid = $qid");
	for my $aid (@aids) {
		$db_conn->do("delete from comment where aid = $aid");
		print "delete $qid $aid\n";
	}
#	$db_conn->do("delete from comment where aid in (select aid from answer where qid = $qid)");
	$db_conn->do("delete from answer where qid = $qid");
}
