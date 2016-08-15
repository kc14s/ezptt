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
for (@ARGV) {
	my $id = $1 if (/(\d+)/);
	print "delete from topic where tid1 = $id\n";
	$db_conn->do("delete from topic where tid1 = $id");
	$db_conn->do("delete from reply where tid1 = $id");
	my $request = $db_conn->prepare("select md5, ext_name from attachment where tid1 = $id");
	$request->execute();
	while (my ($md5, $ext_name) = $request->fetchrow_array) {
		`rm ../front/att/$md5.$ext_name`;
	}
	$db_conn->do("delete from attachment where tid1 = $id");
}
