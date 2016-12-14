#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

my $url = 'https://thepiratebay.org/top/500';
$url = 'https://ukpirate.click/top/501';
$url = 'http://pirateunblocker.com/top/501';

my $rank = 1;
my %sns;
my @ranks;
for my $page (0...30) {
	$url = "http://pirateunblocker.com/browse/501/$page/7";
	my $html = get_url($url);
	my @seeds = parse_pb_list_html($html);
	foreach my $seed (@seeds) {
		my ($title, $magnet, $hash, $size, $seeder, $leecher) = @$seed;
		if ($title =~ /([a-zA-Z]+)\-(\d+)/) {
			my $snn = lc($1).$2;
			my $sn = execute_scalar("select sn from video where sn_normalized = '$snn'");
			if (defined($sn) && $sn eq '0') {
				print "no $snn\n";
				next;
			}
			print "$sn $snn $title $magnet\n";
			if (defined($sns{$sn})) {}
			else {
				$sns{$sn} = 0;
				push @ranks, [$sn, $rank];
				++$rank;
			}
			if (execute_scalar("select count(*) from seed where sn = '$sn' and hash = '$hash'") == 0) {
				$db_conn->do("insert into seed(sn, magnet, hash, name, size, hot, source) values('$sn', '$magnet', '$hash', ".$db_conn->quote($title).", $size, ".($seeder * 3 + $leecher).", 2)");
			}
		}
	}
}
if (@ranks >= 50) {
	$db_conn->do('truncate pb_rank');
	for my $pb_rank (@ranks) {
		my ($sn, $rank) = @$pb_rank;
		$db_conn->do("insert into pb_rank(sn, rank) values('$sn', $rank)");
	}
}
