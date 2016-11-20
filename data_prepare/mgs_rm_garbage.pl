#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
my $request = $db_conn->prepare('select sn, sn_normalized from video where channel = 8');
$request->execute;
while (my ($sn, $snn) = $request->fetchrow_array) {
	if (execute_scalar("select count(*) from video where sn_normalized = '$snn'") > 1) {
		my $other_sn = execute_scalar("select sn from video where sn_normalized = '$snn' and sn <> '$sn'");
		$db_conn->do("delete from video where sn = '$sn'");
		$db_conn->do("delete from star where sn = '$sn'");
		$db_conn->do("delete from seed where sn = '$sn'");
		$db_conn->do("delete from genre where sn = '$sn'");
		print "delete $sn\n";
		$db_conn->do("update seed set sn = '$other_sn' where sn = '$sn'");
		$db_conn->do("update emule set sn = '$other_sn' where sn = '$sn'");
	}
}

