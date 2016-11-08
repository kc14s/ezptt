#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
my $request = $db_conn->prepare('select sn from video where channel <= 4');
$request->execute;
while (my ($sn) = $request->fetchrow_array) {
	next if (execute_scalar("select count(*) from sample_url where sn = 'v_$sn'") > 0);
	my $detail_html = get_url("http://www.r18.com/videos/vod/movies/detail/Intense-Cum-Hina-Kinami/id=$sn/");
	if ($detail_html =~ /fid=(\w+)/) {
		$db_conn->do("replace into sample_url(sn, url) values('v_$sn', '$1')");
		print "$sn $1\n";
	}
}

