#!/usr/bin/perl -w
use strict;
use DBI;

require('config.pl');
require('lib.pl');
require('dmm_lib.pl');

my $db_conn = init_db();
$db_conn->do("use dmm");
$db_conn->do("set names utf8");

my $max_id = execute_scalar("select max(id) from video");
my $req = $db_conn->prepare("select sn, release_date from video where id < ".rand($max_id)." order by id desc limit 50000");
$req->execute;
print '<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
while (my ($sn, $release_date) = $req->fetchrow_array) {
	my $url = "https://www.jav321.com/video/$sn";
	print "<url><loc>$url</loc><lastmod>$release_date</lastmod><changefreq>never</changefreq></url>\n";
}
print '</urlset>';
