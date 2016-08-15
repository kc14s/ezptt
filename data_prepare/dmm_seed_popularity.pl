#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("update star_info set seed_popularity = 0");
my $request = $db_conn->prepare('select sn, fav_count, to_days(now()) - to_days(release_date) from video');
$request->execute;
while (my ($sn, $fav_count, $days) = $request->fetchrow_array) {
	if (0 && $fav_count < 100) {
		$db_conn->do("update video set seed_popularity = 0 where sn = '$sn'");
	}
	else {
		my $popularity = 0;
		if ($sn =~ /^rs\d+$/) {}
		elsif ($sn =~ /^\d+_\d+$/) {}
#		elsif ($sn ne '118jbs00023' && execute_scalar("select count(*) from star where sn = '$sn'") == 0) {}
		else {
			$popularity = execute_scalar("select sum(hot) from (select hot from seed where sn = '$sn' order by hot desc limit 1, 5) as t");
			#$popularity = execute_scalar("select sum(hot) from (select hot from seed where sn = '$sn' order by hot desc limit 5) as t");
		}
		#$db_conn->do("update star_info set seed_popularity = seed_popularity + $popularity where id in (select star_id from star where sn = '$sn')");
		if ($popularity > 0) {
#			my @star_ids = execute_column("select star_id from star where sn = '$sn'");
#			if (@star_ids == 1) {
#				$db_conn->do("update star_info set seed_popularity = seed_popularity + $popularity where id = $star_ids[0]");
#			}
			$db_conn->do("update video set seed_popularity = $popularity where sn = '$sn'");
		}
		print "$sn	$popularity\n";
	}
}

