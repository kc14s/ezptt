#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
my $request = $db_conn->prepare('select sn from video');
$request->execute;
while (my ($sn) = $request->fetchrow_array) {
	my $snn = $sn;
	if ($sn =~ /([a-z]+)0*(\d+)$/) {
		$snn = "$1 $2";
	}
	elsif ($sn =~ /([a-z]+)0*(\d+)[a-z]+$/) {
		$snn = "$1 $2";
	}
	$db_conn->do("update video set sn_normalized = '$snn' where sn = '$sn'");
}

