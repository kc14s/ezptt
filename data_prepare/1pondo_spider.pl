#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');
require('1pondo_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

for (my $page = 50; 0 || $page <= 2 * 50; $page += 50) {
	my $url = "http://www.1pondo.tv/dyn/ren/movie_lists/list_newest_$page.json";
#	$url = "http://www.1pondo.tv/dyn/ren/movie_lists/list_bob_".($page - 50).".json";
	my $list_json = get_url($url);
	my $count = parse_1pondo_list($list_json, $db_conn);
	last if ($count == 0);
}
