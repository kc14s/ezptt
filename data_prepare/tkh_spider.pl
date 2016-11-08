#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');
require('tkh_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

	for (my $page = 1; 0 || $page <= 3; ++$page) {
		my $url = "http://www.tokyo-hot.com/product/?page=$page&order=published_at";
		#my $url = "http://www.tokyo-hot.com/product/?page=$page&order=downloads";
		my $list_html = get_url($url);
		my $count = 0;
		while ($list_html =~ /<a href="\/product\/([\w\-]+)\//g) {
			++$count;
			my $sn = $1;
			download_tkh_video($sn, $db_conn);
		}
		last if ($count == 0);
	}
