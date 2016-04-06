#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
my $request = $db_conn->prepare('select sn, size_text, magnet from seed');
$request->execute;
while (my ($sn, $size_text, $magnet) = $request->fetchrow_array) {
	my $size = 0;
	if ($size_text =~ /([\d\.]+)\s*(\w+)/) {
		$size = $1;
		if (uc($2) eq 'GB') {
			$size *= 1024 * 1024 * 1024;
		}
		elsif (uc($2) eq 'MB') {
			$size *= 1024 * 1024;
		}
		elsif (uc($2) eq 'KB') {
			$size *= 1024;
		}
		$size = int($size + 0.5);
		#print "$size_text\t$size\n";
	}
	if ($size > 0) {
		$db_conn->do("update seed set size = $size where sn = '$sn' and magnet = '$magnet'");
	}
}

