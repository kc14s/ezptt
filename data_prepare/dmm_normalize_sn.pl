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
	$snn =~ s/^h_//; 
	$snn =~ s/_[\d]$//;
	$snn =~ s/^\d+//;
	$snn =~ s/(\d)[a-z]{1,2}$/$1/;
	$snn =~ s/0+([1-9])/$1/g;
	$db_conn->do("update video set sn_normalized = '$snn' where sn = '$sn'");
}

