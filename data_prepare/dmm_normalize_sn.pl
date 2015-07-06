#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
my $request = $db_conn->prepare('select sn from video');
$request->execute;
while (my ($sn) = $request->fetchrow_array) {
	my $snn = normalize_sn($sn);
	$db_conn->do("update video set sn_normalized = '$snn' where sn = '$sn'");
}

