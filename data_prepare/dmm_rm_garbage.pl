#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
my $request = $db_conn->prepare('select sn, name, magnet from seed');
$request->execute;
while (my ($sn, $name, $magnet) = $request->fetchrow_array) {
	my @matches = $name =~ /\-/g;
	if (scalar @matches >= 5) {
		print "$name\n";
#		$db_conn->do("delete from seed where sn = '$sn' and magnet = '$magnet'");
	}
}

