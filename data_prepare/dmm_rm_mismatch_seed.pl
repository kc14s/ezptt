#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

#	validate_seed_name('yst013', 'YST-13');
my $db_conn = init_db();
$db_conn->do("use dmm");
my $request = $db_conn->prepare('select sn, name, magnet from seed');
$request->execute;
while (my ($sn, $name, $magnet) = $request->fetchrow_array) {
	my $snn = normalize_sn($sn);
	if (validate_seed_name($snn, $name) == 0) {
		print "$snn\t$name\n";
		$db_conn->do("delete from seed where sn = '$sn' and magnet = '$magnet'");
	}
}

