#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('jav123_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

my $url = 'http://jav123.com/category/japan/page';
for (my $page = 1; $page < 100; ++$page) {
	my $list_html;
	if ($page == 1) {
		$list_html = get_url('http://jav123.com/category/japan');
	}
	else {
		$list_html = get_url("$url/$page");
	}
	my $vids = extract_vids($list_html);
	my $download_count = download_vids($vids);
	last if ($download_count == 0);
}
