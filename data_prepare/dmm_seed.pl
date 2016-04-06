#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");
my $request = $db_conn->prepare('select sn, sn_normalized from video');
$request->execute;
while (my ($sn, $snn) = $request->fetchrow_array) {
	get_seeds($sn, $snn, $db_conn);
}

