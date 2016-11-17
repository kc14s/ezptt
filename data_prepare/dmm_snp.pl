#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
my %snp;
my %companies;
my $request = $db_conn->prepare('select sn, sn_normalized, seed_popularity, company from video');
$request->execute;
while (my ($sn, $snn, $seed_popularity, $company) = $request->fetchrow_array) {
	if ($snn =~ /([a-zA-Z]+)/) {
		$snp{$1} += $seed_popularity;
		$companies{$1} = $company if (!defined($companies{$1}));
	}
}
while (my ($snp, $count) = each %snp) {
	print "$snp\t$count\t$companies{$snp}\n";
	$db_conn->do("replace into snp(snp, seed_popularity, company) values('$snp', $count, ".$db_conn->quote($companies{$snp}).")");
}

