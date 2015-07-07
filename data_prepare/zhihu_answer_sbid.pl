#!/usr/bin/perl -w
use strict;
use DBI;
require('lib.pl');

my $db_conn = DBI->connect("DBI:mysql:database=zhihu;host=localhost", 'root', '');
my $sql = 'select aid, question.sbid from answer join question on answer.qid = question.qid';
my $request = $db_conn->prepare($sql);
$request->execute;
while (my ($aid, $sbid) = $request->fetchrow_array) {
	$db_conn->do("update answer set sbid = $sbid where aid = $aid");
}
