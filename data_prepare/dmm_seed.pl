#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
my $request = $db_conn->prepare('select sn, sn_normalized, channel from video where 1 = 0 or channel = 3 or channel = 6 order by fav_count desc');
$request->execute;
while (my ($sn, $snn, $channel) = $request->fetchrow_array) {
	if (0 || execute_scalar("select count(*) from seed where sn = '$sn'") == 0) {
		get_seeds($sn, $snn, $channel, $db_conn);
	}
	if (0 && execute_scalar("select count(*) from emule where sn = '$sn'") == 0) {
		get_emule($sn, $snn, $channel, $db_conn);
	}
}

