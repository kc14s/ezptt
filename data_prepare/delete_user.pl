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
for (@ARGV) {
	my $request = $db_conn->prepare("select tid1, tid2 from topic where author = '$_'");
	$request->execute;
	while (my ($tid1, $tid2) = $request->fetchrow_array) {
		$db_conn->do("delete from reply where tid1 = '$tid1' and tid2 = '$tid2'");
		my $req = $db_conn->prepare("select md5, ext_name from attachment where tid1 = $tid1");
		$req->execute();
		while (my ($md5, $ext_name) = $req->fetchrow_array) {
			`rm ../front/att/$md5.$ext_name`;
		}
	}
	$db_conn->do("delete from topic where author = '$_'");
	$db_conn->do("replace into blocked_user values('$_')");
}
