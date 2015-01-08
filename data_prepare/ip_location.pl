#!/usr/bin/perl -w
use strict;
use DBI;
require('config.pl');
require('lib.pl');

my $db_conn = init_db();
$db_conn->do('truncate ip_china');

open IN, 'curl -s http://ftp.apnic.net/apnic/stats/apnic/delegated-apnic-latest | grep CN | grep ipv4 | grep . | ';
while (<IN>) {
	my @arr = split('\|');
	my @nums = split('\.', $arr[3]);
	my $sum = 0;
	foreach my $num (@nums) {
		$sum *= 256;
		$sum += $num;
	}
	$db_conn->do("insert into ip_china values($sum, ".($sum + $arr[4]).")");
}
close IN;
