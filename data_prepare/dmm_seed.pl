#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
my $request = $db_conn->prepare('select sn, sn_normalized from video order by seed_popularity desc');
$request->execute;
while (my ($sn, $snn) = $request->fetchrow_array) {
	if (0 && execute_scalar("select count(*) from seed where sn = '$sn'") == 0) {
		get_seeds($sn, $snn, $db_conn);
	}
	if (execute_scalar("select count(*) from emule where sn = '$sn'") == 0) {
		get_emule($sn, $snn, $db_conn);
	}
}

